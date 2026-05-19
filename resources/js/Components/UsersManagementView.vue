<template>
    <div class="users-container">
        <div class="users-header">
            <div>
                <h1 class="users-title">Gestión de usuarios.</h1>
                <p class="users-subtitle">Administrar accesos y perfiles</p>
            </div>
            <button @click="openCreateModal" class="btn-new-user">+ Nuevo usuario</button>
        </div>

        <!-- Users Table -->
        <div v-if="loading" class="loading-state">Cargando usuarios...</div>
        <div v-else class="users-table">
            <div class="users-table-header">
                <span>Nombre</span>
                <span>Usuario</span>
                <span>Contraseña</span>
                <span>Perfil</span>
                <span></span>
            </div>
            <div v-for="user in users" :key="user.id" class="user-row">
                <div class="user-info">
                    <div class="user-avatar" :style="{ background: getUserColor(user.perfil) }">
                        {{ user.initials }}
                    </div>
                    <div>
                        <div class="user-name">{{ user.name }}</div>
                        <div class="user-role">{{ user.role }}</div>
                    </div>
                </div>
                <span class="user-email">{{ user.email }}</span>
                <span class="user-password">••••••••</span>
                <span>
                    <select 
                        v-model="user.role" 
                        @change="updateUserRole(user)"
                        class="role-select"
                    >
                        <option value="Gerente de Innovación y Diseño">Gerente Innovación y Diseño</option>
                        <option value="Ingeniero de Producto">Ingeniero de Producto</option>
                        <option value="Calidad">Calidad</option>
                        <option value="Costos">Costos</option>
                        <option value="Procesos">Procesos</option>
                    </select>
                </span>
                <span>
                    <button 
                        v-if="user.perfil !== 'GI'" 
                        @click="deleteUser(user)" 
                        class="btn-delete"
                    >
                        ✕
                    </button>
                </span>
            </div>
        </div>

        <!-- Create/Edit User Modal -->
        <div v-if="showModal" class="modal-overlay" @click="closeModal">
            <div class="modal-content" @click.stop>
                <div class="modal-header">
                    <div>
                        <h3 class="modal-title">{{ editingUser ? 'Editar usuario' : 'Nuevo usuario' }}</h3>
                        <div class="modal-subtitle">{{ editingUser ? 'Modificar información del usuario' : 'Crear nuevo acceso al sistema' }}</div>
                    </div>
                    <button @click="closeModal" class="modal-close">✕</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Nombre completo</label>
                        <input 
                            v-model="formData.name" 
                            type="text" 
                            class="form-input"
                            placeholder="Ej. Carlos Mendoza"
                        />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Usuario (login)</label>
                        <input 
                            v-model="formData.email" 
                            type="email" 
                            class="form-input"
                            placeholder="Ej. cmendoza@lineaitalia.com"
                        />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input 
                            v-model="formData.password" 
                            type="password" 
                            class="form-input"
                            placeholder="Contraseña inicial"
                        />
                    </div>
                    <div class="form-group">
                        <label class="form-label">Perfil</label>
                        <select v-model="formData.role" class="form-select">
                            <option value="Gerente de Innovación y Diseño">Gerente Innovación y Diseño</option>
                            <option value="Ingeniero de Producto">Ingeniero de Producto</option>
                            <option value="Calidad">Calidad</option>
                            <option value="Costos">Costos</option>
                            <option value="Procesos">Procesos</option>
                        </select>
                    </div>
                    <div class="modal-actions">
                        <button @click="closeModal" class="btn-cancel">Cancelar</button>
                        <button @click="saveUser" class="btn-save">Guardar usuario</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const users = ref([])
const loading = ref(false)
const showModal = ref(false)
const editingUser = ref(null)
const formData = ref({
    name: '',
    email: '',
    password: '',
    role: 'Ingeniero de Producto'
})

const loadUsers = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/users')
        users.value = response.data
    } catch (error) {
        console.error('Error cargando usuarios:', error)
        alert('Error al cargar usuarios')
    } finally {
        loading.value = false
    }
}

const openCreateModal = () => {
    editingUser.value = null
    formData.value = {
        name: '',
        email: '',
        password: '',
        role: 'Ingeniero de Producto'
    }
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    editingUser.value = null
}

const saveUser = async () => {
    if (!formData.value.name || !formData.value.email || !formData.value.password) {
        alert('Por favor completa todos los campos')
        return
    }

    try {
        const perfil = getPerfilFromRole(formData.value.role)
        const initials = getInitialsFromRole(formData.value.role)
        
        const userData = {
            name: formData.value.name,
            email: formData.value.email,
            password: formData.value.password,
            role: formData.value.role,
            perfil: perfil,
            initials: initials
        }

        if (editingUser.value) {
            await axios.put(`/api/users/${editingUser.value.id}`, userData)
        } else {
            await axios.post('/api/users', userData)
        }

        await loadUsers()
        closeModal()
    } catch (error) {
        console.error('Error guardando usuario:', error)
        alert('Error al guardar usuario: ' + (error.response?.data?.message || error.message))
    }
}

const updateUserRole = async (user) => {
    try {
        const perfil = getPerfilFromRole(user.role)
        const initials = getInitialsFromRole(user.role)
        
        await axios.put(`/api/users/${user.id}`, {
            role: user.role,
            perfil: perfil,
            initials: initials
        })
        
        await loadUsers()
    } catch (error) {
        console.error('Error actualizando rol:', error)
        alert('Error al actualizar el rol')
    }
}

const deleteUser = async (user) => {
    if (!confirm(`¿Estás seguro de eliminar a ${user.name}?`)) return

    try {
        await axios.delete(`/api/users/${user.id}`)
        await loadUsers()
    } catch (error) {
        console.error('Error eliminando usuario:', error)
        alert('Error al eliminar usuario')
    }
}

const getUserColor = (perfil) => {
    const colors = {
        'GI': '#1a1f3c',
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13',
        'CA': '#0F6E56',
        'CO': '#854F0B',
        'PC': '#185FA5'
    }
    return colors[perfil] || '#6b7280'
}

const getPerfilFromRole = (role) => {
    const mapping = {
        'Gerente de Innovación y Diseño': 'GI',
        'Ingeniero de Producto': 'IP',
        'Calidad': 'CA',
        'Costos': 'CO',
        'Procesos': 'PC'
    }
    return mapping[role] || 'IP'
}

const getInitialsFromRole = (role) => {
    const mapping = {
        'Gerente de Innovación y Diseño': 'GI',
        'Ingeniero de Producto': 'IP',
        'Calidad': 'CA',
        'Costos': 'CO',
        'Procesos': 'PC'
    }
    return mapping[role] || 'IP'
}

onMounted(() => {
    loadUsers()
})
</script>

<style scoped>
.users-container {
    max-width: 1200px;
    margin: 0 auto;
}

.users-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.users-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.users-subtitle {
    font-size: 14px;
    color: #6b7280;
}

.btn-new-user {
    padding: 10px 20px;
    background: #534AB7;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-new-user:hover {
    background: #3C3489;
    transform: translateY(-1px);
}

.loading-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
    font-size: 14px;
}

.users-table {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: hidden;
}

.users-table-header {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1fr 1.5fr 60px;
    gap: 16px;
    padding: 16px 20px;
    background: #f9fafb;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    color: #6b7280;
    border-bottom: 1px solid #e5e7eb;
}

.user-row {
    display: grid;
    grid-template-columns: 2fr 1.5fr 1fr 1.5fr 60px;
    gap: 16px;
    padding: 16px 20px;
    border-bottom: 1px solid #f3f4f6;
    align-items: center;
    transition: background 0.15s;
}

.user-row:hover {
    background: #f9fafb;
}

.user-row:last-child {
    border-bottom: none;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 11px;
    font-weight: 700;
    flex-shrink: 0;
}

.user-name {
    font-size: 13px;
    font-weight: 600;
    color: #1a1f3c;
}

.user-role {
    font-size: 11px;
    color: #6b7280;
    margin-top: 2px;
}

.user-email {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #1a1f3c;
}

.user-password {
    font-family: 'Courier New', monospace;
    font-size: 12px;
    color: #9ca3af;
}

.role-select {
    font-size: 11px;
    border: 1px solid #e5e7eb;
    border-radius: 6px;
    padding: 6px 8px;
    background: white;
    cursor: pointer;
    width: 100%;
}

.btn-delete {
    width: 28px;
    height: 28px;
    border-radius: 6px;
    background: rgba(220, 38, 38, 0.1);
    border: none;
    color: #dc2626;
    font-size: 14px;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-delete:hover {
    background: #dc2626;
    color: white;
}

/* Modal */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    width: 90%;
    max-width: 500px;
    max-height: 90vh;
    overflow: hidden;
}

.modal-header {
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.modal-subtitle {
    font-size: 12px;
    color: #6b7280;
}

.modal-close {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    border: none;
    color: #6b7280;
    font-size: 18px;
    cursor: pointer;
    transition: all 0.2s;
}

.modal-close:hover {
    background: #e5e7eb;
}

.modal-body {
    padding: 24px;
}

.form-group {
    margin-bottom: 20px;
}

.form-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #1a1f3c;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 0.03em;
}

.form-input,
.form-select {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #1a1f3c;
    transition: all 0.2s;
}

.form-input:focus,
.form-select:focus {
    outline: none;
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 24px;
}

.btn-cancel {
    padding: 10px 20px;
    background: #f3f4f6;
    color: #6b7280;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-cancel:hover {
    background: #e5e7eb;
}

.btn-save {
    padding: 10px 24px;
    background: #534AB7;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-save:hover {
    background: #3C3489;
}
</style>

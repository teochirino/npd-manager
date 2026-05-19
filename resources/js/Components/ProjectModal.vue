<template>
    <Teleport to="body">
        <Transition name="modal">
            <div v-if="show" class="modal-overlay" @click.self="$emit('close')">
                <div class="modal-container">
                    <!-- Header -->
                    <div class="modal-header">
                        <div>
                            <h2 class="modal-title">{{ project?.name || 'Nuevo Proyecto' }}</h2>
                            <p class="modal-subtitle">{{ project?.project_id || 'Crear nuevo proyecto' }}</p>
                        </div>
                        <button @click="$emit('close')" class="close-btn">✕</button>
                    </div>

                    <!-- Tabs -->
                    <div class="tabs">
                        <button 
                            v-for="tab in tabs" 
                            :key="tab.id"
                            @click="activeTab = tab.id"
                            :class="['tab', { active: activeTab === tab.id }]"
                        >
                            <span class="tab-icon">{{ tab.icon }}</span>
                            <span>{{ tab.label }}</span>
                        </button>
                    </div>

                    <!-- Content -->
                    <div class="modal-content">
                        <!-- Tab: Información General -->
                        <div v-if="activeTab === 'info'" class="tab-content">
                            <div v-if="!project" class="info-banner">
                                <strong>⚙ SE CREARÁ AUTOMÁTICAMENTE</strong><br>
                                ✓ 5 fases: Diseño · Desarrollo · Implementación · Lanzamiento · Seguimiento<br>
                                ✓ 70 tareas estándar del proceso NPD · Gates · Historial iniciado
                            </div>

                            <div class="form-grid">
                                <div class="form-group">
                                    <label>ID del Proyecto *</label>
                                    <input v-model="form.project_id" type="text" class="form-input" :disabled="!!project" placeholder="Ej: PROJ001" maxlength="20" />
                                    <div v-if="!project" class="field-hint">Auto-generado. Puedes cambiarlo.</div>
                                </div>
                                <div class="form-group">
                                    <label>Ingeniero Responsable *</label>
                                    <select v-model="form.engineer_id" class="form-input">
                                        <option value="">Seleccionar ingeniero</option>
                                        <option v-for="eng in engineers" :key="eng.id" :value="eng.id">
                                            {{ eng.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group full-width">
                                <label>Nombre del Proyecto *</label>
                                <input v-model="form.name" type="text" class="form-input" placeholder="Ej: Línea Attese 3.0, Mamparas Comodín..." maxlength="80" />
                            </div>

                            <div class="form-grid-3">
                                <div class="form-group">
                                    <label>Fecha Objetivo Final *</label>
                                    <input v-model="form.final_date" type="date" class="form-input" />
                                </div>
                                <div class="form-group">
                                    <label>Estatus Inicial</label>
                                    <select v-model="form.status" class="form-input">
                                        <option value="En linea">🟢 En línea</option>
                                        <option value="En pausa">🟡 En pausa</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Categoría</label>
                                    <select v-model="form.category" class="form-input">
                                        <option value="">Sin categoría</option>
                                        <option value="Sillas y sillones">Sillas y sillones</option>
                                        <option value="Escritorios y estaciones">Escritorios y estaciones</option>
                                        <option value="Mamparas">Mamparas</option>
                                        <option value="Módulos directivos">Módulos directivos</option>
                                        <option value="Accesorios">Accesorios</option>
                                        <option value="Línea económica">Línea económica</option>
                                        <option value="Reingeniería">Reingeniería</option>
                                    </select>
                                </div>
                            </div>

                            <div v-if="!project" class="form-group full-width">
                                <label>Brief Inicial (opcional)</label>
                                <textarea v-model="form.brief" class="form-textarea" placeholder="Objetivo del proyecto, contexto, necesidad del mercado..." rows="3"></textarea>
                            </div>

                            <div v-if="project" class="form-group">
                                <label>Fase Actual</label>
                                <select v-model="form.active_phase" class="form-input">
                                    <option value="Diseño">Diseño</option>
                                    <option value="Desarrollo">Desarrollo</option>
                                    <option value="Implementacion">Implementación</option>
                                    <option value="Lanzamiento">Lanzamiento</option>
                                    <option value="Seguimiento">Seguimiento</option>
                                </select>
                            </div>

                            <!-- Progress -->
                            <div class="progress-display">
                                <div class="progress-label">
                                    <span>Avance del Proyecto</span>
                                    <span class="progress-value">{{ project?.progress || 0 }}%</span>
                                </div>
                                <div class="progress-bar-large">
                                    <div 
                                        class="progress-fill-large" 
                                        :style="{ width: (project?.progress || 0) + '%' }"
                                    ></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Tareas -->
                        <div v-if="activeTab === 'tasks'" class="tab-content">
                            <div v-if="loadingTasks" class="loading">Cargando tareas...</div>
                            <div v-else-if="tasks.length === 0" class="empty-message">
                                No hay tareas para esta fase
                            </div>
                            <div v-else class="tasks-list">
                                <div v-for="task in tasks" :key="task.id" class="task-item">
                                    <input 
                                        type="checkbox" 
                                        :checked="task.status === 'Completado'"
                                        @change="toggleTask(task)"
                                        class="task-checkbox"
                                    />
                                    <div class="task-info">
                                        <div class="task-name">{{ task.predefined_task?.name }}</div>
                                        <div class="task-meta">
                                            <span class="task-department">{{ task.predefined_task?.department }}</span>
                                            <span class="task-status" :class="'status-' + task.status.toLowerCase().replace(' ', '-')">
                                                {{ task.status }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: SKUs -->
                        <div v-if="activeTab === 'skus'" class="tab-content">
                            <div class="section-header-inline">
                                <h3>SKUs del Proyecto</h3>
                                <button @click="showAddSku = true" class="btn-small">+ Agregar SKU</button>
                            </div>
                            
                            <div v-if="loadingSkus" class="loading">Cargando SKUs...</div>
                            <div v-else-if="skus.length === 0" class="empty-message">
                                No hay SKUs registrados
                            </div>
                            <div v-else class="skus-grid">
                                <div v-for="sku in skus" :key="sku.id" class="sku-card">
                                    <div class="sku-header">
                                        <div>
                                            <div class="sku-id">{{ sku.sku_id }}</div>
                                            <div class="sku-name">{{ sku.name }}</div>
                                        </div>
                                        <span class="sku-status" :class="'status-' + sku.status.toLowerCase()">
                                            {{ sku.status }}
                                        </span>
                                    </div>
                                    <div class="sku-code">Código: {{ sku.code }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Tab: Notas -->
                        <div v-if="activeTab === 'notes'" class="tab-content">
                            <div class="notes-section">
                                <textarea 
                                    v-model="newNote" 
                                    placeholder="Agregar una nota..."
                                    class="notes-textarea"
                                ></textarea>
                                <button @click="addNote" class="btn-primary-small">Agregar Nota</button>
                            </div>
                            
                            <div v-if="project?.notes && project.notes.length > 0" class="notes-list">
                                <div v-for="note in project.notes" :key="note.id" class="note-item">
                                    <div class="note-header">
                                        <span class="note-author">{{ note.user?.name }}</span>
                                        <span class="note-date">{{ formatDate(note.created_at) }}</span>
                                    </div>
                                    <div class="note-content">{{ note.content }}</div>
                                </div>
                            </div>
                            <div v-else class="empty-message">No hay notas</div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="modal-footer">
                        <button @click="$emit('close')" class="btn-secondary">Cancelar</button>
                        <button @click="saveProject" :disabled="saving" class="btn-primary">
                            {{ saving ? 'Guardando...' : (project ? 'Guardar Cambios' : 'Crear Proyecto') }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>

<script setup>
import { ref, watch, computed } from 'vue'
import axios from 'axios'

const props = defineProps({
    show: Boolean,
    project: Object,
    engineers: Array
})

const emit = defineEmits(['close', 'saved'])

const activeTab = ref('info')
const saving = ref(false)
const loadingTasks = ref(false)
const loadingSkus = ref(false)
const tasks = ref([])
const skus = ref([])
const newNote = ref('')
const showAddSku = ref(false)

const form = ref({
    project_id: '',
    name: '',
    engineer_id: '',
    final_date: '',
    status: 'En linea',
    active_phase: 'Diseño',
    category: '',
    brief: ''
})

const tabs = [
    { id: 'info', label: 'Información', icon: 'ℹ️' },
    { id: 'tasks', label: 'Tareas', icon: '✓' },
    { id: 'skus', label: 'SKUs', icon: '📦' },
    { id: 'notes', label: 'Notas', icon: '📝' }
]

watch(() => props.show, (newVal) => {
    if (newVal && props.project) {
        form.value = {
            project_id: props.project.project_id,
            name: props.project.name,
            engineer_id: props.project.engineer_id,
            final_date: props.project.final_date,
            status: props.project.status,
            active_phase: props.project.active_phase,
            category: props.project.category || '',
            brief: ''
        }
        loadTasks()
        loadSkus()
    } else if (newVal && !props.project) {
        // Auto-generar ID para nuevo proyecto
        const nextId = generateProjectId()
        // Fecha por defecto: 6 meses desde hoy
        const defaultDate = new Date()
        defaultDate.setMonth(defaultDate.getMonth() + 6)
        const dateStr = defaultDate.toISOString().split('T')[0]
        
        form.value = {
            project_id: nextId,
            name: '',
            engineer_id: props.engineers?.[0]?.id || '',
            final_date: dateStr,
            status: 'En linea',
            active_phase: 'Diseño',
            category: '',
            brief: ''
        }
    }
})

const loadTasks = async () => {
    if (!props.project) return
    loadingTasks.value = true
    try {
        const response = await axios.get(`/api/projects/${props.project.id}/tasks`)
        tasks.value = response.data
    } catch (error) {
        console.error('Error cargando tareas:', error)
    } finally {
        loadingTasks.value = false
    }
}

const loadSkus = async () => {
    if (!props.project) return
    loadingSkus.value = true
    try {
        const response = await axios.get(`/api/projects/${props.project.id}/skus`)
        skus.value = response.data
    } catch (error) {
        console.error('Error cargando SKUs:', error)
    } finally {
        loadingSkus.value = false
    }
}

const toggleTask = async (task) => {
    const newStatus = task.status === 'Completado' ? 'No iniciado' : 'Completado'
    try {
        await axios.put(`/api/tasks/${task.id}`, { status: newStatus })
        task.status = newStatus
        emit('saved')
    } catch (error) {
        console.error('Error actualizando tarea:', error)
    }
}

const generateProjectId = () => {
    const timestamp = Date.now().toString().slice(-6)
    return `PROJ${timestamp}`
}

const saveProject = async () => {
    saving.value = true
    try {
        if (props.project) {
            await axios.put(`/api/projects/${props.project.id}`, form.value)
        } else {
            await axios.post('/api/projects', form.value)
        }
        emit('saved')
        emit('close')
    } catch (error) {
        console.error('Error guardando proyecto:', error)
        alert('Error al guardar el proyecto')
    } finally {
        saving.value = false
    }
}

const addNote = () => {
    // TODO: Implementar API de notas
    console.log('Agregar nota:', newNote.value)
    newNote.value = ''
}

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>

<style scoped>
.modal-overlay {
    position: fixed;
    inset: 0;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 20px;
}

.modal-container {
    background: white;
    border-radius: 16px;
    width: 100%;
    max-width: 900px;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-header {
    padding: 24px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.modal-title {
    font-size: 24px;
    font-weight: 800;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.modal-subtitle {
    font-size: 14px;
    color: #6b7280;
    font-family: 'DM Mono', monospace;
}

.close-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: none;
    background: #f3f4f6;
    color: #6b7280;
    font-size: 20px;
    cursor: pointer;
    transition: all 0.2s;
}

.close-btn:hover {
    background: #e5e7eb;
    color: #1a1f3c;
}

.tabs {
    display: flex;
    border-bottom: 1px solid #e5e7eb;
    padding: 0 24px;
    gap: 4px;
}

.tab {
    padding: 12px 20px;
    border: none;
    background: none;
    color: #6b7280;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    border-bottom: 2px solid transparent;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.tab:hover {
    color: #1a1f3c;
    background: #f9fafb;
}

.tab.active {
    color: #534AB7;
    border-bottom-color: #534AB7;
}

.tab-icon {
    font-size: 16px;
}

.modal-content {
    flex: 1;
    overflow-y: auto;
    padding: 24px;
}

.tab-content {
    animation: fadeIn 0.2s;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.form-group label {
    font-size: 13px;
    font-weight: 600;
    color: #374151;
}

.form-input {
    padding: 10px 14px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    transition: border-color 0.2s;
}

.form-input:focus {
    outline: none;
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.form-input:disabled {
    background: #f9fafb;
    color: #9ca3af;
}

.progress-display {
    background: #f9fafb;
    padding: 20px;
    border-radius: 12px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 12px;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
}

.progress-value {
    color: #534AB7;
    font-size: 18px;
}

.progress-bar-large {
    height: 12px;
    background: #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
}

.progress-fill-large {
    height: 100%;
    background: linear-gradient(90deg, #534AB7, #7c3aed);
    transition: width 0.3s;
    border-radius: 6px;
}

.tasks-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.task-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    transition: background 0.2s;
}

.task-item:hover {
    background: #f3f4f6;
}

.task-checkbox {
    width: 20px;
    height: 20px;
    margin-top: 2px;
    cursor: pointer;
}

.task-info {
    flex: 1;
}

.task-name {
    font-size: 14px;
    font-weight: 600;
    color: #1a1f3c;
    margin-bottom: 6px;
}

.task-meta {
    display: flex;
    gap: 12px;
    font-size: 12px;
}

.task-department {
    color: #6b7280;
}

.task-status {
    padding: 2px 8px;
    border-radius: 4px;
    font-weight: 600;
}

.status-completado {
    background: #E1F5EE;
    color: #085041;
}

.status-en-progreso {
    background: #E6F1FB;
    color: #0C447C;
}

.status-no-iniciado {
    background: #f3f4f6;
    color: #6b7280;
}

.section-header-inline {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-header-inline h3 {
    font-size: 18px;
    font-weight: 700;
    color: #1a1f3c;
}

.btn-small {
    padding: 8px 16px;
    background: #534AB7;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-small:hover {
    background: #3C3489;
}

.skus-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}

.sku-card {
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
}

.sku-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.sku-id {
    font-size: 12px;
    font-family: 'DM Mono', monospace;
    color: #6b7280;
    margin-bottom: 4px;
}

.sku-name {
    font-size: 14px;
    font-weight: 600;
    color: #1a1f3c;
}

.sku-status {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 11px;
    font-weight: 600;
}

.status-pendiente {
    background: #f3f4f6;
    color: #6b7280;
}

.status-en-proceso {
    background: #E6F1FB;
    color: #0C447C;
}

.status-completado {
    background: #E1F5EE;
    color: #085041;
}

.sku-code {
    font-size: 12px;
    color: #6b7280;
}

.notes-section {
    margin-bottom: 24px;
}

.notes-textarea {
    width: 100%;
    min-height: 100px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    margin-bottom: 12px;
}

.notes-textarea:focus {
    outline: none;
    border-color: #534AB7;
}

.btn-primary-small {
    padding: 8px 16px;
    background: #534AB7;
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
}

.notes-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.note-item {
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #534AB7;
}

.note-header {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
}

.note-author {
    font-weight: 600;
    color: #1a1f3c;
}

.note-date {
    color: #6b7280;
}

.note-content {
    font-size: 14px;
    color: #374151;
    line-height: 1.6;
}

.loading, .empty-message {
    text-align: center;
    padding: 40px;
    color: #6b7280;
}

.modal-footer {
    padding: 20px 24px;
    border-top: 1px solid #e5e7eb;
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}

.btn-secondary {
    padding: 10px 20px;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: #374151;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-primary {
    padding: 10px 20px;
    background: #534AB7;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    color: white;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-primary:hover {
    background: #3C3489;
}

.btn-primary:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.modal-enter-active, .modal-leave-active {
    transition: opacity 0.3s;
}

.modal-enter-from, .modal-leave-to {
    opacity: 0;
}

.modal-enter-active .modal-container,
.modal-leave-active .modal-container {
    transition: transform 0.3s;
}

.modal-enter-from .modal-container,
.modal-leave-to .modal-container {
    transform: scale(0.9);
}

/* Nuevos estilos para formulario de creación */
.info-banner {
    background: #f8f9fb;
    border: 1px solid #e4e8ee;
    border-radius: 12px;
    padding: 10px 13px;
    margin-bottom: 16px;
    font-size: 11px;
    color: #4a5568;
    line-height: 1.8;
}

.info-banner strong {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #6b7280;
}

.form-grid-3 {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
    margin-bottom: 14px;
}

.full-width {
    grid-column: 1 / -1;
}

.field-hint {
    font-size: 10px;
    color: #6b7280;
    margin-top: 3px;
}

.form-textarea {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 13px;
    font-family: inherit;
    resize: vertical;
    color: #1a1f3c;
}

.form-textarea:focus {
    outline: none;
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.form-textarea::placeholder {
    color: #9ca3af;
}
</style>

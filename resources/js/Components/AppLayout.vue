<template>
    <div class="app-container">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <svg viewBox="0 0 200 58" width="160" height="46" xmlns="http://www.w3.org/2000/svg">
                    <rect x="1" y="6" width="42" height="42" rx="4" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                    <text x="22" y="30" text-anchor="middle" dominant-baseline="middle" font-family="Arial,sans-serif" font-size="16" font-weight="700" fill="#ffffff" letter-spacing="2">LI</text>
                    <text x="54" y="26" font-family="Georgia,serif" font-size="19" font-weight="400" fill="#ffffff">línea italia</text>
                    <text x="186" y="14" font-family="Georgia,serif" font-size="9" fill="#9ca3af">®</text>
                    <text x="54" y="41" font-family="Arial,sans-serif" font-size="7.5" font-weight="400" fill="#9ca3af" letter-spacing="2.8">ECCELLENZA IN MOBILE</text>
                </svg>
                <div class="sidebar-subtitle">NPD Manager · Gestión de Innovación</div>
            </div>

            <!-- User Info -->
            <div class="user-info">
                <div class="user-avatar" :style="{ backgroundColor: user?.perfil ? getAvatarColor(user.perfil) : '#e5e7eb' }">
                    {{ user?.initials || 'U' }}
                </div>
                <div class="user-details">
                    <div class="user-name">{{ user?.name }}</div>
                    <div class="user-role">{{ user?.role }}</div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="sidebar-nav">
                <a 
                    v-for="item in menuItems" 
                    :key="item.id"
                    href="#"
                    @click.prevent="$emit('navigate', item.id)"
                    :class="['nav-item', { active: currentView === item.id }]"
                >
                    <span class="nav-icon">{{ item.icon }}</span>
                    <span class="nav-label">{{ item.label }}</span>
                </a>
            </nav>

            <!-- Logout -->
            <button @click="$emit('logout')" class="logout-btn">
                <span>🚪</span>
                <span>Salir</span>
            </button>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <slot></slot>
        </main>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    user: Object,
    currentView: String
})

defineEmits(['navigate', 'logout'])

const menuItems = computed(() => {
    const perfil = props.user?.perfil
    
    if (perfil === 'GI') {
        return [
            { id: 'inicio', label: 'Inicio', icon: '⊞' },
            { id: 'proyectos', label: 'Todos los proyectos', icon: '▤' },
            { id: 'reporte', label: 'Reporte ejecutivo', icon: '↗' },
            { id: 'indicadores', label: 'Indicadores NPD', icon: '📊' },
            { id: 'aprobaciones', label: 'Aprobaciones', icon: '✓' },
            { id: 'diagrama', label: 'Diagrama de proceso', icon: '◈' },
            { id: 'usuarios', label: 'Gestión de usuarios', icon: '⊙' },
            { id: 'respaldo', label: 'Respaldo de datos', icon: '↓' },
        ]
    } else if (['RB', 'FR', 'AE', 'PR', 'RE'].includes(perfil)) {
        return [
            { id: 'mis-proyectos', label: 'Mis proyectos', icon: '⊞' },
            { id: 'diagrama', label: 'Diagrama de proceso', icon: '◈' },
        ]
    } else if (['CAL', 'COS', 'PRO'].includes(perfil)) {
        return [
            { id: 'mis-tareas-area', label: 'Mis tareas pendientes', icon: '✓' },
            { id: 'diagrama', label: 'Diagrama de proceso', icon: '◈' },
        ]
    }

    return [{ id: 'inicio', label: 'Inicio', icon: '⊞' }]
})

const getAvatarColor = (perfil) => {
    const colors = {
        'GI': '#1a1f3c',
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13',
        'CAL': '#185FA5',
        'COS': '#0F6E56',
        'PRO': '#854F0B',
    }
    return colors[perfil] || '#374151'
}
</script>

<style scoped>
.app-container {
    display: flex;
    min-height: 100vh;
    background-color: #f4f6f9;
}

.sidebar {
    width: 280px;
    background: linear-gradient(180deg, #1a1f3c 0%, #2d3561 100%);
    color: white;
    display: flex;
    flex-direction: column;
    position: fixed;
    height: 100vh;
    overflow-y: auto;
}

.sidebar-header {
    padding: 24px 20px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.sidebar-subtitle {
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 8px;
    text-align: center;
}

.user-info {
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 16px;
    color: white;
    flex-shrink: 0;
}

.user-details {
    flex: 1;
    min-width: 0;
}

.user-name {
    font-weight: 600;
    font-size: 14px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.user-role {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.6);
    margin-top: 2px;
}

.sidebar-nav {
    flex: 1;
    padding: 16px 0;
}

.nav-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: all 0.2s;
    font-size: 14px;
}

.nav-item:hover {
    background-color: rgba(255, 255, 255, 0.1);
    color: white;
}

.nav-item.active {
    background-color: rgba(255, 255, 255, 0.15);
    color: white;
    border-left: 3px solid #534AB7;
}

.nav-icon {
    font-size: 18px;
    width: 24px;
    text-align: center;
}

.nav-label {
    flex: 1;
}

.logout-btn {
    margin: 16px 20px;
    padding: 12px;
    background-color: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 8px;
    color: white;
    display: flex;
    align-items: center;
    gap: 8px;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
    font-size: 14px;
}

.logout-btn:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

.main-content {
    flex: 1;
    margin-left: 280px;
    padding: 24px;
    overflow-y: auto;
    height: 100vh;
}
</style>

<template>
    <div class="project-card" @click="$emit('click')">
        <!-- Header -->
        <div class="card-header">
            <div class="project-id">{{ project.project_id }}</div>
            <div class="status-badge" :class="statusClass">
                {{ project.status }}
            </div>
        </div>

        <!-- Title -->
        <h3 class="project-title">{{ project.name }}</h3>

        <!-- Engineer -->
        <div class="engineer-info">
            <div class="engineer-avatar" :style="{ backgroundColor: getEngineerColor() }">
                {{ project.engineer?.initials || 'N/A' }}
            </div>
            <div class="engineer-name">{{ project.engineer?.name || 'Sin asignar' }}</div>
        </div>

        <!-- Phase -->
        <div class="phase-badge" :style="{ 
            backgroundColor: getPhaseColor().bg,
            color: getPhaseColor().text
        }">
            <span class="phase-icon">{{ getPhaseIcon() }}</span>
            <span>{{ project.active_phase }}</span>
        </div>

        <!-- Progress -->
        <div class="progress-section">
            <div class="progress-header">
                <span class="progress-label">Avance</span>
                <span class="progress-value">{{ project.progress }}%</span>
            </div>
            <div class="progress-bar">
                <div 
                    class="progress-fill" 
                    :style="{ width: project.progress + '%', backgroundColor: getProgressColor() }"
                ></div>
            </div>
        </div>

        <!-- Footer -->
        <div class="card-footer">
            <div class="footer-item">
                <span class="footer-icon">📅</span>
                <span class="footer-text">{{ formatDate(project.final_date) }}</span>
            </div>
            <div class="footer-item" v-if="project.skus && project.skus.length > 0">
                <span class="footer-icon">📦</span>
                <span class="footer-text">{{ project.skus.length }} SKU{{ project.skus.length > 1 ? 's' : '' }}</span>
            </div>
        </div>

        <!-- Risk indicator -->
        <div v-if="isAtRisk" class="risk-indicator">
            ⚠️ Próximo a vencer
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
    project: {
        type: Object,
        required: true
    }
})

defineEmits(['click'])

const statusClass = computed(() => {
    const classes = {
        'Terminado': 'status-completed',
        'En linea': 'status-active',
        'En pausa': 'status-paused',
        'Cancelado': 'status-cancelled',
        'Fuera de linea': 'status-offline'
    }
    return classes[props.project.status] || 'status-active'
})

const isAtRisk = computed(() => {
    if (!props.project.final_date || props.project.status === 'Terminado') return false
    const daysUntil = Math.ceil((new Date(props.project.final_date) - new Date()) / (1000 * 60 * 60 * 24))
    return daysUntil <= 7 && daysUntil >= 0
})

const getPhaseColor = () => {
    const colors = {
        'Diseño': { bg: '#EEEDFE', text: '#3C3489' },
        'Desarrollo': { bg: '#E6F1FB', text: '#0C447C' },
        'Implementacion': { bg: '#E1F5EE', text: '#085041' },
        'Lanzamiento': { bg: '#FAEEDA', text: '#633806' },
        'Seguimiento': { bg: '#FAECE7', text: '#712B13' }
    }
    return colors[props.project.active_phase] || { bg: '#e5e7eb', text: '#374151' }
}

const getPhaseIcon = () => {
    const icons = {
        'Diseño': '✏️',
        'Desarrollo': '🔧',
        'Implementacion': '⚙️',
        'Lanzamiento': '🚀',
        'Seguimiento': '📊'
    }
    return icons[props.project.active_phase] || '📋'
}

const getProgressColor = () => {
    if (props.project.progress >= 80) return '#0F6E56'
    if (props.project.progress >= 50) return '#185FA5'
    if (props.project.progress >= 25) return '#854F0B'
    return '#9ca3af'
}

const getEngineerColor = () => {
    const perfil = props.project.engineer?.perfil
    const colors = {
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13'
    }
    return colors[perfil] || '#6b7280'
}

const formatDate = (date) => {
    if (!date) return 'Sin fecha'
    return new Date(date).toLocaleDateString('es-MX', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    })
}
</script>

<style scoped>
.project-card {
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    transition: all 0.2s;
    cursor: pointer;
    position: relative;
    border: 1px solid #e5e7eb;
}

.project-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.project-id {
    font-family: 'DM Mono', monospace;
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-completed {
    background-color: #E1F5EE;
    color: #085041;
}

.status-active {
    background-color: #E6F1FB;
    color: #0C447C;
}

.status-paused {
    background-color: #FAEEDA;
    color: #633806;
}

.status-cancelled {
    background-color: #fee2e2;
    color: #991b1b;
}

.status-offline {
    background-color: #f3f4f6;
    color: #6b7280;
}

.project-title {
    font-size: 16px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 16px;
    line-height: 1.4;
}

.engineer-info {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 16px;
}

.engineer-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: 700;
    color: white;
}

.engineer-name {
    font-size: 13px;
    color: #4b5563;
}

.phase-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    margin-bottom: 16px;
}

.phase-icon {
    font-size: 14px;
}

.progress-section {
    margin-bottom: 16px;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 8px;
}

.progress-label {
    font-size: 12px;
    color: #6b7280;
    font-weight: 600;
}

.progress-value {
    font-size: 14px;
    font-weight: 700;
    color: #1a1f3c;
}

.progress-bar {
    height: 8px;
    background-color: #e5e7eb;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    transition: width 0.3s ease;
    border-radius: 4px;
}

.card-footer {
    display: flex;
    gap: 16px;
    padding-top: 12px;
    border-top: 1px solid #e5e7eb;
}

.footer-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
}

.footer-icon {
    font-size: 14px;
}

.risk-indicator {
    position: absolute;
    top: 12px;
    right: 12px;
    background-color: #fef3c7;
    color: #92400e;
    padding: 4px 8px;
    border-radius: 6px;
    font-size: 10px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
}
</style>

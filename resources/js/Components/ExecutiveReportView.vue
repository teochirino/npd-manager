<template>
    <div class="report-container">
        <!-- Header -->
        <div class="report-header">
            <div>
                <h1 class="report-title">Reporte ejecutivo</h1>
                <p class="report-subtitle">Vista filtrable por ingeniero</p>
            </div>
            <div class="header-actions">
                <span class="current-time">{{ currentTime }}</span>
                <button @click="loadData" class="btn-refresh">
                    🔄 Actualizar
                </button>
                <select v-model="selectedEngineer" class="filter-select">
                    <option value="">Todos los ingenieros</option>
                    <option v-for="eng in engineers" :key="eng.id" :value="eng.id">
                        {{ eng.name }}
                    </option>
                </select>
                <select v-model="selectedAction" class="filter-select">
                    <option value="">Solo activos</option>
                    <option value="all">Todos</option>
                    <option value="completed">Completados</option>
                    <option value="paused">En pausa</option>
                </select>
                <button @click="printReport" class="btn-print">
                    🖨️ Imprimir reporte
                </button>
            </div>
        </div>

        <!-- Report Title Bar -->
        <div class="report-title-bar">
            <div class="report-info">
                <strong>Reporte ejecutivo - Gerencia de Innovación y Diseño</strong>
                <span class="report-date">Linea Italia · NPD Manager</span>
            </div>
            <div class="report-filters-bar">
                <select v-model="selectedEngineer" class="filter-select-bar">
                    <option value="">Todos los ingenieros</option>
                    <option v-for="eng in engineers" :key="eng.id" :value="eng.id">
                        {{ eng.name }}
                    </option>
                </select>
                <select v-model="selectedAction" class="filter-select-bar">
                    <option value="">Solo activos</option>
                    <option value="all">Todos</option>
                </select>
                <button @click="printReport" class="btn-print-bar">
                    🖨 Imprimir reporte
                </button>
                <span class="report-date-time">{{ currentDateTime }}</span>
            </div>
        </div>

        <!-- KPI Panel -->
        <div class="kpi-panel">
            <div class="kpi-header">
                <span>📊 PANEL DE KPIS DEL EQUIPO</span>
            </div>
            <div class="kpi-grid-report">
                <div class="kpi-item">
                    <div class="kpi-label">TOTAL PROYECTOS</div>
                    <div class="kpi-value-large">{{ reportStats.total }}</div>
                    <div class="kpi-sublabel">proyectos</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">TERMINADOS</div>
                    <div class="kpi-value-large">{{ reportStats.completed }}</div>
                    <div class="kpi-sublabel">completados</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">EFECTIVIDAD</div>
                    <div class="kpi-value-large">{{ reportStats.effectiveness }}%</div>
                    <div class="kpi-sublabel">0 de 20 cancelados</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">EN LÍNEA</div>
                    <div class="kpi-value-large">{{ reportStats.online }}</div>
                    <div class="kpi-sublabel">activos</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">FUERA DE LÍNEA</div>
                    <div class="kpi-value-large">{{ reportStats.offline }}</div>
                    <div class="kpi-sublabel">con retraso</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">EN PAUSA</div>
                    <div class="kpi-value-large">{{ reportStats.paused }}</div>
                    <div class="kpi-sublabel">pausados</div>
                </div>
                <div class="kpi-item">
                    <div class="kpi-label">A EN RIESGO</div>
                    <div class="kpi-value-large">{{ reportStats.atRisk }}</div>
                    <div class="kpi-sublabel">entrega <30d días</div>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-label">
                    <span>⏱️ AVANCE PROMEDIO</span>
                    <span class="progress-percentage">{{ reportStats.avgProgress }}% de todos los proyectos</span>
                </div>
                <div class="progress-bar-full">
                    <div class="progress-fill-full" :style="{ width: reportStats.avgProgress + '%' }"></div>
                </div>
            </div>
        </div>

        <!-- Projects by Engineer -->
        <div v-if="loading" class="loading-state">Cargando proyectos...</div>
        <div v-else class="engineers-section">
            <div v-for="engineer in filteredEngineers" :key="engineer.id" class="engineer-block">
                <div class="engineer-header">
                    <div class="engineer-avatar" :style="{ backgroundColor: getEngineerColor(engineer.perfil) }">
                        {{ engineer.initials }}
                    </div>
                    <div class="engineer-info">
                        <div class="engineer-name">{{ engineer.name }}</div>
                        <div class="engineer-stats">
                            {{ engineer.projects.length }} proyecto{{ engineer.projects.length !== 1 ? 's' : '' }} · 
                            Avance {{ engineer.avgProgress }}%
                        </div>
                    </div>
                    <div class="engineer-summary">
                        <span class="summary-badge">⏱️ {{ engineer.inProgress }} vencidos</span>
                    </div>
                </div>

                <!-- Projects Table -->
                <div class="table-wrapper">
                    <table class="report-table">
                        <tbody>
                            <tr v-for="(project, idx) in engineer.projects" :key="project.id" 
                                :class="['table-row', { 'row-even': idx % 2 === 0 }]"
                                @click="viewProject(project)">
                                <td class="cell-health">
                                    <div class="health-icon">{{ getHealthIcon(project) }}</div>
                                    <div class="health-label" :style="{ color: getHealthColor(project) }">
                                        {{ getHealthLabel(project) }}
                                    </div>
                                </td>
                                <td class="cell-id">{{ project.project_id }}</td>
                                <td class="cell-name">
                                    <div class="name-main">{{ project.name }}</div>
                                    <div class="name-sub">{{ engineer.name }}</div>
                                </td>
                                <td class="cell-status">
                                    <span class="status-pill" :class="getStatusPillClass(project.status)">
                                        {{ getStatusLabel(project.status) }}
                                    </span>
                                </td>
                                <td class="cell-progress">
                                    <div class="progress-container">
                                        <div class="progress-bar-mini">
                                            <div class="progress-fill-mini" 
                                                :style="{ width: project.progress + '%', background: getProgressColor(project.progress) }">
                                            </div>
                                        </div>
                                        <span class="progress-label">{{ project.progress }}%</span>
                                    </div>
                                </td>
                                <td class="cell-date">{{ formatDate(project.final_date) }}</td>
                                <td class="cell-days" :style="{ color: getDaysColor(project.final_date) }">
                                    {{ getDaysRemaining(project.final_date) }}
                                </td>
                                <td class="cell-phase">
                                    <span class="phase-pill" :style="getPhaseStyle(project.active_phase)">
                                        {{ project.active_phase }}
                                    </span>
                                </td>
                                <td class="cell-milestone">
                                    <div class="milestone-text">{{ getMilestone(project) }}</div>
                                </td>
                                <td class="cell-ms-date" :style="{ color: getMilestoneColor(project) }">
                                    {{ getMilestoneDate(project) }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    user: Object
})

const projects = ref([])
const engineers = ref([])
const loading = ref(false)
const selectedEngineer = ref('')
const selectedAction = ref('')

const currentTime = computed(() => {
    return new Date().toLocaleTimeString('es-MX', {
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    })
})

const currentDateTime = computed(() => {
    return new Date().toLocaleString('es-MX', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    }) + ' p. m.'
})

const currentDate = computed(() => {
    return new Date().toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
})

const reportStats = computed(() => {
    const total = projects.value.length
    const completed = projects.value.filter(p => p.status === 'Terminado').length
    const online = projects.value.filter(p => p.status === 'En linea').length
    const offline = projects.value.filter(p => p.status === 'Fuera de linea').length
    const paused = projects.value.filter(p => p.status === 'En pausa').length
    const atRisk = projects.value.filter(p => isNearDeadline(p.final_date)).length
    const avgProgress = total > 0 ? Math.round(projects.value.reduce((sum, p) => sum + p.progress, 0) / total) : 0
    const effectiveness = total > 0 ? Math.round((completed / total) * 100) : 0

    return { total, completed, online, offline, paused, atRisk, avgProgress, effectiveness }
})

const filteredEngineers = computed(() => {
    console.log('=== FILTERED ENGINEERS DEBUG ===')
    console.log('Total engineers:', engineers.value.length)
    console.log('Total projects:', projects.value.length)
    console.log('Selected action:', selectedAction.value)
    console.log('Selected engineer:', selectedEngineer.value)
    
    let engs = engineers.value.map(eng => {
        let engineerProjects = projects.value.filter(p => p.engineer_id === eng.id)
        console.log(`Engineer ${eng.name} (ID: ${eng.id}): ${engineerProjects.length} proyectos totales`)
        
        if (selectedAction.value === 'completed') {
            engineerProjects = engineerProjects.filter(p => p.status === 'Terminado')
        } else if (selectedAction.value === 'paused') {
            engineerProjects = engineerProjects.filter(p => p.status === 'En pausa')
        } else if (!selectedAction.value || selectedAction.value === '') {
            const before = engineerProjects.length
            engineerProjects = engineerProjects.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
            console.log(`  Después de filtrar activos: ${engineerProjects.length} (antes: ${before})`)
        }

        const avgProgress = engineerProjects.length > 0 
            ? Math.round(engineerProjects.reduce((sum, p) => sum + p.progress, 0) / engineerProjects.length)
            : 0
        
        const inProgress = engineerProjects.filter(p => isNearDeadline(p.final_date)).length

        return {
            ...eng,
            projects: engineerProjects,
            avgProgress,
            inProgress
        }
    })

    if (selectedEngineer.value) {
        engs = engs.filter(e => e.id === parseInt(selectedEngineer.value))
    }

    const result = engs.filter(e => e.projects.length > 0)
    console.log('Ingenieros con proyectos después de filtrar:', result.length)
    console.log('Ingenieros:', result.map(e => `${e.name} (${e.projects.length} proyectos)`))
    
    return result
})

const loadData = async () => {
    loading.value = true
    try {
        const [projectsRes, engineersRes] = await Promise.all([
            axios.get('/api/projects'),
            axios.get('/api/users/engineers')
        ])
        projects.value = projectsRes.data
        engineers.value = engineersRes.data
        
        console.log('Proyectos cargados:', projects.value.length)
        console.log('Ingenieros cargados:', engineers.value.length)
        console.log('Ingenieros:', engineers.value.map(e => e.name))
    } catch (error) {
        console.error('Error cargando datos:', error)
    } finally {
        loading.value = false
    }
}

const getEngineerColor = (perfil) => {
    const colors = {
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13'
    }
    return colors[perfil] || '#6b7280'
}

const getStatusClass = (status) => {
    const classes = {
        'En linea': 'status-online',
        'Terminado': 'status-completed',
        'En pausa': 'status-paused',
        'Fuera de linea': 'status-offline'
    }
    return classes[status] || ''
}

const getStatusIcon = (status) => {
    const icons = {
        'En linea': '●',
        'Terminado': '✓',
        'En pausa': '⏸',
        'Fuera de linea': '⚠'
    }
    return icons[status] || '●'
}

const getPhaseStyle = (phase) => {
    const styles = {
        'Diseño': { backgroundColor: '#EEEDFE', color: '#3C3489' },
        'Desarrollo': { backgroundColor: '#E6F1FB', color: '#0C447C' },
        'Implementacion': { backgroundColor: '#E1F5EE', color: '#085041' },
        'Lanzamiento': { backgroundColor: '#FAEEDA', color: '#633806' },
        'Seguimiento': { backgroundColor: '#FAECE7', color: '#712B13' }
    }
    return styles[phase] || {}
}

const getGateLabel = (phase) => {
    const gates = {
        'Diseño': 'GATE 1 - ENTREGA',
        'Desarrollo': 'GATE 2 - ENTREGA',
        'Implementacion': 'GATE 3 - ENTREGA',
        'Lanzamiento': 'GATE 4 - ENTREGA',
        'Seguimiento': 'GATE 5 - ENTREGA'
    }
    return gates[phase] || 'GATE'
}

const getGateStatus = (phase) => {
    return 'Entrega Diseño'
}

const getDeliveryLabel = (date) => {
    if (!date) return 'SIN FECHA'
    const days = getDaysRemainingNum(date)
    if (days < 0) return 'LANZAMIENTO - ENTREGA'
    return 'LANZAMIENTO - ENTREGA'
}

const getDeliveryStatus = (date, status) => {
    if (status === 'Terminado') return 'Completado'
    if (!date) return 'Sin fecha'
    const days = getDaysRemainingNum(date)
    if (days < 0) return 'Vencido'
    return `${days}d días`
}

const formatDate = (date) => {
    if (!date) return 'Sin fecha'
    return new Date(date).toLocaleDateString('es-MX', { day: '2-digit', month: '2-digit', year: 'numeric' })
}

const getDaysRemaining = (date) => {
    if (!date) return 'N/A'
    const days = getDaysRemainingNum(date)
    if (days < 0) return `${Math.abs(days)}d`
    return `${days}d`
}

const getDaysRemainingNum = (date) => {
    if (!date) return 0
    return Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24))
}

const isNearDeadline = (date) => {
    if (!date) return false
    const days = getDaysRemainingNum(date)
    return days <= 30 && days >= 0
}

const isOverdue = (date) => {
    if (!date) return false
    return getDaysRemainingNum(date) < 0
}

const getStatusCircleClass = (status) => {
    if (status === 'En linea') return 'circle-green'
    if (status === 'Fuera de linea') return 'circle-red'
    if (status === 'En pausa') return 'circle-yellow'
    return 'circle-gray'
}

const getStatusBadgeClass = (status) => {
    if (status === 'En linea') return 'badge-green'
    if (status === 'Fuera de linea') return 'badge-red'
    if (status === 'En pausa') return 'badge-yellow'
    if (status === 'Terminado') return 'badge-blue'
    return 'badge-gray'
}

const getStatusLabel = (status) => {
    const labels = {
        'En linea': 'En línea',
        'En pausa': 'En pausa',
        'Fuera de linea': 'Fuera de línea',
        'Terminado': 'Terminado'
    }
    return labels[status] || status
}

const getProgressColor = (progress) => {
    if (progress >= 80) return '#16a34a'
    if (progress >= 50) return '#818cf8'
    if (progress >= 25) return '#d97706'
    return '#dc2626'
}

const getDaysClass = (date) => {
    if (!date) return ''
    const days = getDaysRemainingNum(date)
    if (days < 0) return 'days-overdue'
    if (days <= 7) return 'days-critical'
    if (days <= 30) return 'days-warning'
    return 'days-ok'
}

const getHealthIcon = (project) => {
    if (project.status === 'Terminado') return '✅'
    const days = getDaysRemainingNum(project.phase_date || project.final_date)
    if (days === null) return '⚪'
    if (days < 0) return '🔴'
    if (days <= 7) return '🟠'
    if (days <= 21) return '🟡'
    return '🟢'
}

const getHealthLabel = (project) => {
    if (project.status === 'Terminado') return 'Terminado'
    const days = getDaysRemainingNum(project.phase_date || project.final_date)
    if (days === null) return 'Sin fecha'
    if (days < 0) return `${Math.abs(days)}d venc.`
    return `${days}d`
}

const getHealthColor = (project) => {
    if (project.status === 'Terminado') return '#16a34a'
    const days = getDaysRemainingNum(project.phase_date || project.final_date)
    if (days === null) return '#9ca3af'
    if (days < 0) return '#dc2626'
    if (days <= 7) return '#ea580c'
    if (days <= 21) return '#d97706'
    return '#16a34a'
}

const getStatusPillClass = (status) => {
    if (status === 'En linea') return 'pill-green'
    if (status === 'Fuera de linea') return 'pill-red'
    if (status === 'En pausa') return 'pill-yellow'
    if (status === 'Terminado') return 'pill-blue'
    return 'pill-gray'
}

const getDaysColor = (date) => {
    if (!date) return '#9ca3af'
    const days = getDaysRemainingNum(date)
    if (days < 0) return '#dc2626'
    if (days <= 30) return '#d97706'
    return '#16a34a'
}

const getMilestone = (project) => {
    return `Entrega ${project.active_phase}`
}

const getMilestoneDate = (project) => {
    if (!project.phase_date) return 'Sin fecha'
    return formatDate(project.phase_date)
}

const getMilestoneColor = (project) => {
    if (!project.phase_date) return '#9ca3af'
    const days = getDaysRemainingNum(project.phase_date)
    if (days < 0) return '#dc2626'
    if (days <= 7) return '#ea580c'
    return '#1a1f3c'
}

const viewProject = (project) => {
    // Emit event or navigate to project detail
    console.log('View project:', project)
}

const printReport = () => {
    window.print()
}

onMounted(() => {
    loadData()
})
</script>

<style scoped>
.report-container {
    max-width: 1600px;
    margin: 0 auto;
    min-height: 100vh;
    padding-bottom: 40px;
}

.report-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 24px;
}

.report-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.report-subtitle {
    font-size: 14px;
    color: #6b7280;
}

.header-actions {
    display: flex;
    gap: 12px;
    align-items: center;
}

.current-time {
    font-size: 13px;
    font-weight: 600;
    color: #1a1f3c;
    padding: 10px 16px;
    background: #f3f4f6;
    border-radius: 8px;
}

.btn-refresh {
    padding: 10px 16px;
    background: white;
    color: #1a1f3c;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-refresh:hover {
    background: #f9fafb;
    border-color: #534AB7;
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    background: white;
    cursor: pointer;
}

.btn-print {
    padding: 10px 20px;
    background: #1a1f3c;
    color: white;
    border: none;
    border-radius: 8px;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-print:hover {
    background: #2d3561;
}

.report-title-bar {
    background: #1a1f3c;
    color: white;
    padding: 16px 24px;
    border-radius: 8px 8px 0 0;
    margin-bottom: 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.report-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
    font-size: 14px;
}

.report-date {
    font-size: 12px;
    opacity: 0.8;
}

.report-filters-bar {
    display: flex;
    gap: 12px;
    align-items: center;
}

.filter-select-bar {
    padding: 8px 12px;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    font-size: 13px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    cursor: pointer;
}

.filter-select-bar option {
    background: #1a1f3c;
    color: white;
}

.btn-print-bar {
    padding: 8px 16px;
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    font-size: 13px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-print-bar:hover {
    background: rgba(255, 255, 255, 0.2);
}

.report-date-time {
    font-size: 12px;
    opacity: 0.8;
    white-space: nowrap;
}

.kpi-panel {
    background: #2d3561;
    color: white;
    padding: 24px;
    border-radius: 0 0 8px 8px;
    margin-bottom: 32px;
}

.kpi-header {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 20px;
    opacity: 0.9;
}

.kpi-grid-report {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 20px;
    margin-bottom: 24px;
}

.kpi-item {
    text-align: center;
}

.kpi-label {
    font-size: 11px;
    opacity: 0.7;
    margin-bottom: 8px;
}

.kpi-value-large {
    font-size: 36px;
    font-weight: 800;
    line-height: 1;
    margin-bottom: 4px;
}

.kpi-sublabel {
    font-size: 11px;
    opacity: 0.6;
}

.progress-bar-container {
    margin-top: 20px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 12px;
}

.progress-percentage {
    opacity: 0.8;
}

.progress-bar-full {
    height: 12px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    overflow: hidden;
}

.progress-fill-full {
    height: 100%;
    background: linear-gradient(90deg, #534AB7, #7c3aed);
    transition: width 0.3s;
}

.engineers-section {
    display: flex;
    flex-direction: column;
    gap: 32px;
    width: 100%;
    min-height: auto;
}

.engineer-block {
    background: white;
    border-radius: 12px;
    border: 1px solid #e5e7eb;
    overflow: visible;
    margin-bottom: 24px;
}

.engineer-header {
    background: #f9fafb;
    padding: 20px 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    border-bottom: 2px solid #e5e7eb;
}

.engineer-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 16px;
}

.engineer-info {
    flex: 1;
}

.engineer-name {
    font-size: 18px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.engineer-stats {
    font-size: 13px;
    color: #6b7280;
}

.engineer-summary {
    display: flex;
    gap: 12px;
}

.summary-badge {
    padding: 6px 12px;
    background: #FEF3C7;
    color: #92400E;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 600;
}

/* Table Wrapper */
.table-wrapper {
    overflow-x: auto;
}

.table-wrapper::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.table-wrapper::-webkit-scrollbar-track {
    background: #f3f4f6;
}

.table-wrapper::-webkit-scrollbar-thumb {
    background: #9ca3af;
    border-radius: 4px;
}

.table-wrapper::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Report Table */
.report-table {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
    font-size: 11px;
}

.table-row {
    cursor: pointer;
    transition: background 0.15s;
}

.table-row:hover {
    background: #f9fafb !important;
}

.row-even {
    background: white;
}

.table-row:not(.row-even) {
    background: rgba(0, 0, 0, 0.018);
}

.table-row td {
    padding: 10px 8px;
    border-bottom: 1px solid #e5e7eb;
    vertical-align: middle;
}

/* Cell Styles */
.cell-health {
    width: 52px;
    text-align: center;
}

.health-icon {
    font-size: 15px;
    line-height: 1;
    margin-bottom: 2px;
}

.health-label {
    font-size: 8px;
    font-weight: 700;
    white-space: nowrap;
}

.cell-id {
    width: 68px;
    font-size: 10px;
    font-weight: 700;
    color: #6b7280;
    font-family: 'Courier New', monospace;
}

.cell-name {
    min-width: 200px;
}

.name-main {
    font-size: 12px;
    font-weight: 600;
    color: #1a1f3c;
    margin-bottom: 2px;
}

.name-sub {
    font-size: 10px;
    color: #9ca3af;
}

.cell-status {
    width: 100px;
    text-align: center;
}

.status-pill {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
    white-space: nowrap;
}

.pill-green {
    background: rgba(16, 185, 129, 0.15);
    color: #059669;
}

.pill-red {
    background: rgba(220, 38, 38, 0.15);
    color: #dc2626;
}

.pill-yellow {
    background: rgba(234, 179, 8, 0.15);
    color: #ca8a04;
}

.pill-blue {
    background: rgba(59, 130, 246, 0.15);
    color: #2563eb;
}

.pill-gray {
    background: rgba(156, 163, 175, 0.15);
    color: #6b7280;
}

.cell-progress {
    width: 115px;
}

.progress-container {
    display: flex;
    align-items: center;
    gap: 6px;
}

.progress-bar-mini {
    flex: 1;
    height: 5px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill-mini {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}

.progress-label {
    font-size: 10px;
    font-weight: 600;
    color: #6b7280;
    min-width: 30px;
}

.cell-date {
    width: 92px;
    text-align: center;
    font-size: 10px;
    color: #6b7280;
}

.cell-days {
    width: 68px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
}

.cell-phase {
    width: 110px;
    text-align: center;
}

.phase-pill {
    display: inline-block;
    padding: 3px 8px;
    border-radius: 10px;
    font-size: 10px;
    font-weight: 600;
}

.cell-milestone {
    width: 190px;
}

.milestone-text {
    font-size: 10px;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.02em;
}

.cell-ms-date {
    width: 85px;
    text-align: center;
    font-size: 10px;
    font-weight: 600;
}

/* Status Circle */
.status-circle {
    width: 12px;
    height: 12px;
    border-radius: 50%;
}

.circle-green {
    background: #16a34a;
}

.circle-red {
    background: #dc2626;
}

.circle-yellow {
    background: #eab308;
}

.circle-gray {
    background: #9ca3af;
}

/* Project ID Cell */
.project-id-cell {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    font-family: 'Courier New', monospace;
}

/* Project Name Cell */
.project-name-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.project-name-main {
    font-size: 13px;
    font-weight: 600;
    color: #1a1f3c;
}

.project-engineer-sub {
    font-size: 11px;
    color: #6b7280;
}

/* Status Badge */
.project-status-cell {
    display: flex;
    align-items: center;
}

.status-badge {
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}

.badge-green {
    background: rgba(16, 185, 129, 0.15);
    color: #059669;
}

.badge-red {
    background: rgba(220, 38, 38, 0.15);
    color: #dc2626;
}

.badge-yellow {
    background: rgba(234, 179, 8, 0.15);
    color: #ca8a04;
}

.badge-blue {
    background: rgba(59, 130, 246, 0.15);
    color: #2563eb;
}

.badge-gray {
    background: rgba(156, 163, 175, 0.15);
    color: #6b7280;
}

/* Progress Cell */
.progress-bar-wrapper {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar-track {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}

.progress-text {
    font-size: 11px;
    font-weight: 600;
    color: #6b7280;
    min-width: 35px;
}

/* Dates Cell */
.project-dates-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.date-row {
    font-size: 11px;
    color: #6b7280;
}

.date-overdue {
    color: #dc2626;
    font-weight: 600;
}

.days-remaining {
    font-weight: 600;
}

.days-ok {
    color: #16a34a;
}

.days-warning {
    color: #eab308;
}

.days-critical {
    color: #f97316;
}

.days-overdue {
    color: #dc2626;
}

/* Phase Cell */
.project-phase-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.phase-name {
    font-size: 12px;
    font-weight: 600;
    color: #1a1f3c;
}

.phase-gate {
    font-size: 10px;
    color: #6b7280;
    text-transform: uppercase;
}

/* Delivery Cell */
.project-delivery-cell {
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.delivery-status {
    font-size: 10px;
    color: #6b7280;
    text-transform: uppercase;
}

.delivery-date {
    font-size: 11px;
    font-weight: 600;
    color: #1a1f3c;
}

.delivery-overdue {
    color: #dc2626;
}

.project-status-icon {
    font-size: 20px;
    text-align: center;
}

.status-online {
    color: #0F6E56;
}

.status-completed {
    color: #085041;
}

.status-paused {
    color: #854F0B;
}

.status-offline {
    color: #DC2626;
}

.project-details {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.project-id {
    font-family: 'DM Mono', monospace;
    font-size: 11px;
    color: #6b7280;
}

.project-name-report {
    font-weight: 600;
    color: #1a1f3c;
}

.project-engineer-name {
    font-size: 12px;
    color: #9ca3af;
}

.project-phase {
    display: flex;
    justify-content: center;
}

.phase-badge {
    padding: 4px 12px;
    border-radius: 6px;
    font-size: 11px;
    font-weight: 600;
}

.project-progress-cell {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-bar-small {
    flex: 1;
    height: 6px;
    background: #e5e7eb;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill-small {
    height: 100%;
    background: #534AB7;
    transition: width 0.3s;
}

.progress-text {
    font-size: 12px;
    font-weight: 600;
    color: #6b7280;
    min-width: 35px;
}

.project-dates {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.date-item {
    font-size: 12px;
    color: #6b7280;
}

.date-warning {
    color: #DC2626;
    font-weight: 600;
}

.project-gate, .project-delivery {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.gate-label, .delivery-label {
    font-size: 10px;
    color: #9ca3af;
    text-transform: uppercase;
}

.gate-status, .delivery-date {
    font-size: 12px;
    color: #1a1f3c;
    font-weight: 600;
}

.loading-state {
    text-align: center;
    padding: 60px;
    color: #6b7280;
}

@media print {
    .header-actions {
        display: none;
    }
}
</style>

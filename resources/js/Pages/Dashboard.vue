<template>
    <AppLayout :user="user" :current-view="currentView" @navigate="handleNavigate" @logout="logout">
        <!-- Vista Inicio -->
        <div v-if="currentView === 'inicio'" class="inicio-container">
            <!-- KPIs Strip -->
            <div class="kpi-strip">
                <div class="kpi-card-strip blue" @click="handleNavigate('proyectos')">
                    <div class="kpi-label">Proyectos activos</div>
                    <div class="kpi-value-strip">{{ stats.activeProjects }}</div>
                    <div class="kpi-hint">{{ stats.totalProjects }} totales · {{ stats.efficiency }}% ef.</div>
                    <div class="kpi-arrow">→</div>
                </div>
                <div class="kpi-card-strip ok" @click="handleNavigate('proyectos')">
                    <div class="kpi-label">Terminados</div>
                    <div class="kpi-value-strip">{{ stats.completed }}</div>
                    <div class="kpi-hint">{{ stats.inProgress }} en línea actualmente</div>
                    <div class="kpi-arrow">→</div>
                </div>
                <div :class="['kpi-card-strip', stats.atRisk > 0 ? 'bad' : 'ok']" @click="handleNavigate('proyectos')">
                    <div class="kpi-label">En riesgo</div>
                    <div class="kpi-value-strip">{{ stats.atRisk }}</div>
                    <div class="kpi-hint">Entrega de fase ≤ 7 días</div>
                    <div class="kpi-arrow">→</div>
                </div>
                <div class="kpi-card-strip blue">
                    <div class="kpi-label">SKUs completados</div>
                    <div class="kpi-value-strip">{{ stats.completedSkus }}<span class="kpi-total">/{{ stats.totalSkus }}</span></div>
                    <div class="kpi-hint">Entregables técnicos</div>
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="quick-access-grid">
                <div class="quick-access-card" @click="handleNavigate('reporte')">
                    <div class="quick-icon" style="background: rgba(26,31,60,0.1);">↗</div>
                    <div class="quick-content">
                        <div class="quick-title">Reporte ejecutivo</div>
                        <div class="quick-subtitle">Dashboard filtrable por ingeniero</div>
                    </div>
                </div>
                <div class="quick-access-card" @click="handleNavigate('proyectos')">
                    <div class="quick-icon" style="background: #EEEDFE;">▤</div>
                    <div class="quick-content">
                        <div class="quick-title">Todos los proyectos</div>
                        <div class="quick-subtitle">{{ stats.activeProjects }} activos · {{ stats.completed }} terminados</div>
                    </div>
                </div>
                <div class="quick-access-card" :style="{ borderColor: stats.pendingApprovals > 0 ? 'rgba(217,53,53,0.4)' : '#e4e8ee' }" @click="handleNavigate('aprobaciones')">
                    <div class="quick-icon" :style="{ background: stats.pendingApprovals > 0 ? 'rgba(217,53,53,0.1)' : 'rgba(30,158,94,0.1)' }">✓</div>
                    <div class="quick-content">
                        <div class="quick-title">Aprobaciones</div>
                        <div class="quick-subtitle" :style="{ color: stats.pendingApprovals > 0 ? '#d93535' : '#6b7280' }">
                            {{ stats.pendingApprovals > 0 ? stats.pendingApprovals + ' pendientes de revisión' : 'Sin pendientes' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vista Todos los Proyectos -->
        <div v-else-if="currentView === 'proyectos'" class="proyectos-container">
            <!-- Barra de filtros -->
            <div class="filter-bar">
                <div class="filter-bar-content">
                    <div class="search-input-wrapper">
                        <span class="search-icon">🔍</span>
                        <input 
                            v-model="filters.search" 
                            type="text" 
                            placeholder="Buscar por nombre o ID..." 
                            class="search-input"
                        />
                    </div>
                    <select v-model="filters.engineer" class="filter-select-compact">
                        <option value="">👤 Todos los ingenieros</option>
                        <option v-for="eng in engineers" :key="eng.id" :value="eng.id">{{ eng.name }}</option>
                    </select>
                    <select v-model="filters.phase" class="filter-select-compact">
                        <option value="">📌 Todas las fases</option>
                        <option value="Diseño">Diseño</option>
                        <option value="Desarrollo">Desarrollo</option>
                        <option value="Implementacion">Implementación</option>
                        <option value="Lanzamiento">Lanzamiento</option>
                        <option value="Seguimiento">Seguimiento</option>
                    </select>
                    <select v-model="filters.status" class="filter-select-compact">
                        <option value="">📋 Todos los estatus</option>
                        <option value="En linea">🟢 En línea</option>
                        <option value="En pausa">🟡 En pausa</option>
                        <option value="Fuera de linea">🔴 Fuera de línea</option>
                        <option value="Terminado">✅ Terminado</option>
                    </select>
                    <button v-if="hasActiveFilters" @click="clearFilters" class="btn-clear-filters">
                        ✕ Limpiar
                    </button>
                </div>
                <button v-if="user?.perfil === 'GI'" @click="openCreateModal" class="btn-new-project">
                    ＋ Nuevo proyecto
                </button>
            </div>

            <!-- Contador de resultados -->
            <div class="results-counter">
                <span>{{ filteredProjects.length }} de {{ projects.length }} proyectos</span>
                <span v-if="activeFilterLabels.length" class="filter-labels">
                    Filtrado por: {{ activeFilterLabels.join(' · ') }}
                </span>
            </div>

            <!-- Tabla de proyectos -->
            <div v-if="loading" class="loading-state">Cargando proyectos...</div>
            <div v-else-if="filteredProjects.length === 0" class="empty-state">
                <div class="empty-icon">🔍</div>
                <p>No se encontraron proyectos</p>
            </div>
            <div v-else class="projects-table">
                <div class="projects-table-header">
                    <span>ID</span>
                    <span>Proyecto</span>
                    <span>Estatus</span>
                    <span>Avance</span>
                    <span></span>
                </div>
                <div 
                    v-for="project in filteredProjects" 
                    :key="project.id" 
                    class="projects-table-row"
                    @click="viewProject(project)"
                >
                    <div>
                        <span class="project-id">{{ project.project_id }}</span>
                    </div>
                    <div>
                        <div class="project-name">{{ project.name }}</div>
                        <div class="project-engineer">
                            <span class="engineer-dot" :style="{ background: getEngineerColor(project.engineer_id) }"></span>
                            {{ getEngineerName(project.engineer_id) }}
                        </div>
                    </div>
                    <div>
                        <span :class="['status-tag', getStatusClass(project.status)]">{{ getStatusLabel(project.status) }}</span>
                        <div class="project-phase">{{ project.active_phase || 'Diseño' }}</div>
                    </div>
                    <div>
                        <div class="progress-bar-container">
                            <div class="progress-track">
                                <div 
                                    class="progress-fill" 
                                    :style="{ width: project.progress + '%', background: getProgressColor(project.progress) }"
                                ></div>
                            </div>
                            <span class="progress-percent" :style="{ color: getProgressColor(project.progress) }">
                                {{ project.progress }}%
                            </span>
                        </div>
                        <div v-if="getProjectDaysUntil(project)" class="days-chip" :class="getDaysChipClass(project)">
                            {{ getProjectDaysUntil(project) }}
                        </div>
                    </div>
                    <div class="row-arrow">›</div>
                </div>
            </div>
        </div>

        <!-- Vista Mis Proyectos (para ingenieros) -->
        <div v-else-if="currentView === 'mis-proyectos'" class="view-container">
            <!-- KPIs Strip -->
            <div class="kpi-strip">
                <div class="kpi-card-strip blue" @click="myProjectsFilter = 'curso'" style="cursor: pointer;">
                    <div class="kpi-label">En curso</div>
                    <div class="kpi-value-strip">{{ myProjectsStats.active }}</div>
                    <div class="kpi-hint">proyectos activos</div>
                </div>
                <div class="kpi-card-strip ok">
                    <div class="kpi-label">Terminados</div>
                    <div class="kpi-value-strip">{{ myProjectsStats.completed }}</div>
                    <div class="kpi-hint">completados</div>
                </div>
                <div :class="['kpi-card-strip', myProjectsStats.atRisk > 0 ? 'bad' : 'ok']" @click="myProjectsFilter = 'riesgo'" style="cursor: pointer;">
                    <div class="kpi-label">En riesgo</div>
                    <div class="kpi-value-strip">{{ myProjectsStats.atRisk }}</div>
                    <div class="kpi-hint">entrega ≤ 7 días</div>
                </div>
                <div class="kpi-card-strip blue">
                    <div class="kpi-label">Avance promedio</div>
                    <div class="kpi-value-strip">{{ myProjectsStats.avgProgress }}%</div>
                    <div class="kpi-hint">de todos mis proyectos</div>
                </div>
            </div>

            <!-- Filtro Tabs -->
            <div class="filter-tabs-container">
                <div class="filter-tabs">
                    <button 
                        @click="myProjectsFilter = 'curso'"
                        :class="['filter-tab', myProjectsFilter === 'curso' ? 'active purple' : '']"
                    >
                        En curso ({{ myProjectsStats.active }})
                    </button>
                    <button 
                        @click="myProjectsFilter = 'riesgo'"
                        :class="['filter-tab', myProjectsFilter === 'riesgo' ? 'active bad' : '']"
                    >
                        En riesgo ({{ myProjectsStats.atRisk }})
                    </button>
                    <button 
                        @click="myProjectsFilter = 'todos'"
                        :class="['filter-tab', myProjectsFilter === 'todos' ? 'active navy' : '']"
                    >
                        Todos ({{ myProjects.filter(p => p.status !== 'Cancelado').length }})
                    </button>
                </div>
                <button class="btn-new-project" @click="openNewProjectModal">
                    ＋ Nuevo proyecto
                </button>
            </div>

            <div v-if="loading" class="loading-state">Cargando proyectos...</div>
            <div v-else-if="filteredMyProjects.length === 0" class="empty-state">
                <div class="empty-icon">📁</div>
                <p>No hay proyectos en esta categoría</p>
            </div>
            <div v-else class="projects-grid">
                <ProjectCard 
                    v-for="project in filteredMyProjects" 
                    :key="project.id" 
                    :project="project"
                    @click="viewProject(project)"
                />
            </div>
        </div>

        <!-- Vista Aprobaciones -->
        <GateApprovalsView v-else-if="currentView === 'aprobaciones'" :user="user" />

        <!-- Vista Reporte Ejecutivo -->
        <ExecutiveReportView v-else-if="currentView === 'reporte'" :user="user" />

        <!-- Vista Indicadores NPD -->
        <IndicatorsView v-else-if="currentView === 'indicadores'" :user="user" />

        <!-- Vista Diagrama de Proceso -->
        <ProcessDiagramView v-else-if="currentView === 'diagrama'" />

        <!-- Vista Gestión de Usuarios -->
        <UsersManagementView v-else-if="currentView === 'usuarios'" />

        <!-- Otras vistas (placeholder) -->
        <div v-else class="view-container">
            <div class="view-header">
                <h1 class="view-title">{{ getViewTitle() }}</h1>
            </div>
            <div class="empty-state">
                <div class="empty-icon">🚧</div>
                <p>Esta sección está en desarrollo</p>
            </div>
        </div>
    </AppLayout>

    <!-- Modal de Proyecto -->
    <ProjectModal 
        :show="showProjectModal"
        :project="selectedProject"
        :engineers="engineers"
        @close="showProjectModal = false"
        @saved="handleProjectSaved"
    />
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'
import AppLayout from '../Components/AppLayout.vue'
import ProjectCard from '../Components/ProjectCard.vue'
import ProjectModal from '../Components/ProjectModal.vue'
import GateApprovalsView from '../Components/GateApprovalsView.vue'
import ExecutiveReportView from '../Components/ExecutiveReportView.vue'
import IndicatorsView from '../Components/IndicatorsView.vue'
import ProcessDiagramView from '../Components/ProcessDiagramView.vue'
import UsersManagementView from '../Components/UsersManagementView.vue'

const user = ref(null)
const projects = ref([])
const engineers = ref([])
const loading = ref(false)
const currentView = ref('inicio')
const showProjectModal = ref(false)
const selectedProject = ref(null)
const myProjectsFilter = ref('curso')

const filters = ref({
    search: '',
    engineer: '',
    status: '',
    phase: ''
})

const stats = computed(() => {
    if (!projects.value || projects.value.length === 0) {
        return {
            totalProjects: 0,
            activeProjects: 0,
            inProgress: 0,
            completed: 0,
            atRisk: 0,
            totalSkus: 0,
            completedSkus: 0,
            efficiency: 0,
            pendingApprovals: 0
        }
    }
    
    const all = projects.value.filter(p => p.status !== 'Cancelado')
    const active = projects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
    const completed = projects.value.filter(p => p.status === 'Terminado')
    const inProgress = projects.value.filter(p => p.status === 'En linea')
    const atRisk = active.filter(p => isProjectAtRisk(p))
        
    // Calcular SKUs
    const totalSkus = projects.value.reduce((sum, p) => sum + (p.skus?.length || 0), 0)
    const completedSkus = projects.value.reduce((sum, p) => {
        return sum + (p.skus?.filter(s => s.status === 'Completado').length || 0)
    }, 0)
        
    // Calcular eficiencia
    const efficiency = all.length > 0 ? Math.round((completed.length / all.length) * 100) : 0
    
    // Proyectos pendientes de aprobación (progreso >= 80%)
    const pendingApprovals = active.filter(p => p.progress >= 80).length
    
    return {
        totalProjects: all.length,
        activeProjects: active.length,
        inProgress: inProgress.length,
        completed: completed.length,
        atRisk: atRisk.length,
        totalSkus,
        completedSkus,
        efficiency,
        pendingApprovals
    }
})

const recentProjects = computed(() => {
    return projects.value.slice(0, 6)
})

const myProjects = computed(() => {
    if (!user.value) return []
    return projects.value.filter(p => p.engineer_id === user.value.id)
})

const myProjectsStats = computed(() => {
    if (!user.value || myProjects.value.length === 0) {
        return {
            active: 0,
            completed: 0,
            atRisk: 0,
            avgProgress: 0
        }
    }
    
    const active = myProjects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
    const completed = myProjects.value.filter(p => p.status === 'Terminado')
    const atRisk = active.filter(p => isProjectAtRisk(p))
    const avgProgress = myProjects.value.length > 0
        ? Math.round(myProjects.value.reduce((sum, p) => sum + (p.progress || 0), 0) / myProjects.value.length)
        : 0
    
    return {
        active: active.length,
        completed: completed.length,
        atRisk: atRisk.length,
        avgProgress
    }
})

const filteredMyProjects = computed(() => {
    if (!myProjects.value.length) return []
    
    const active = myProjects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
    const atRisk = active.filter(p => isProjectAtRisk(p))
    const all = myProjects.value.filter(p => p.status !== 'Cancelado')
    
    if (myProjectsFilter.value === 'curso') return active
    if (myProjectsFilter.value === 'riesgo') return atRisk
    if (myProjectsFilter.value === 'todos') return all
    
    return active
})

const filteredProjects = computed(() => {
    let result = projects.value
    
    // Filtro de búsqueda
    if (filters.value.search) {
        const query = filters.value.search.toLowerCase()
        result = result.filter(p => 
            p.name.toLowerCase().includes(query) || 
            p.project_id.toLowerCase().includes(query)
        )
    }
    
    // Filtro de ingeniero
    if (filters.value.engineer) {
        result = result.filter(p => p.engineer_id === parseInt(filters.value.engineer))
    }
    
    // Filtro de estatus
    if (filters.value.status) {
        result = result.filter(p => p.status === filters.value.status)
    }
    
    // Filtro de fase
    if (filters.value.phase) {
        result = result.filter(p => p.active_phase === filters.value.phase)
    }
    
    return result
})

const hasActiveFilters = computed(() => {
    return filters.value.search || filters.value.engineer || filters.value.status || filters.value.phase
})

const activeFilterLabels = computed(() => {
    const labels = []
    if (filters.value.search) labels.push(`"${filters.value.search}"`)
    if (filters.value.engineer) {
        const eng = engineers.value.find(e => e.id === parseInt(filters.value.engineer))
        if (eng) labels.push(eng.name)
    }
    if (filters.value.phase) labels.push(filters.value.phase)
    if (filters.value.status) labels.push(getStatusLabel(filters.value.status))
    return labels
})

const isProjectAtRisk = (project) => {
    if (!project.final_date || project.status === 'Terminado') return false
    const daysUntil = Math.ceil((new Date(project.final_date) - new Date()) / (1000 * 60 * 60 * 24))
    return daysUntil <= 7 && daysUntil >= 0
}

const loadProjects = async () => {
    loading.value = true
    try {
        const response = await axios.get('/api/projects')
        projects.value = response.data
    } catch (error) {
        console.error('Error cargando proyectos:', error)
    } finally {
        loading.value = false
    }
}

const handleNavigate = (viewId) => {
    currentView.value = viewId
}

const viewProject = (project) => {
    selectedProject.value = project
    showProjectModal.value = true
}

const openNewProjectModal = () => {
    selectedProject.value = null
    showProjectModal.value = true
}

const openCreateModal = () => {
    selectedProject.value = null
    showProjectModal.value = true
}

const handleProjectSaved = () => {
    loadProjects()
    showProjectModal.value = false
}

const loadEngineers = async () => {
    try {
        const response = await axios.get('/api/users/engineers')
        engineers.value = response.data
    } catch (error) {
        console.error('Error cargando ingenieros:', error)
    }
}

const getViewTitle = () => {
    const titles = {
        'inicio': 'Inicio',
        'proyectos': 'Todos los proyectos',
        'reporte': 'Reporte ejecutivo',
        'aprobaciones': 'Aprobaciones de gates',
        'mis-proyectos': 'Mis proyectos',
        'mis-tareas': 'Mis tareas',
        'indicadores': 'Indicadores NPD',
        'diagrama': 'Diagrama de proceso',
        'usuarios': 'Gestión de usuarios',
        'respaldo': 'Respaldo de datos'
    }
    return titles[currentView.value] || 'Dashboard'
}

const logout = async () => {
    try {
        await axios.post('/api/logout')
    } catch (error) {
        console.error('Error al cerrar sesión:', error)
    } finally {
        localStorage.removeItem('token')
        localStorage.removeItem('user')
        delete axios.defaults.headers.common['Authorization']
        router.get('/')
    }
}

// Helper functions para la tabla de proyectos
const clearFilters = () => {
    filters.value.search = ''
    filters.value.engineer = ''
    filters.value.status = ''
    filters.value.phase = ''
}

const getEngineerName = (engineerId) => {
    const engineer = engineers.value.find(e => e.id === engineerId)
    return engineer?.name || 'Sin asignar'
}

const getEngineerColor = (engineerId) => {
    const engineer = engineers.value.find(e => e.id === engineerId)
    const colors = {
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13'
    }
    return colors[engineer?.perfil] || '#9ca3af'
}

const getStatusLabel = (status) => {
    const labels = {
        'En linea': 'En línea',
        'En pausa': 'En pausa',
        'Fuera de linea': 'Fuera de línea',
        'Terminado': 'Terminado',
        'Cancelado': 'Cancelado'
    }
    return labels[status] || status
}

const getStatusClass = (status) => {
    const classes = {
        'En linea': 'status-active',
        'En pausa': 'status-paused',
        'Fuera de linea': 'status-offline',
        'Terminado': 'status-completed',
        'Cancelado': 'status-canceled'
    }
    return classes[status] || ''
}

const getProgressColor = (progress) => {
    if (progress >= 80) return '#16a34a'
    if (progress >= 50) return '#818cf8'
    if (progress >= 25) return '#d97706'
    return '#dc2626'
}

const getProjectDaysUntil = (project) => {
    if (!project.phase_date || project.status === 'Terminado') return null
    const daysUntil = Math.ceil((new Date(project.phase_date) - new Date()) / (1000 * 60 * 60 * 24))
    if (daysUntil < 0) return `${Math.abs(daysUntil)}d venc.`
    if (daysUntil <= 21) return `${daysUntil}d`
    return null
}

const getDaysChipClass = (project) => {
    if (!project.phase_date || project.status === 'Terminado') return ''
    const daysUntil = Math.ceil((new Date(project.phase_date) - new Date()) / (1000 * 60 * 60 * 24))
    if (daysUntil < 0) return 'days-overdue'
    if (daysUntil <= 7) return 'days-critical'
    if (daysUntil <= 14) return 'days-warning'
    return 'days-ok'
}

onMounted(() => {
    const userData = localStorage.getItem('user')
    if (userData) {
        user.value = JSON.parse(userData)
        
        // Determinar vista inicial según perfil del usuario
        const perfil = user.value?.perfil
        if (perfil && perfil !== 'GI') {
            // Ingenieros de Producto (RB, FR, AE, PR, RE)
            if (['RB', 'FR', 'AE', 'PR', 'RE'].includes(perfil)) {
                currentView.value = 'mis-proyectos'
            }
            // Áreas de soporte (CAL, COS, PRO)
            else if (['CAL', 'COS', 'PRO'].includes(perfil)) {
                currentView.value = 'mis-tareas-area'
            }
        }
        // Gerente (GI) mantiene vista 'inicio' por defecto
    }
    loadProjects()
    loadEngineers()
})

watch(currentView, () => {
    if (currentView.value === 'proyectos' || currentView.value === 'mis-proyectos') {
        loadProjects()
    }
})
</script>

<style scoped>
.view-container {
    max-width: 1400px;
    margin: 0 auto;
}

.view-header {
    margin-bottom: 32px;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.view-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.view-subtitle {
    font-size: 14px;
    color: #6b7280;
}

.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 20px;
    margin-bottom: 40px;
}

.kpi-card {
    background: white;
    border-radius: 12px;
    padding: 24px;
    display: flex;
    align-items: center;
    gap: 16px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
}

.kpi-icon {
    width: 56px;
    height: 56px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    flex-shrink: 0;
}

.kpi-content {
    flex: 1;
}

.kpi-value {
    font-size: 32px;
    font-weight: 800;
    color: #1a1f3c;
    line-height: 1;
}

.kpi-label {
    font-size: 13px;
    color: #6b7280;
    margin-top: 4px;
}

.section {
    margin-bottom: 40px;
}

.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.section-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1f3c;
}

.btn-link {
    color: #534AB7;
    font-size: 14px;
    font-weight: 600;
    background: none;
    border: none;
    cursor: pointer;
    transition: color 0.2s;
}

.btn-link:hover {
    color: #3C3489;
}

.btn-primary {
    background-color: #534AB7;
    color: white;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    background-color: #3C3489;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(83, 74, 183, 0.3);
}

.filters {
    display: flex;
    gap: 12px;
    margin-bottom: 24px;
}

.filter-select {
    padding: 10px 16px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    color: #374151;
    background-color: white;
    cursor: pointer;
    transition: border-color 0.2s;
}

.filter-select:hover {
    border-color: #534AB7;
}

.filter-select:focus {
    outline: none;
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.projects-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 20px;
}

.loading-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
    font-size: 14px;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
}

/* Vista Inicio */
.inicio-container {
    max-width: 1400px;
    margin: 0 auto;
}

.kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.kpi-card-strip {
    background: white;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    padding: 14px 16px;
    cursor: pointer;
    transition: all 0.15s;
    position: relative;
}

.kpi-card-strip:hover {
    box-shadow: 0 2px 12px rgba(26, 34, 51, 0.08);
}

.kpi-card-strip.blue {
    border-top: 3px solid #185FA5;
}

.kpi-card-strip.ok {
    border-top: 3px solid #0F6E56;
}

.kpi-card-strip.bad {
    border-top: 3px solid #d93535;
}

.kpi-label {
    font-size: 11px;
    font-weight: 700;
    color: #4a5568;
    margin-bottom: 6px;
}

.kpi-value-strip {
    font-size: 32px;
    font-weight: 800;
    font-family: 'Syne', sans-serif;
    color: #1a1f3c;
    line-height: 1;
    margin-bottom: 4px;
}

.kpi-total {
    font-size: 15px;
    color: #6b7280;
    font-weight: 500;
}

.kpi-hint {
    font-size: 10px;
    color: #6b7280;
}

.kpi-arrow {
    position: absolute;
    top: 14px;
    right: 16px;
    font-size: 18px;
    color: #9ca3af;
}

.quick-access-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 10px;
    margin-bottom: 20px;
}

.quick-access-card {
    background: white;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    padding: 16px 18px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 13px;
    transition: box-shadow 0.15s;
}

.quick-access-card:hover {
    box-shadow: 0 2px 12px rgba(26, 34, 51, 0.08);
}

.quick-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.quick-content {
    flex: 1;
}

.quick-title {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 2px;
    color: #1a1f3c;
}

.quick-subtitle {
    font-size: 11px;
    color: #6b7280;
}

/* Vista Todos los Proyectos - Tabla */
.proyectos-container {
    max-width: 1400px;
    margin: 0 auto;
}

.filter-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    margin-bottom: 12px;
    flex-wrap: wrap;
}

.filter-bar-content {
    flex: 1;
    background: #f8f9fb;
    border: 1px solid #e4e8ee;
    border-radius: 12px;
    padding: 9px 12px;
    display: flex;
    gap: 7px;
    min-width: 0;
}

.search-input-wrapper {
    position: relative;
    flex: 1;
    min-width: 140px;
}

.search-icon {
    position: absolute;
    left: 8px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 12px;
    color: #6b7280;
    pointer-events: none;
}

.search-input {
    width: 100%;
    padding: 5px 9px 5px 26px;
    font-size: 12px;
    border: 1px solid #e4e8ee;
    border-radius: 8px;
    background: white;
    color: #1a1f3c;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    height: 30px;
}

.search-input:focus {
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.filter-select-compact {
    font-size: 12px;
    padding: 5px 9px;
    border: 1px solid #e4e8ee;
    border-radius: 8px;
    background: white;
    color: #1a1f3c;
    font-family: 'DM Sans', sans-serif;
    outline: none;
    height: 30px;
    cursor: pointer;
}

.filter-select-compact:hover {
    border-color: #534AB7;
}

.filter-select-compact:focus {
    border-color: #534AB7;
    box-shadow: 0 0 0 3px rgba(83, 74, 183, 0.1);
}

.btn-clear-filters {
    font-size: 11px;
    font-weight: 700;
    padding: 5px 10px;
    border-radius: 8px;
    border: 1px solid #e4e8ee;
    background: white;
    color: #d93535;
    cursor: pointer;
    height: 30px;
    white-space: nowrap;
}

.btn-clear-filters:hover {
    background: #fef2f2;
    border-color: #d93535;
}

.btn-new-project {
    padding: 8px 18px;
    background: #1a1f3c;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    font-family: 'DM Sans', sans-serif;
    white-space: nowrap;
    flex-shrink: 0;
}

.btn-new-project:hover {
    background: #0f1420;
    box-shadow: 0 2px 8px rgba(26, 31, 60, 0.2);
}

.results-counter {
    font-size: 11px;
    color: #6b7280;
    margin-bottom: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
}

.filter-labels {
    color: #1a1f3c;
    font-size: 10px;
}

.projects-table {
    background: white;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    overflow: hidden;
}

.projects-table-header {
    display: grid;
    grid-template-columns: 100px 1fr 180px 200px 30px;
    gap: 12px;
    padding: 10px 16px;
    background: #f8f9fb;
    border-bottom: 1px solid #e4e8ee;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.projects-table-row {
    display: grid;
    grid-template-columns: 100px 1fr 180px 200px 30px;
    gap: 12px;
    padding: 12px 16px;
    border-bottom: 1px solid #f3f4f6;
    cursor: pointer;
    transition: background 0.15s;
    align-items: center;
}

.projects-table-row:last-child {
    border-bottom: none;
}

.projects-table-row:hover {
    background: #f8f9fb;
}

.project-id {
    font-size: 11px;
    font-weight: 700;
    font-family: 'Courier New', monospace;
    color: #6b7280;
    background: #f3f4f6;
    padding: 3px 8px;
    border-radius: 6px;
    display: inline-block;
}

.project-name {
    font-size: 13px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 3px;
}

.project-engineer {
    font-size: 11px;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 6px;
}

.engineer-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
}

.status-tag {
    font-size: 11px;
    font-weight: 700;
    padding: 4px 10px;
    border-radius: 99px;
    display: inline-block;
}

.status-active {
    background: rgba(16, 185, 129, 0.1);
    color: #059669;
}

.status-paused {
    background: rgba(251, 191, 36, 0.1);
    color: #d97706;
}

.status-offline {
    background: rgba(239, 68, 68, 0.1);
    color: #dc2626;
}

.status-completed {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
}

.status-canceled {
    background: rgba(156, 163, 175, 0.1);
    color: #6b7280;
}

.project-phase {
    font-size: 10px;
    color: #6b7280;
    margin-top: 3px;
}

.progress-bar-container {
    display: flex;
    align-items: center;
    gap: 8px;
}

.progress-track {
    flex: 1;
    height: 5px;
    background: #e4e8ee;
    border-radius: 3px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 3px;
    transition: width 0.3s;
}

.progress-percent {
    font-size: 10px;
    font-weight: 700;
    min-width: 32px;
    text-align: right;
}

.days-chip {
    font-size: 10px;
    font-weight: 700;
    padding: 2px 6px;
    border-radius: 4px;
    margin-top: 4px;
    display: inline-block;
}

.days-ok {
    background: rgba(34, 197, 94, 0.1);
    color: #16a34a;
}

.days-warning {
    background: rgba(234, 179, 8, 0.1);
    color: #ca8a04;
}

.days-critical {
    background: rgba(234, 88, 12, 0.1);
    color: #ea580c;
}

.days-overdue {
    background: rgba(220, 38, 38, 0.1);
    color: #dc2626;
}

.row-arrow {
    font-size: 16px;
    color: #9ca3af;
}

/* KPI Strip - Diseño de referencia */
.kpi-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 24px;
}

.kpi-card-strip {
    background: white;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    padding: 14px 15px;
    position: relative;
    overflow: hidden;
    transition: box-shadow 0.15s;
}

.kpi-card-strip:hover {
    box-shadow: 0 2px 12px rgba(26, 34, 51, 0.08);
}

.kpi-card-strip::before {
    content: '';
    position: absolute;
    inset: 0;
    border-radius: 14px;
    opacity: 0;
}

.kpi-card-strip.ok {
    border-color: rgba(30, 158, 94, 0.3);
}

.kpi-card-strip.ok::before {
    background: rgba(30, 158, 94, 0.10);
    opacity: 1;
}

.kpi-card-strip.bad {
    border-color: rgba(217, 53, 53, 0.3);
}

.kpi-card-strip.bad::before {
    background: rgba(217, 53, 53, 0.10);
    opacity: 1;
}

.kpi-card-strip.blue {
    border-color: rgba(26, 31, 60, 0.2);
}

.kpi-card-strip.blue::before {
    background: rgba(26, 31, 60, 0.05);
    opacity: 1;
}

.kpi-label {
    font-size: 10px;
    color: #4a5568;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
    position: relative;
}

.kpi-value-strip {
    font-family: 'Syne', sans-serif;
    font-size: 28px;
    font-weight: 800;
    margin: 4px 0 2px;
    position: relative;
    line-height: 1;
}

.kpi-card-strip.ok .kpi-value-strip {
    color: #1e9e5e;
}

.kpi-card-strip.bad .kpi-value-strip {
    color: #d93535;
}

.kpi-card-strip.blue .kpi-value-strip {
    color: #1a1f3c;
}

.kpi-hint {
    font-size: 11px;
    color: #4a5568;
    position: relative;
}

/* Filter Tabs */
.filter-tabs-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
    gap: 12px;
    flex-wrap: wrap;
}

.filter-tabs {
    display: flex;
    gap: 6px;
    flex-wrap: wrap;
}

.filter-tab {
    padding: 6px 16px;
    border-radius: 99px;
    border: 2px solid #d8dde5;
    background: white;
    color: #1a2233;
    font-size: 11px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.15s;
    font-family: inherit;
}

.filter-tab:hover {
    border-color: #534AB7;
}

.filter-tab.active {
    color: white;
}

.filter-tab.active.purple {
    background: #534AB7;
    border-color: #534AB7;
}

.filter-tab.active.bad {
    background: #d93535;
    border-color: #d93535;
}

.filter-tab.active.navy {
    background: #1a1f3c;
    border-color: #1a1f3c;
}

.btn-new-project {
    padding: 7px 16px;
    background: #1a1f3c;
    color: white;
    border: none;
    border-radius: 99px;
    font-size: 12px;
    font-weight: 700;
    cursor: pointer;
    font-family: inherit;
    white-space: nowrap;
    transition: background 0.15s;
}

.btn-new-project:hover {
    background: #2d3561;
}
</style>
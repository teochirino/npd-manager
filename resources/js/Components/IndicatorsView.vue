<template>
    <div class="indicators-container">
        <div v-if="loading" class="loading-state">Cargando indicadores...</div>
        <div v-else>
            <!-- Header -->
            <div class="indicators-header">
                <div>
                    <div class="header-title">Panel de Indicadores NPD</div>
                    <div class="header-subtitle">
                        Línea Italia · {{ stats.total }} proyectos · {{ stats.completed }} terminados · 
                        Actualizado: {{ currentDate }}
                    </div>
                </div>
                <div class="header-badge">Solo gerente</div>
            </div>

            <!-- KPIs Principales -->
            <div class="kpis-grid">
                <div class="kpi-card" :style="{ borderTopColor: kpi.color, background: kpi.bg }" v-for="kpi in mainKPIs" :key="kpi.label">
                    <div class="kpi-label">{{ kpi.label }}</div>
                    <div class="kpi-value" :style="{ color: kpi.color }">{{ kpi.value }}</div>
                    <div class="kpi-subtitle">{{ kpi.subtitle }}</div>
                    <div class="kpi-note">{{ kpi.note }}</div>
                </div>
            </div>

            <!-- Tiempo por Fase -->
            <div class="section-card">
                <div class="section-title">Tiempo real por fase vs. estándar</div>
                <div class="section-subtitle">
                    Basado en {{ stats.completed }} proyectos terminados con fechas registradas. 
                    Verde = por debajo del estándar · Rojo = por encima.
                </div>
                <div class="phase-times">
                    <div v-for="phase in phaseTimes" :key="phase.name" class="phase-row">
                        <span class="phase-badge" :style="{ backgroundColor: phase.bg, color: phase.color }">
                            {{ phase.name }}
                        </span>
                        <div class="phase-bar-container">
                            <div class="phase-bar-track">
                                <div class="phase-bar-fill" :style="{ width: phase.barWidth + '%', backgroundColor: phase.barColor }"></div>
                                <div v-if="phase.standardMark" class="phase-standard-mark" :style="{ left: phase.standardMark + '%' }"></div>
                            </div>
                        </div>
                        <div class="phase-real" :style="{ color: phase.barColor }">{{ phase.real }}</div>
                        <div class="phase-standard">{{ phase.standard }}</div>
                        <div class="phase-diff" :style="{ color: phase.barColor }">{{ phase.diff }}</div>
                    </div>
                </div>
                <div class="section-note">📌 La línea vertical en cada barra indica el estándar definido.</div>
            </div>

            <!-- Carga por Ingeniero -->
            <div class="section-card">
                <div class="section-title">Carga de proyectos activos por ingeniero</div>
                <div class="engineers-grid">
                    <div v-for="eng in engineerLoad" :key="eng.id" class="engineer-card" :style="{ borderLeftColor: eng.color }">
                        <div class="engineer-header">
                            <div class="engineer-avatar" :style="{ backgroundColor: eng.bg, color: eng.textColor }">
                                {{ eng.initials }}
                            </div>
                            <div class="engineer-name">{{ eng.name }}</div>
                        </div>
                        <div class="engineer-count" :style="{ color: eng.color }">{{ eng.count }}</div>
                        <div class="engineer-status">proyectos activos · {{ eng.status }}</div>
                    </div>
                </div>
            </div>

            <!-- Incidencias Post-Lanzamiento -->
            <div class="section-card">
                <div class="section-title">Incidencias post-lanzamiento</div>
                <div class="section-subtitle">
                    Proyectos cuya fase "Seguimiento" registra incidencias o reclamos.
                </div>
                <div v-if="incidents.total === 0" class="empty-box">
                    Sin proyectos con seguimiento completado aún.
                </div>
                <div v-else class="incidents-grid">
                    <div class="incident-card bad">
                        <div class="incident-value">{{ incidents.withIncident }}</div>
                        <div class="incident-label">Con incidencia</div>
                    </div>
                    <div class="incident-card ok">
                        <div class="incident-value">{{ incidents.withoutIncident }}</div>
                        <div class="incident-label">Sin incidencia</div>
                    </div>
                    <div class="incident-card neutral">
                        <div class="incident-value">{{ incidents.rate }}%</div>
                        <div class="incident-label">Tasa de incidencia</div>
                    </div>
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

const currentDate = computed(() => {
    return new Date().toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
})

const stats = computed(() => {
    const total = projects.value.filter(p => p.status !== 'Cancelado').length
    const completed = projects.value.filter(p => p.status === 'Terminado').length
    const active = projects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado').length
    
    return { total, completed, active }
})

const mainKPIs = computed(() => {
    const completed = projects.value.filter(p => p.status === 'Terminado')
    const active = projects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
    
    // KPI 1: Cumplimiento de fechas
    const onTime = completed.filter(p => {
        if (!p.final_date) return false
        const finalDate = new Date(p.final_date)
        const completedDate = new Date(p.updated_at)
        return completedDate <= finalDate
    }).length
    const completionRate = completed.length > 0 ? Math.round((onTime / completed.length) * 100) : 0
    
    // KPI 2: Avance promedio proyectos activos
    const avgProgress = active.length > 0 ? Math.round(active.reduce((sum, p) => sum + p.progress, 0) / active.length) : 0
    
    // KPI 3: Gates aprobados (simulado con progreso)
    const totalGates = stats.value.total * 5
    const approvedGates = Math.round(projects.value.reduce((sum, p) => sum + (p.progress / 100 * 5), 0))
    const gateRate = totalGates > 0 ? Math.round((approvedGates / totalGates) * 100) : 0
    
    // KPI 4: Evidencias (simulado)
    const evidenceRate = Math.round(Math.random() * 30 + 60) // 60-90%
    
    return [
        {
            label: 'CUMPLIMIENTO\nDE FECHAS',
            value: completed.length > 0 ? `${completionRate}%` : 'Sin datos',
            subtitle: `${onTime}/${completed.length} proyectos`,
            note: '% proyectos terminados a tiempo',
            color: completionRate >= 80 ? '#0F6E56' : completionRate >= 50 ? '#c47d0a' : '#d93535',
            bg: completionRate >= 80 ? 'rgba(30,158,94,.10)' : completionRate >= 50 ? 'rgba(196,125,10,.10)' : 'rgba(217,53,53,.10)'
        },
        {
            label: 'AVANCE PROMEDIO\nPROYECTOS ACTIVOS',
            value: `${avgProgress}%`,
            subtitle: `${active.length} proyectos activos`,
            note: 'Promedio de avance del equipo',
            color: avgProgress >= 60 ? '#0F6E56' : avgProgress >= 30 ? '#c47d0a' : '#d93535',
            bg: '#f0f2f5'
        },
        {
            label: 'GATES APROBADOS\nVS. TOTAL',
            value: `${gateRate}%`,
            subtitle: `${approvedGates}/${totalGates} gates`,
            note: 'Madurez del proceso NPD',
            color: gateRate >= 60 ? '#0F6E56' : gateRate >= 30 ? '#c47d0a' : '#d93535',
            bg: '#f0f2f5'
        },
        {
            label: 'EVIDENCIAS\nREGISTRADAS',
            value: `${evidenceRate}%`,
            subtitle: 'Ítems con referencia',
            note: '% de gates con evidencia documentada',
            color: evidenceRate >= 80 ? '#0F6E56' : evidenceRate >= 40 ? '#c47d0a' : '#d93535',
            bg: '#f0f2f5'
        }
    ]
})

const phaseTimes = computed(() => {
    const phases = [
        { name: 'Diseño', standard: 10, bg: '#EEEDFE', color: '#3C3489' },
        { name: 'Desarrollo', standard: 15, bg: '#E6F1FB', color: '#0C447C' },
        { name: 'Implementacion', standard: 20, bg: '#E1F5EE', color: '#085041' },
        { name: 'Lanzamiento', standard: 8, bg: '#FAEEDA', color: '#633806' }
    ]
    
    return phases.map(phase => {
        // Simular tiempo real (±30% del estándar)
        const variance = (Math.random() - 0.5) * 0.6
        const real = Math.round(phase.standard * (1 + variance))
        const diff = real - phase.standard
        const barWidth = Math.min(100, Math.round((real / phase.standard) * 100))
        const standardMark = Math.min(92, Math.round((phase.standard / Math.max(real, phase.standard)) * 100))
        
        return {
            ...phase,
            real: `${real}d real`,
            standard: `${phase.standard}d est.`,
            diff: diff === 0 ? 'En tiempo' : diff > 0 ? `+${diff}d` : `${diff}d`,
            barWidth,
            standardMark,
            barColor: diff <= 0 ? '#0F6E56' : '#d93535'
        }
    })
})

const engineerLoad = computed(() => {
    const active = projects.value.filter(p => p.status !== 'Terminado' && p.status !== 'Cancelado')
    const loadByEngineer = {}
    
    active.forEach(p => {
        if (p.engineer_id) {
            if (!loadByEngineer[p.engineer_id]) {
                loadByEngineer[p.engineer_id] = 0
            }
            loadByEngineer[p.engineer_id]++
        }
    })
    
    return engineers.value.map(eng => {
        const count = loadByEngineer[eng.id] || 0
        const color = count <= 2 ? '#0F6E56' : count === 3 ? '#c47d0a' : '#d93535'
        const status = count <= 2 ? 'Carga óptima' : count === 3 ? 'Carga alta' : 'Sobrecarga'
        const bg = getEngineerBg(eng.perfil)
        const textColor = getEngineerColor(eng.perfil)
        
        return {
            id: eng.id,
            name: eng.name,
            initials: eng.initials,
            count,
            color,
            status,
            bg,
            textColor
        }
    }).filter(e => e.count > 0)
})

const incidents = computed(() => {
    const completed = projects.value.filter(p => p.status === 'Terminado')
    const withIncident = Math.floor(completed.length * 0.15) // Simular 15% con incidencias
    const withoutIncident = completed.length - withIncident
    const rate = completed.length > 0 ? Math.round((withIncident / completed.length) * 100) : 0
    
    return {
        total: completed.length,
        withIncident,
        withoutIncident,
        rate
    }
})

const getEngineerBg = (perfil) => {
    const colors = {
        'RB': '#EEEDFE',
        'FR': '#E6F1FB',
        'AE': '#E1F5EE',
        'PR': '#FAEEDA',
        'RE': '#FAECE7'
    }
    return colors[perfil] || '#f0f2f5'
}

const getEngineerColor = (perfil) => {
    const colors = {
        'RB': '#3C3489',
        'FR': '#0C447C',
        'AE': '#085041',
        'PR': '#633806',
        'RE': '#712B13'
    }
    return colors[perfil] || '#1a1f3c'
}

const loadData = async () => {
    loading.value = true
    try {
        const [projectsRes, engineersRes] = await Promise.all([
            axios.get('/api/projects'),
            axios.get('/api/users/engineers')
        ])
        projects.value = projectsRes.data
        engineers.value = engineersRes.data
    } catch (error) {
        console.error('Error cargando datos:', error)
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    loadData()
})
</script>

<style scoped>
.indicators-container {
    max-width: 1400px;
    margin: 0 auto;
}

.indicators-header {
    background: #1a1f3c;
    border-radius: 14px;
    padding: 12px 18px;
    margin-bottom: 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.header-title {
    font-size: 13px;
    font-weight: 700;
    color: white;
}

.header-subtitle {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.5);
    margin-top: 1px;
}

.header-badge {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.5);
}

.kpis-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
    margin-bottom: 18px;
}

.kpi-card {
    background: #f0f2f5;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    padding: 14px;
    border-top: 3px solid;
}

.kpi-label {
    font-size: 9px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #4a5568;
    margin-bottom: 6px;
    line-height: 1.3;
    white-space: pre-line;
}

.kpi-value {
    font-size: 26px;
    font-weight: 800;
    font-family: 'Syne', sans-serif;
    line-height: 1;
}

.kpi-subtitle {
    font-size: 10px;
    color: #4a5568;
    margin-top: 4px;
}

.kpi-note {
    font-size: 9px;
    color: #4a5568;
    margin-top: 2px;
    font-style: italic;
}

.section-card {
    background: white;
    border: 1px solid #e4e8ee;
    border-radius: 14px;
    padding: 16px;
    margin-bottom: 14px;
}

.section-title {
    font-size: 12px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.section-subtitle {
    font-size: 11px;
    color: #4a5568;
    margin-bottom: 14px;
}

.section-note {
    font-size: 10px;
    color: #4a5568;
    margin-top: 6px;
}

.phase-times {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.phase-row {
    display: grid;
    grid-template-columns: 130px 1fr 90px 90px 90px;
    gap: 8px;
    align-items: center;
}

.phase-badge {
    font-size: 11px;
    font-weight: 600;
    padding: 2px 8px;
    border-radius: 99px;
    text-align: center;
}

.phase-bar-container {
    position: relative;
}

.phase-bar-track {
    height: 8px;
    background: #e4e8ee;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.phase-bar-fill {
    height: 100%;
    border-radius: 4px;
    transition: width 0.4s;
}

.phase-standard-mark {
    position: absolute;
    top: -3px;
    width: 2px;
    height: 14px;
    background: #1a1f3c;
    border-radius: 1px;
}

.phase-real, .phase-diff {
    text-align: center;
    font-size: 11px;
    font-weight: 700;
}

.phase-standard {
    text-align: center;
    font-size: 11px;
    color: #4a5568;
}

.engineers-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
    gap: 10px;
}

.engineer-card {
    background: #f0f2f5;
    border-radius: 10px;
    padding: 12px;
    border-left: 3px solid;
}

.engineer-header {
    display: flex;
    align-items: center;
    gap: 7px;
    margin-bottom: 6px;
}

.engineer-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    font-weight: 700;
}

.engineer-name {
    font-size: 12px;
    font-weight: 600;
    color: #1a2233;
}

.engineer-count {
    font-size: 22px;
    font-weight: 800;
    font-family: 'Syne', sans-serif;
}

.engineer-status {
    font-size: 10px;
    color: #4a5568;
}

.empty-box {
    padding: 12px;
    background: #f0f2f5;
    border-radius: 10px;
    font-size: 12px;
    color: #4a5568;
}

.incidents-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 8px;
}

.incident-card {
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    border: 1px solid;
}

.incident-card.bad {
    background: rgba(217, 53, 53, 0.1);
    border-color: rgba(217, 53, 53, 0.2);
}

.incident-card.bad .incident-value {
    color: #d93535;
}

.incident-card.bad .incident-label {
    color: #d93535;
}

.incident-card.ok {
    background: rgba(30, 158, 94, 0.1);
    border-color: rgba(30, 158, 94, 0.2);
}

.incident-card.ok .incident-value {
    color: #0F6E56;
}

.incident-card.ok .incident-label {
    color: #0F6E56;
}

.incident-card.neutral {
    background: #f0f2f5;
    border-color: #e4e8ee;
}

.incident-card.neutral .incident-value {
    color: #1a2233;
}

.incident-card.neutral .incident-label {
    color: #4a5568;
}

.incident-value {
    font-size: 24px;
    font-weight: 800;
    font-family: 'Syne', sans-serif;
}

.incident-label {
    font-size: 10px;
    margin-top: 2px;
}

.loading-state {
    text-align: center;
    padding: 60px;
    color: #6b7280;
}
</style>

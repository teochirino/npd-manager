<template>
    <div class="approvals-container">
        <!-- Pendientes de Aprobación -->
        <div v-if="loading" class="loading-state">Cargando proyectos...</div>
        <div v-else>
            <div v-if="pendingProjects.length > 0" class="section">
                <div class="section-header">
                    <h3 class="section-title">Pendientes de aprobación ({{ pendingProjects.length }})</h3>
                </div>
                <div v-for="project in pendingProjects" :key="project.id" class="approval-card-pending">
                    <div class="pending-header">
                        <div class="pending-title">{{ project.name }}</div>
                        <div class="pending-meta">
                            <span class="phase-badge" :style="getPhaseStyle(project.active_phase)">{{ project.active_phase }}</span>
                            <span class="progress-badge">{{ project.progress }}%</span>
                            <span class="engineer-name">{{ project.engineer?.name }}</span>
                        </div>
                    </div>
                    <div class="pending-body">
                        <div class="approval-box">
                            <div class="approval-box-info">
                                <div class="approval-box-title">Gerencia de Innovación y Diseño</div>
                                <div class="approval-box-subtitle">Pendiente de revisión</div>
                            </div>
                            <span class="tag-pending">Pendiente</span>
                            <button v-if="canApprove" @click="approveProject(project)" class="btn-approve-small">
                                ✓ Dar aprobación
                            </button>
                        </div>
                        <div class="pending-actions">
                            <button @click="viewProject(project)" class="btn-view">Ver proyecto →</button>
                            <button v-if="canApprove" class="btn-generate">📄 Generar documento de liberación</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Proyectos Aprobados -->
            <div v-if="approvedProjects.length > 0" class="section">
                <div class="section-header">
                    <h3 class="section-title approved-title">Aprobados ({{ approvedProjects.length }})</h3>
                </div>
                <div v-for="project in approvedProjects" :key="project.id" class="approval-card-approved" @click="viewProject(project)">
                    <span class="approved-name">{{ project.name }}</span>
                    <span class="tag-approved">✓ Aprobado</span>
                    <span class="approved-date">{{ formatDate(project.updated_at) }}</span>
                    <button v-if="canApprove" @click.stop class="btn-pdf">📄 PDF</button>
                </div>
            </div>

            <!-- Sin proyectos -->
            <div v-if="pendingProjects.length === 0 && approvedProjects.length === 0" class="empty-state">
                <div class="empty-icon">✅</div>
                <p>Sin proyectos pendientes o aprobados.</p>
            </div>
        </div>

        <!-- Modal Simple de Confirmación -->
        <Teleport to="body">
            <div v-if="showModal" class="modal-overlay" @click.self="closeModal">
                <div class="modal-box">
                    <h3 class="modal-title">
                        {{ modalAction === 'approve' ? 'Aprobar' : 'Rechazar' }} Gate
                    </h3>
                    <p class="modal-subtitle">
                        {{ selectedApproval?.gate_key }} - {{ selectedApproval?.project?.name }}
                    </p>

                    <div class="form-group">
                        <label>Comentarios {{ modalAction === 'reject' ? '(requerido)' : '' }}</label>
                        <textarea 
                            v-model="comments" 
                            class="form-textarea"
                            :placeholder="modalAction === 'approve' ? 'Comentarios opcionales...' : 'Explica el motivo del rechazo...'"
                            rows="4"
                        ></textarea>
                    </div>

                    <div class="modal-actions">
                        <button @click="closeModal" class="btn-secondary">Cancelar</button>
                        <button 
                            @click="submitApproval" 
                            :disabled="submitting || (modalAction === 'reject' && !comments)"
                            :class="modalAction === 'approve' ? 'btn-approve' : 'btn-reject'"
                        >
                            {{ submitting ? 'Procesando...' : (modalAction === 'approve' ? 'Aprobar' : 'Rechazar') }}
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import axios from 'axios'

const props = defineProps({
    user: Object
})

const emit = defineEmits(['view-project'])

const projects = ref([])
const loading = ref(false)
const showModal = ref(false)
const selectedProject = ref(null)

const canApprove = computed(() => {
    return props.user?.perfil === 'GI'
})

const pendingProjects = computed(() => {
    return projects.value.filter(p => p.progress >= 80 && p.status !== 'Terminado' && p.status !== 'Cancelado')
})

const approvedProjects = computed(() => {
    return projects.value.filter(p => p.status === 'Terminado')
})

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

const formatDate = (date) => {
    if (!date) return ''
    return new Date(date).toLocaleDateString('es-MX', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const approveProject = async (project) => {
    if (!confirm(`¿Aprobar el proyecto "${project.name}"?`)) return
    
    try {
        await axios.put(`/api/projects/${project.id}`, {
            status: 'Terminado'
        })
        await loadProjects()
        alert('Proyecto aprobado exitosamente ✓')
    } catch (error) {
        console.error('Error aprobando proyecto:', error)
        alert('Error al aprobar el proyecto')
    }
}

const viewProject = (project) => {
    emit('view-project', project)
}

const closeModal = () => {
    showModal.value = false
    selectedProject.value = null
}

onMounted(() => {
    loadProjects()
})
</script>

<style scoped>
.approvals-container {
    max-width: 1200px;
    margin: 0 auto;
}

.section {
    margin-bottom: 32px;
}

.section-header {
    margin-bottom: 16px;
}

.section-title {
    font-size: 15px;
    font-weight: 700;
    color: #1a1f3c;
}

.approved-title {
    color: #0F6E56;
}

.approval-card-pending {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    overflow: hidden;
    margin-bottom: 12px;
}

.pending-header {
    padding: 10px 14px;
    border-bottom: 1px solid #e5e7eb;
    background: #f9fafb;
    display: flex;
    align-items: center;
    gap: 8px;
}

.pending-title {
    font-size: 13px;
    font-weight: 700;
    flex: 1;
}

.pending-meta {
    display: flex;
    align-items: center;
    gap: 8px;
}

.phase-badge {
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
}

.progress-badge {
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(26, 31, 60, 0.08);
    color: #1a1f3c;
    border: 1px solid rgba(26, 31, 60, 0.15);
}

.engineer-name {
    font-size: 11px;
    color: #6b7280;
}

.pending-body {
    padding: 11px 14px;
}

.approval-box {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 11px;
    background: #f9fafb;
    border-radius: 10px;
    border: 1px solid #e5e7eb;
    margin-bottom: 8px;
}

.approval-box-info {
    flex: 1;
}

.approval-box-title {
    font-size: 12px;
    font-weight: 700;
}

.approval-box-subtitle {
    font-size: 10px;
    color: #6b7280;
}

.tag-pending {
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    background: #FEF3C7;
    color: #92400E;
    border: 1px solid rgba(196, 125, 10, 0.25);
}

.pending-actions {
    display: flex;
    gap: 8px;
}

.btn-view {
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #e5e7eb;
    background: #f0f2f5;
    color: #1a2233;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.1s;
}

.btn-view:hover {
    background: white;
}

.btn-generate {
    padding: 5px 10px;
    font-size: 12px;
    font-weight: 600;
    border: 1px solid #1a1f3c;
    background: #1a1f3c;
    color: white;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.1s;
}

.btn-generate:hover {
    background: #2d3561;
}

.btn-approve-small {
    padding: 5px 10px;
    font-size: 11px;
    font-weight: 600;
    background: rgba(30, 158, 94, 0.1);
    border: 1px solid rgba(30, 158, 94, 0.3);
    color: #0F6E56;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.1s;
}

.btn-approve-small:hover {
    background: rgba(30, 158, 94, 0.2);
}

.approval-card-approved {
    background: white;
    border: 1px solid rgba(30, 158, 94, 0.3);
    border-radius: 14px;
    padding: 10px 14px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    transition: box-shadow 0.15s;
}

.approval-card-approved:hover {
    box-shadow: 0 2px 12px rgba(26, 34, 51, 0.08);
}

.approved-name {
    font-size: 13px;
    font-weight: 600;
    flex: 1;
}

.tag-approved {
    padding: 3px 9px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    background: #E1F5EE;
    color: #085041;
    border: 1px solid rgba(30, 158, 94, 0.2);
}

.approved-date {
    font-size: 11px;
    color: #6b7280;
}

.btn-pdf {
    padding: 3px 8px;
    font-size: 10px;
    font-weight: 600;
    background: #1a1f3c;
    border: 1px solid #1a1f3c;
    color: white;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.1s;
}

.btn-pdf:hover {
    background: #2d3561;
}


.loading-state, .empty-state {
    text-align: center;
    padding: 60px 20px;
    color: #6b7280;
}

.empty-icon {
    font-size: 64px;
    margin-bottom: 16px;
    opacity: 0.5;
}

.modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-box {
    background: white;
    border-radius: 16px;
    padding: 24px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
}

.modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1f3c;
    margin-bottom: 4px;
}

.modal-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
}

.form-textarea {
    width: 100%;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
}

.form-textarea:focus {
    outline: none;
    border-color: #534AB7;
}

.modal-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
}
</style>

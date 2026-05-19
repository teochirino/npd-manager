<template>
    <div class="diagram-container">
        <h1 class="diagram-title">Diagrama del proceso NPD</h1>
        <p class="diagram-subtitle">Proceso de desarrollo de nuevos productos · 5 fases · 70+ tareas</p>

        <!-- Phases -->
        <div class="phases-container">
            <div v-for="phase in phases" :key="phase.id" class="phase-block" :style="{ background: phase.bg }">
                <!-- Phase Header -->
                <div class="phase-header" :style="{ background: phase.color }">
                    <div class="phase-icon">{{ phase.icon }}</div>
                    <div class="phase-info">
                        <div class="phase-number">{{ phase.num }}</div>
                        <div class="phase-name">{{ phase.name }}</div>
                    </div>
                    <button class="phase-toggle" @click="togglePhase(phase.id)">
                        {{ expandedPhases.includes(phase.id) ? '−' : '+' }}
                    </button>
                </div>

                <!-- Phase Description -->
                <div class="phase-desc" :style="{ color: phase.textColor }">
                    {{ phase.description }}
                </div>

                <!-- Tasks Grid -->
                <div v-if="expandedPhases.includes(phase.id)" class="tasks-grid">
                    <div v-for="(taskRow, idx) in phase.tasks" :key="idx" class="task-row">
                        <div v-for="task in taskRow" :key="task" class="task-item" @click="showTaskDetail(task, phase)">
                            <div class="task-bullet" :style="{ background: phase.color }"></div>
                            <div class="task-name">{{ task }}</div>
                            <div class="task-arrow">→</div>
                        </div>
                    </div>
                </div>

                <!-- Gate -->
                <div class="gate-section" :style="{ background: phase.color }">
                    <div class="gate-icon">◆</div>
                    <div class="gate-info">
                        <div class="gate-title">{{ phase.gate.name }}</div>
                        <div class="gate-subtitle">{{ phase.gate.subtitle }}</div>
                    </div>
                    <div class="gate-question">{{ phase.gate.question }}</div>
                </div>
            </div>
        </div>

        <!-- Task Detail Modal -->
        <div v-if="selectedTask" class="modal-overlay" @click="selectedTask = null">
            <div class="modal-content" @click.stop>
                <div class="modal-header" :style="{ background: selectedTask.phaseColor }">
                    <h3>{{ selectedTask.name }}</h3>
                    <button @click="selectedTask = null" class="modal-close">✕</button>
                </div>
                <div class="modal-body">
                    <div class="modal-section">
                        <div class="modal-label">Fase</div>
                        <div class="modal-value">{{ selectedTask.phaseName }}</div>
                    </div>
                    <div class="modal-section">
                        <div class="modal-label">Área responsable</div>
                        <div class="modal-value">{{ getTaskDepartment(selectedTask.name) }}</div>
                    </div>
                    <div class="modal-section">
                        <div class="modal-label">Descripción</div>
                        <div class="modal-value">{{ getTaskDescription(selectedTask.name) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const expandedPhases = ref(['D', 'De', 'Im', 'La', 'Se'])
const selectedTask = ref(null)

const phases = [
    {
        id: 'D',
        num: 1,
        name: 'DISEÑO',
        color: '#534AB7',
        bg: '#EEEDFE',
        textColor: '#3C3489',
        icon: '💡',
        description: 'Definir un concepto viable, alineado al mercado y a la estrategia de Línea Italia.',
        gate: {
            name: 'GATE 1',
            subtitle: 'Concepto aprobado',
            question: '¿El concepto es viable y alineado con los objetivos?'
        },
        tasks: [
            ['Brief de diseño', 'Benchmarking del mercado', 'Requerimientos de diseño y producto', 'Selección de materiales, herrajes y componentes'],
            ['Modelados 3D preliminares', 'Presentación de concepto y diseño', 'Criterios de diseño establecidos', 'Revisión preliminar de factibilidad de manufactura'],
            ['Liberación de BOM de ingeniería preliminar', 'Validación de costo objetivo inicial', 'Cronograma del proyecto']
        ]
    },
    {
        id: 'De',
        num: 2,
        name: 'DESARROLLO',
        color: '#185FA5',
        bg: '#E6F1FB',
        textColor: '#0C447C',
        icon: '🔧',
        description: 'Convertir el concepto en un producto técnicamente definido, probado y validado mediante prototipo.',
        gate: {
            name: 'GATE 2',
            subtitle: 'Diseño liberado',
            question: '¿El diseño está técnicamente completo y el prototipo validado?'
        },
        tasks: [
            ['Validación de diseño final y componentes', 'Desarrollo de planos y especificaciones técnicas', 'Liberación de BOM de ingeniería', 'Evaluación de alternativas críticas'],
            ['Evaluación de diseño para manufactura y ensamble', 'Análisis estructurado de riesgo de diseño', 'Programación de prototipo', 'Preparación de archivos de corte y ensamble'],
            ['Fabricación de prototipos funcionales', 'Validación dimensional', 'Validación funcional y de ensamble', 'Pruebas estéticas y de acabados'],
            ['Pruebas de uso, resistencia, carga y estabilidad', 'Definición preliminar de flujo de proceso', 'Análisis estructurado de riesgo de proceso', 'Revisión de herramientas, plantillas o dispositivos'],
            ['Validación de tiempos preliminares', 'Revisión de BOM y abastecimiento', 'Costo preliminar de ingeniería', 'Dictamen técnico de prototipo validado']
        ]
    },
    {
        id: 'Im',
        num: 3,
        name: 'IMPLEMENTACIÓN',
        color: '#0F6E56',
        bg: '#E1F5EE',
        textColor: '#085041',
        icon: '🏭',
        description: 'Asegurar que el producto pueda fabricarse de manera repetible, estable y documentada.',
        gate: {
            name: 'GATE 3',
            subtitle: 'Proceso implementado',
            question: '¿El proceso está implementado y listo para producir en serie?'
        },
        tasks: [
            ['Fabricación de plantillas', 'Liberación dimensional de plantillas', 'Flujo de proceso y ruta de fabricación', 'Instrucciones de trabajo y ayudas visuales'],
            ['Confirmación de requerimiento de equipo y herramientas', 'Costo industrial validado', 'Alta completa en ERP', 'Asignación de SKU'],
            ['Documentación técnica', 'Especificaciones de los SKUs', 'Fichas técnicas por SKU', 'Instructivos de ensamble por SKU'],
            ['Liberación de empaque', 'Corrida piloto controlada (mín. 3 pzas.)', 'Liberación de piezas de corrida piloto', 'Validación de plantillas post-piloto'],
            ['Ajustes en ERP y costo final liberado', 'Liberación de producción y procesos']
        ]
    },
    {
        id: 'La',
        num: 4,
        name: 'LANZAMIENTO',
        color: '#854F0B',
        bg: '#FAEEDA',
        textColor: '#633806',
        icon: '📣',
        description: 'Poner el producto en condiciones reales de venta, especificación y adopción interna.',
        gate: {
            name: 'GATE 4',
            subtitle: 'Lanzamiento autorizado',
            question: '¿Todo listo para salir al mercado y operación comercial?'
        },
        tasks: [
            ['Definición de precios de lista', 'Alta en aplicativo y cotizador', 'Enviar modelos SAT para proyectistas', 'Render y product shots', 'Contenido para catálogo y web'],
            ['Definición de nombre comercial', 'Presentación de capacitación comercial', 'Capacitación comercial', 'Presentación de capacitación operativa/ manufactura', 'Capacitación operativa/ manufactura'],
            ['Material especial (fotos, folletos, infografías)', 'Liberación oficial por gerencia', 'Aprobación final del Director Comercial']
        ]
    },
    {
        id: 'Se',
        num: 5,
        name: 'SEGUIMIENTO',
        color: '#712B13',
        bg: '#FAECE7',
        textColor: '#712B13',
        icon: '📊',
        description: 'Monitorear el desempeño real del producto y cerrar formalmente el proyecto.',
        gate: {
            name: 'GATE 5',
            subtitle: 'Cierre de proyecto',
            question: '¿El producto cumplió los objetivos y el proyecto puede cerrarse?'
        },
        tasks: [
            ['Seguimiento de primeras fabricaciones', 'Seguimiento a reclamos, fallas o incidencias', 'Validar costo real vs. objetivo', 'Validar tiempo real vs. objetivo'],
            ['Revisar calidad en primeras entregas', 'Revisión de incidencias en instalación y uso', 'Ajuste de documentación si aplica'],
            ['Evaluación final de éxito', 'Cierre formal del expediente']
        ]
    }
]

const togglePhase = (phaseId) => {
    const index = expandedPhases.value.indexOf(phaseId)
    if (index > -1) {
        expandedPhases.value.splice(index, 1)
    } else {
        expandedPhases.value.push(phaseId)
    }
}

const showTaskDetail = (taskName, phase) => {
    selectedTask.value = {
        name: taskName,
        phaseName: phase.name,
        phaseColor: phase.color
    }
}

const getTaskDepartment = (taskName) => {
    const departments = {
        'Brief de diseño': 'GER. INNOVACIÓN / ING. PRODUCTO',
        'Benchmarking del mercado': 'ING. PRODUCTO',
        'Requerimientos de diseño y producto': 'ING. PRODUCTO',
        'Validación dimensional': 'CALIDAD',
        'Fabricación de prototipos funcionales': 'TALLER INNOVACIÓN / MANUFACTURA',
        'Costo preliminar de ingeniería': 'COSTOS',
        'Alta completa en ERP': 'COSTOS',
        'Definición de precios de lista': 'COMERCIAL / COSTOS',
        'Capacitación comercial': 'COMERCIAL',
        'Seguimiento a reclamos, fallas o incidencias': 'CALIDAD / GER. INNOVACIÓN'
    }
    return departments[taskName] || 'ING. PRODUCTO / MANUFACTURA'
}

const getTaskDescription = (taskName) => {
    const descriptions = {
        'Brief de diseño': 'Documento inicial que define el alcance, los objetivos comerciales y los criterios de éxito del nuevo producto.',
        'Benchmarking del mercado': 'Análisis comparativo de productos similares en el mercado para identificar oportunidades de diferenciación.',
        'Validación dimensional': 'Verificación de que el prototipo cumple las dimensiones definidas en planos.',
        'Fabricación de prototipos funcionales': 'Construcción del prototipo real para pruebas y validación.',
        'Alta completa en ERP': 'Registro de todos los componentes, rutas y costos en el sistema ERP.',
        'Definición de precios de lista': 'Establecimiento del precio de venta oficial del producto.',
        'Evaluación final de éxito': 'Análisis comparativo de los resultados reales vs. los objetivos iniciales del proyecto.'
    }
    return descriptions[taskName] || `Tarea de la fase: revisa el diagrama de proceso para más contexto.`
}
</script>

<style scoped>
.diagram-container {
    max-width: 1400px;
    margin: 0 auto;
    padding-bottom: 40px;
}

.diagram-title {
    font-size: 32px;
    font-weight: 800;
    color: #1a1f3c;
    margin-bottom: 8px;
}

.diagram-subtitle {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 32px;
}

.phases-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.phase-block {
    border-radius: 12px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

.phase-header {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    cursor: pointer;
}

.phase-icon {
    font-size: 28px;
}

.phase-info {
    flex: 1;
}

.phase-number {
    font-size: 11px;
    font-weight: 700;
    color: rgba(255, 255, 255, 0.7);
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.phase-name {
    font-size: 18px;
    font-weight: 800;
    color: white;
    letter-spacing: 0.02em;
}

.phase-toggle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    font-size: 20px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.2s;
}

.phase-toggle:hover {
    background: rgba(255, 255, 255, 0.3);
}

.phase-desc {
    padding: 16px 20px;
    font-size: 13px;
    line-height: 1.6;
    font-weight: 500;
}

.tasks-grid {
    padding: 0 20px 20px;
}

.task-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
    margin-bottom: 12px;
}

.task-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 12px;
    background: white;
    border-radius: 8px;
    border: 1px solid #e5e7eb;
    cursor: pointer;
    transition: all 0.2s;
}

.task-item:hover {
    border-color: #534AB7;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.task-bullet {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    flex-shrink: 0;
}

.task-name {
    flex: 1;
    font-size: 11px;
    font-weight: 600;
    color: #1a1f3c;
    line-height: 1.4;
}

.task-arrow {
    font-size: 12px;
    color: #9ca3af;
}

.gate-section {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.gate-icon {
    font-size: 24px;
    color: white;
}

.gate-info {
    flex: 1;
}

.gate-title {
    font-size: 12px;
    font-weight: 800;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.gate-subtitle {
    font-size: 14px;
    font-weight: 600;
    color: rgba(255, 255, 255, 0.9);
}

.gate-question {
    font-size: 11px;
    color: rgba(255, 255, 255, 0.8);
    font-style: italic;
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
    max-width: 600px;
    max-height: 80vh;
    overflow: hidden;
}

.modal-header {
    padding: 20px 24px;
    color: white;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    font-size: 18px;
    font-weight: 700;
    margin: 0;
}

.modal-close {
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    font-size: 18px;
    cursor: pointer;
}

.modal-body {
    padding: 24px;
}

.modal-section {
    margin-bottom: 20px;
}

.modal-label {
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    margin-bottom: 6px;
}

.modal-value {
    font-size: 14px;
    color: #1a1f3c;
    line-height: 1.6;
}
</style>

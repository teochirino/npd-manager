<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TasksPredefinedTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de las fases por nombre
        $phases = [];
        foreach (DB::table('phases')->get() as $phase) {
            $phases[$phase->name] = $phase->id;
        }

        $tasks = [
            // ========== FASE 1: DISEÑO (12 tareas) ==========
            ['phase_id' => $phases['Diseño'], 'name' => 'Brief de diseño', 'department' => 'GER. INNOVACIÓN / ING. PRODUCTO', 'default_duration' => 2, 'order' => 1],
            ['phase_id' => $phases['Diseño'], 'name' => 'Benchmarking del mercado', 'department' => 'ING. PRODUCTO', 'default_duration' => 2, 'order' => 2],
            ['phase_id' => $phases['Diseño'], 'name' => 'Requerimientos de diseño y producto', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 3],
            ['phase_id' => $phases['Diseño'], 'name' => 'Selección de materiales, herrajes y componentes', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 4],
            ['phase_id' => $phases['Diseño'], 'name' => 'Modelados 3D preliminares', 'department' => 'ING. PRODUCTO', 'default_duration' => 2, 'order' => 5],
            ['phase_id' => $phases['Diseño'], 'name' => 'Presentación de concepto y diseño', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 6],
            ['phase_id' => $phases['Diseño'], 'name' => 'Criterios de diseño establecidos', 'department' => 'GER. INNOVACIÓN / ING. PRODUCTO', 'default_duration' => 1, 'order' => 7],
            ['phase_id' => $phases['Diseño'], 'name' => 'Revisión preliminar de factibilidad de manufactura', 'department' => 'MANUFACTURA / PROCESOS', 'default_duration' => 1, 'order' => 8],
            ['phase_id' => $phases['Diseño'], 'name' => 'Liberación de BOM de ingeniería preliminar', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 9],
            ['phase_id' => $phases['Diseño'], 'name' => 'Validación de costo objetivo inicial', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 10],
            ['phase_id' => $phases['Diseño'], 'name' => 'Cronograma del proyecto', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 11],
            ['phase_id' => $phases['Diseño'], 'name' => 'Project Plan', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 12],

            // ========== FASE 2: DESARROLLO (20 tareas) ==========
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Validación de diseño final y componentes', 'department' => 'GER. INNOVACIÓN Y DISEÑO', 'default_duration' => 1, 'order' => 1],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Desarrollo de planos y especificaciones técnicas', 'department' => 'ING. PRODUCTO', 'default_duration' => 2, 'order' => 2],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Liberación de BOM de ingeniería', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 3],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Evaluación de alternativas críticas', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 4],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Evaluación de diseño para manufactura y ensamble', 'department' => 'MANUFACTURA / PROCESOS', 'default_duration' => 1, 'order' => 5],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Análisis estructurado de riesgo de diseño', 'department' => 'ING. PRODUCTO / PROCESOS / CALIDAD', 'default_duration' => 1, 'order' => 6],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Programación de prototipo', 'department' => 'ING. PRODUCTO / PCP', 'default_duration' => 1, 'order' => 7],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Preparación de archivos de corte y ensamble', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 8],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Fabricación de prototipos funcionales', 'department' => 'TALLER INNOVACIÓN / MANUFACTURA', 'default_duration' => 2, 'order' => 9],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Validación dimensional', 'department' => 'CALIDAD', 'default_duration' => 1, 'order' => 10],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Validación funcional y de ensamble', 'department' => 'ING. PRODUCTO / PROCESOS', 'default_duration' => 1, 'order' => 11],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Pruebas estéticas y de acabados', 'department' => 'CALIDAD', 'default_duration' => 1, 'order' => 12],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Pruebas de uso, resistencia, carga y estabilidad', 'department' => 'ING. PRODUCTO / CALIDAD', 'default_duration' => 2, 'order' => 13],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Definición preliminar de flujo de proceso', 'department' => 'ING. PROCESOS', 'default_duration' => 1, 'order' => 14],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Análisis estructurado de riesgo de proceso', 'department' => 'ING. PROCESOS / CALIDAD', 'default_duration' => 1, 'order' => 15],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Revisión de herramientas, plantillas o dispositivos', 'department' => 'ING. PROCESOS / CALIDAD', 'default_duration' => 1, 'order' => 16],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Validación de tiempos preliminares', 'department' => 'MANUFACTURA / ING. PROCESOS', 'default_duration' => 1, 'order' => 17],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Revisión de BOM y abastecimiento', 'department' => 'COMPRAS', 'default_duration' => 1, 'order' => 18],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Costo preliminar de ingeniería', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 19],
            ['phase_id' => $phases['Desarrollo'], 'name' => 'Dictamen técnico de prototipo validado', 'department' => 'GER. INNOVACIÓN Y DISEÑO', 'default_duration' => 1, 'order' => 20],

            // ========== FASE 3: IMPLEMENTACION (18 tareas) ==========
            ['phase_id' => $phases['Implementacion'], 'name' => 'Fabricación de plantillas', 'department' => 'ING. PROCESOS / HERRAMENTALES', 'default_duration' => 3, 'order' => 1],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Liberación dimensional de plantillas', 'department' => 'CALIDAD / ING. PROCESOS', 'default_duration' => 1, 'order' => 2],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Flujo de proceso y ruta de fabricación', 'department' => 'ING. PROCESOS', 'default_duration' => 1, 'order' => 3],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Instrucciones de trabajo y ayudas visuales', 'department' => 'ING. PROCESOS', 'default_duration' => 1, 'order' => 4],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Confirmación de requerimiento de equipo y herramientas', 'department' => 'MANUFACTURA / ING. PROCESOS', 'default_duration' => 1, 'order' => 5],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Costo industrial validado', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 6],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Alta completa en ERP', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 7],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Asignación de SKU', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 8],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Documentación técnica', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 9],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Especificaciones', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 10],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Fichas técnicas', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 11],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Instructivos de ensamble', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 12],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Liberación de empaque', 'department' => 'ING. PRODUCTO / MANUFACTURA', 'default_duration' => 1, 'order' => 13],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Corrida piloto controlada mínimo 3 pzas.', 'department' => 'MANUFACTURA / ING. PROCESOS', 'default_duration' => 3, 'order' => 14],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Liberación de piezas de corrida piloto', 'department' => 'CALIDAD', 'default_duration' => 1, 'order' => 15],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Validación de plantillas post-piloto', 'department' => 'MANUFACTURA / CALIDAD / ING. PROCESOS', 'default_duration' => 1, 'order' => 16],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Ajustes en ERP y costo final liberado', 'department' => 'COSTOS', 'default_duration' => 1, 'order' => 17],
            ['phase_id' => $phases['Implementacion'], 'name' => 'Liberación de producción y procesos', 'department' => 'GER. INNOVACIÓN / MANUFACTURA', 'default_duration' => 1, 'order' => 18],

            // ========== FASE 4: LANZAMIENTO (11 tareas) ==========
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Definición de precios de lista', 'department' => 'COMERCIAL / COSTOS', 'default_duration' => 1, 'order' => 1],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Alta en aplicativo y cotizador', 'department' => 'COMERCIAL / ERP', 'default_duration' => 1, 'order' => 2],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Enviar modelos SAT para proyectistas', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 3],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Render y product shots', 'department' => 'MARKETING', 'default_duration' => 2, 'order' => 4],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Contenido para catálogo y web', 'department' => 'MARKETING', 'default_duration' => 2, 'order' => 5],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Definición de nombre comercial', 'department' => 'COMERCIAL / MARKETING', 'default_duration' => 1, 'order' => 6],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Presentación de capacitación comercial', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 7],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Presentación de capacitación operativa / manufactura', 'department' => 'ING. PRODUCTO', 'default_duration' => 1, 'order' => 8],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Material especial (fotos, folletos, infografías)', 'department' => 'MARKETING', 'default_duration' => 1, 'order' => 9],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Liberación oficial por Gerencia', 'department' => 'GER. INNOVACIÓN Y DISEÑO', 'default_duration' => 1, 'order' => 10],
            ['phase_id' => $phases['Lanzamiento'], 'name' => 'Aprobación final del Director Comercial', 'department' => 'DIRECCIÓN COMERCIAL', 'default_duration' => 1, 'order' => 11],

            // ========== FASE 5: SEGUIMIENTO (9 tareas) ==========
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Seguimiento de primeras fabricaciones', 'department' => 'MANUFACTURA / GER. INNOVACIÓN', 'default_duration' => 0, 'order' => 1],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Seguimiento a reclamos, fallas o incidencias', 'department' => 'CALIDAD / GER. INNOVACIÓN', 'default_duration' => 0, 'order' => 2],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Validar costo real vs. objetivo', 'department' => 'COSTOS', 'default_duration' => 0, 'order' => 3],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Validar tiempo real vs. objetivo', 'department' => 'MANUFACTURA / ING. PROCESOS', 'default_duration' => 0, 'order' => 4],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Revisar calidad en primeras entregas', 'department' => 'CALIDAD', 'default_duration' => 0, 'order' => 5],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Revisión de incidencias en instalación y uso', 'department' => 'SERVICIO / CALIDAD', 'default_duration' => 0, 'order' => 6],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Ajuste de documentación si aplica', 'department' => 'ING. PRODUCTO', 'default_duration' => 0, 'order' => 7],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Evaluación final de éxito', 'department' => 'GER. INNOVACIÓN Y DISEÑO', 'default_duration' => 0, 'order' => 8],
            ['phase_id' => $phases['Seguimiento'], 'name' => 'Cierre formal del expediente', 'department' => 'GER. INNOVACIÓN Y DISEÑO', 'default_duration' => 0, 'order' => 9],
        ];

        foreach ($tasks as $task) {
            DB::table('tasks_predefined')->updateOrInsert(
                [
                    'phase_id' => $task['phase_id'],
                    'name' => $task['name']
                ],
                $task
            );
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PredefinedTaskSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de fases
        $phases = DB::table('phases')->pluck('id', 'name');

        $tasks = [
            // FASE 1: DISEÑO (Gate 1 - Concepto aprobado)
            ['phase' => 'Diseño', 'name' => 'Brief de diseño y alcance del proyecto', 'department' => 'Diseño', 'duration' => 2, 'order' => 1],
            ['phase' => 'Diseño', 'name' => 'Validación de costo objetivo inicial', 'department' => 'Costos', 'duration' => 1, 'order' => 2],
            ['phase' => 'Diseño', 'name' => 'Revisión preliminar de factibilidad de manufactura', 'department' => 'Procesos', 'duration' => 2, 'order' => 3],
            ['phase' => 'Diseño', 'name' => 'Bocetos y renders iniciales', 'department' => 'Diseño', 'duration' => 3, 'order' => 4],
            ['phase' => 'Diseño', 'name' => 'Selección de concepto final', 'department' => 'Diseño', 'duration' => 1, 'order' => 5],
            ['phase' => 'Diseño', 'name' => 'Modelado 3D preliminar', 'department' => 'Diseño', 'duration' => 5, 'order' => 6],
            ['phase' => 'Diseño', 'name' => 'Especificaciones técnicas iniciales', 'department' => 'Ingeniería', 'duration' => 3, 'order' => 7],
            ['phase' => 'Diseño', 'name' => 'Presentación de concepto a dirección', 'department' => 'Gerencia', 'duration' => 1, 'order' => 8],

            // FASE 2: DESARROLLO (Gate 2 - Diseño liberado)
            ['phase' => 'Desarrollo', 'name' => 'Modelado 3D detallado', 'department' => 'Diseño', 'duration' => 7, 'order' => 1],
            ['phase' => 'Desarrollo', 'name' => 'Planos de fabricación', 'department' => 'Ingeniería', 'duration' => 5, 'order' => 2],
            ['phase' => 'Desarrollo', 'name' => 'Selección de materiales y acabados', 'department' => 'Ingeniería', 'duration' => 3, 'order' => 3],
            ['phase' => 'Desarrollo', 'name' => 'Evaluación de diseño para manufactura y ensamble', 'department' => 'Procesos', 'duration' => 3, 'order' => 4],
            ['phase' => 'Desarrollo', 'name' => 'Costo preliminar de ingeniería', 'department' => 'Costos', 'duration' => 2, 'order' => 5],
            ['phase' => 'Desarrollo', 'name' => 'Fabricación de prototipos funcionales', 'department' => 'Procesos', 'duration' => 10, 'order' => 6],
            ['phase' => 'Desarrollo', 'name' => 'Validación dimensional', 'department' => 'Calidad', 'duration' => 2, 'order' => 7],
            ['phase' => 'Desarrollo', 'name' => 'Pruebas estéticas y de acabados', 'department' => 'Calidad', 'duration' => 2, 'order' => 8],
            ['phase' => 'Desarrollo', 'name' => 'Pruebas de uso, resistencia, carga y estabilidad', 'department' => 'Calidad', 'duration' => 3, 'order' => 9],
            ['phase' => 'Desarrollo', 'name' => 'Ajustes de diseño post-prototipo', 'department' => 'Diseño', 'duration' => 5, 'order' => 10],
            ['phase' => 'Desarrollo', 'name' => 'Liberación de planos finales', 'department' => 'Ingeniería', 'duration' => 2, 'order' => 11],
            ['phase' => 'Desarrollo', 'name' => 'Aprobación de diseño final', 'department' => 'Gerencia', 'duration' => 1, 'order' => 12],

            // FASE 3: IMPLEMENTACIÓN (Gate 3 - Producción validada)
            ['phase' => 'Implementacion', 'name' => 'Fabricación de plantillas', 'department' => 'Procesos', 'duration' => 15, 'order' => 1],
            ['phase' => 'Implementacion', 'name' => 'Liberación dimensional de plantillas', 'department' => 'Calidad', 'duration' => 2, 'order' => 2],
            ['phase' => 'Implementacion', 'name' => 'Flujo de proceso y ruta de fabricación', 'department' => 'Procesos', 'duration' => 3, 'order' => 3],
            ['phase' => 'Implementacion', 'name' => 'Instrucciones de trabajo y ayudas visuales', 'department' => 'Procesos', 'duration' => 5, 'order' => 4],
            ['phase' => 'Implementacion', 'name' => 'Validación de tiempos preliminares', 'department' => 'Procesos', 'duration' => 2, 'order' => 5],
            ['phase' => 'Implementacion', 'name' => 'Corrida piloto controlada mínimo 3 pzas.', 'department' => 'Procesos', 'duration' => 3, 'order' => 6],
            ['phase' => 'Implementacion', 'name' => 'Liberación de piezas de corrida piloto', 'department' => 'Calidad', 'duration' => 2, 'order' => 7],
            ['phase' => 'Implementacion', 'name' => 'Validación de plantillas post-piloto', 'department' => 'Calidad', 'duration' => 1, 'order' => 8],
            ['phase' => 'Implementacion', 'name' => 'Costo industrial validado', 'department' => 'Costos', 'duration' => 2, 'order' => 9],
            ['phase' => 'Implementacion', 'name' => 'Validar costo real vs. objetivo', 'department' => 'Costos', 'duration' => 1, 'order' => 10],
            ['phase' => 'Implementacion', 'name' => 'Validar tiempo real vs. objetivo', 'department' => 'Procesos', 'duration' => 1, 'order' => 11],
            ['phase' => 'Implementacion', 'name' => 'Liberación de producción y procesos', 'department' => 'Procesos', 'duration' => 1, 'order' => 12],
            ['phase' => 'Implementacion', 'name' => 'Capacitación de personal de producción', 'department' => 'Procesos', 'duration' => 2, 'order' => 13],

            // FASE 4: LANZAMIENTO (Gate 4 - Lanzamiento aprobado)
            ['phase' => 'Lanzamiento', 'name' => 'Alta completa en ERP', 'department' => 'Costos', 'duration' => 2, 'order' => 1],
            ['phase' => 'Lanzamiento', 'name' => 'Asignación de SKU', 'department' => 'Costos', 'duration' => 1, 'order' => 2],
            ['phase' => 'Lanzamiento', 'name' => 'Ajustes en ERP y costo final liberado', 'department' => 'Costos', 'duration' => 2, 'order' => 3],
            ['phase' => 'Lanzamiento', 'name' => 'Definición de precios de lista', 'department' => 'Costos', 'duration' => 1, 'order' => 4],
            ['phase' => 'Lanzamiento', 'name' => 'Alta en aplicativo y cotizador', 'department' => 'Costos', 'duration' => 2, 'order' => 5],
            ['phase' => 'Lanzamiento', 'name' => 'Fotografía de producto', 'department' => 'Marketing', 'duration' => 2, 'order' => 6],
            ['phase' => 'Lanzamiento', 'name' => 'Fichas técnicas y catálogo', 'department' => 'Marketing', 'duration' => 3, 'order' => 7],
            ['phase' => 'Lanzamiento', 'name' => 'Material promocional y renders', 'department' => 'Marketing', 'duration' => 5, 'order' => 8],
            ['phase' => 'Lanzamiento', 'name' => 'Capacitación a equipo comercial', 'department' => 'Ventas', 'duration' => 1, 'order' => 9],
            ['phase' => 'Lanzamiento', 'name' => 'Lanzamiento interno (kick-off)', 'department' => 'Gerencia', 'duration' => 1, 'order' => 10],
            ['phase' => 'Lanzamiento', 'name' => 'Lanzamiento externo (clientes)', 'department' => 'Ventas', 'duration' => 1, 'order' => 11],

            // FASE 5: SEGUIMIENTO (Gate 5 - Cierre de proyecto)
            ['phase' => 'Seguimiento', 'name' => 'Revisar calidad en primeras entregas', 'department' => 'Calidad', 'duration' => 7, 'order' => 1],
            ['phase' => 'Seguimiento', 'name' => 'Seguimiento a reclamos, fallas o incidencias', 'department' => 'Calidad', 'duration' => 30, 'order' => 2],
            ['phase' => 'Seguimiento', 'name' => 'Análisis de ventas primeros 3 meses', 'department' => 'Ventas', 'duration' => 90, 'order' => 3],
            ['phase' => 'Seguimiento', 'name' => 'Evaluación de rentabilidad', 'department' => 'Costos', 'duration' => 7, 'order' => 4],
            ['phase' => 'Seguimiento', 'name' => 'Lecciones aprendidas', 'department' => 'Gerencia', 'duration' => 2, 'order' => 5],
            ['phase' => 'Seguimiento', 'name' => 'Documentación final del proyecto', 'department' => 'Ingeniería', 'duration' => 3, 'order' => 6],
            ['phase' => 'Seguimiento', 'name' => 'Cierre formal del proyecto', 'department' => 'Gerencia', 'duration' => 1, 'order' => 7],
        ];

        foreach ($tasks as $task) {
            $phaseName = $task['phase'];
            $phaseId = $phases[$phaseName] ?? null;

            if ($phaseId) {
                DB::table('tasks_predefined')->insert([
                    'phase_id' => $phaseId,
                    'name' => $task['name'],
                    'department' => $task['department'],
                    'default_duration' => $task['duration'],
                    'order' => $task['order'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}

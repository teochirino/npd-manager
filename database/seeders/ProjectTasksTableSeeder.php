<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProjectTasksTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener todos los proyectos con su fase activa y avance
        $projects = DB::table('projects')->get();
        
        // Obtener todas las tareas predefinidas agrupadas por fase
        $tasksByPhase = [];
        $allTasks = DB::table('tasks_predefined')->orderBy('phase_id')->orderBy('order')->get();
        foreach ($allTasks as $task) {
            $tasksByPhase[$task->phase_id][] = $task;
        }
        
        // Obtener el orden de las fases (1=Diseño, 5=Seguimiento)
        $phases = DB::table('phases')->orderBy('order')->get();
        $phaseOrder = [];
        foreach ($phases as $idx => $phase) {
            $phaseOrder[$phase->name] = $idx + 1; // 1 a 5
        }
        
        $projectTasks = [];
        
        foreach ($projects as $project) {
            $faseActual = $project->active_phase;
            $progress = $project->progress;
            
            // Calcular cuántas fases completas hay antes de la actual
            $faseActualIndex = $phaseOrder[$faseActual] ?? 1;
            
            foreach ($phases as $phase) {
                $phaseIndex = $phaseOrder[$phase->name];
                $tasks = $tasksByPhase[$phase->id] ?? [];
                
                if (empty($tasks)) continue;
                
                $totalTasks = count($tasks);
                $completedCount = 0;
                
                if ($phaseIndex < $faseActualIndex) {
                    // Fases anteriores: todas completadas
                    $completedCount = $totalTasks;
                    $taskStatus = 'Completado';
                } elseif ($phaseIndex == $faseActualIndex) {
                    // Fase actual: calcular según porcentaje de avance
                    // El avance se distribuye solo en la fase actual
                    $completedCount = round(($progress / 100) * $totalTasks);
                    if ($completedCount > $totalTasks) $completedCount = $totalTasks;
                } else {
                    // Fases futuras: ninguna completada
                    $completedCount = 0;
                }
                
                // Asignar estado a cada tarea
                foreach ($tasks as $taskIdx => $task) {
                    $status = ($taskIdx < $completedCount) ? 'Completado' : 
                              (($phaseIndex == $faseActualIndex && $taskIdx == $completedCount && $completedCount < $totalTasks) ? 'En progreso' : 'No iniciado');
                    
                    $projectTasks[] = [
                        'project_id' => $project->id,
                        'task_predefined_id' => $task->id,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }
        
        // Insertar en batches para no sobrecargar
        $chunks = array_chunk($projectTasks, 200);
        foreach ($chunks as $chunk) {
            DB::table('project_tasks')->insert($chunk);
        }
    }
}
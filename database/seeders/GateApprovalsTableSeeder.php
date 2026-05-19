<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GateApprovalsTableSeeder extends Seeder
{
    public function run(): void
    {
        $projects = DB::table('projects')->get();
        $gates = ['Gate 1', 'Gate 2', 'Gate 3', 'Gate 4', 'Gate 5'];
        
        $approvals = [];
        
        foreach ($projects as $project) {
            $phaseIndex = $this->getPhaseIndex($project->active_phase);
            
            foreach ($gates as $idx => $gate) {
                $status = 'pendiente';
                $itemsChecked = [];
                
                // Si el proyecto está terminado, todos los gates aprobados
                if ($project->status === 'Terminado') {
                    $status = 'aprobado';
                    $itemsChecked = $this->getAllGateItems($gate);
                } 
                // Gates anteriores a la fase actual: aprobados
                elseif ($idx + 1 < $phaseIndex) {
                    $status = 'aprobado';
                    $itemsChecked = $this->getAllGateItems($gate);
                }
                // Gate actual: pendiente o parcialmente aprobado según avance
                elseif ($idx + 1 == $phaseIndex) {
                    $status = $project->progress >= 80 ? 'aprobado' : 'pendiente';
                    $itemsChecked = $this->getPartialGateItems($gate, $project->progress);
                }
                // Gates futuros: pendiente
                
                $approvals[] = [
                    'project_id' => $project->id,
                    'gate_key' => $gate,
                    'status' => $status,
                    'items_checked' => json_encode($itemsChecked),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        
        DB::table('gate_approvals')->insert($approvals);
    }
    
    private function getPhaseIndex($phase)
    {
        $order = ['Diseño' => 1, 'Desarrollo' => 2, 'Implementacion' => 3, 'Lanzamiento' => 4, 'Seguimiento' => 5];
        return $order[$phase] ?? 1;
    }
    
    private function getAllGateItems($gate)
    {
        // Items simulados para cada gate (del HTML original)
        $items = [
            'Gate 1' => ['brief_claro', 'mercado_revisado', 'requerimientos_definidos', 'concepto_solido'],
            'Gate 2' => ['diseño_definido', 'planos_listos', 'prototipo_valida', 'costo_calculado'],
            'Gate 3' => ['plantillas_fabricadas', 'ruta_definida', 'instrucciones_claras', 'corrida_piloto_ok'],
            'Gate 4' => ['precio_autorizado', 'alta_erp', 'renders_listos', 'capacitacion_ok'],
            'Gate 5' => ['fabricaciones_estables', 'calidad_ok', 'costo_ok', 'cierre_formal'],
        ];
        
        $result = [];
        foreach ($items[$gate] ?? [] as $item) {
            $result[$item] = true;
        }
        return $result;
    }
    
    private function getPartialGateItems($gate, $progress)
    {
        $items = $this->getAllGateItems($gate);
        // Simular que según el progreso, algunos items están chequeados
        $totalItems = count($items);
        $checkedCount = round(($progress / 100) * $totalItems);
        
        $i = 0;
        foreach ($items as $key => $value) {
            $items[$key] = ($i < $checkedCount);
            $i++;
        }
        
        return $items;
    }
}
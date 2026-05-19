<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SkusTableSeeder extends Seeder
{
    public function run(): void
    {
        // Los SKUs que vienen del HTML original
        $skusData = [
            ['project_id' => 'PROJ1', 'sku_id' => 's1', 'name' => 'Escritorio Básico 120cm', 'code' => 'LBA-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ1', 'sku_id' => 's2', 'name' => 'Escritorio L 160cm', 'code' => 'LBA-002', 'status' => 'Completado'],
            ['project_id' => 'PROJ1', 'sku_id' => 's3', 'name' => 'Mesa de Juntas 240cm', 'code' => 'LBA-003', 'status' => 'Completado'],
            ['project_id' => 'PROJ1', 'sku_id' => 's4', 'name' => 'Módulo Esquinero', 'code' => 'LBA-004', 'status' => 'Completado'],
            
            ['project_id' => 'PROJ2', 'sku_id' => 's1', 'name' => 'Mesa de Trabajo', 'code' => 'EM-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ2', 'sku_id' => 's2', 'name' => 'Estación Maker Pro', 'code' => 'EM-002', 'status' => 'En proceso'],
            ['project_id' => 'PROJ2', 'sku_id' => 's3', 'name' => 'Panel de Herramientas', 'code' => 'EM-003', 'status' => 'Pendiente'],
            
            ['project_id' => 'PROJ4', 'sku_id' => 's1', 'name' => 'Silla Filio Base', 'code' => 'FI2-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ4', 'sku_id' => 's2', 'name' => 'Silla Filio con Brazos', 'code' => 'FI2-002', 'status' => 'Completado'],
            
            ['project_id' => 'PROJ7', 'sku_id' => 's1', 'name' => 'Escritorio Lincoln 140', 'code' => 'LNB-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ7', 'sku_id' => 's2', 'name' => 'Escritorio Lincoln L', 'code' => 'LNB-002', 'status' => 'Completado'],
            ['project_id' => 'PROJ7', 'sku_id' => 's3', 'name' => 'Módulo Complementario', 'code' => 'LNB-003', 'status' => 'En proceso'],
            
            ['project_id' => 'PROJ18', 'sku_id' => 's1', 'name' => 'Mampara Estándar', 'code' => 'ST2-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ18', 'sku_id' => 's2', 'name' => 'Mampara en L', 'code' => 'ST2-002', 'status' => 'En proceso'],
            
            ['project_id' => 'PROJ19', 'sku_id' => 's1', 'name' => 'Mampara Comodín', 'code' => 'IW-001', 'status' => 'En proceso'],
            
            ['project_id' => 'PROJ21', 'sku_id' => 's1', 'name' => 'Attesa Base', 'code' => 'AT2-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ21', 'sku_id' => 's2', 'name' => 'Attesa con Brazos', 'code' => 'AT2-002', 'status' => 'En proceso'],
            
            ['project_id' => 'PROJ25a', 'sku_id' => 's1', 'name' => 'Anzio Base Refresh', 'code' => 'ANZ-001', 'status' => 'Completado'],
            
            ['project_id' => 'PROJ26', 'sku_id' => 's1', 'name' => 'Vetta Ejecutiva', 'code' => 'VT2-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ26', 'sku_id' => 's2', 'name' => 'Vetta Visitante', 'code' => 'VT2-002', 'status' => 'En proceso'],
            
            ['project_id' => 'PROJ28', 'sku_id' => 's1', 'name' => 'Sillón Lounge A', 'code' => 'PST-001', 'status' => 'Completado'],
            ['project_id' => 'PROJ28', 'sku_id' => 's2', 'name' => 'Taburete Alto', 'code' => 'PST-002', 'status' => 'En proceso'],
        ];
        
        // Obtener IDs reales de proyectos
        foreach ($skusData as &$sku) {
            $project = DB::table('projects')->where('project_id', $sku['project_id'])->first();
            if ($project) {
                $sku['project_id'] = $project->id;
                $sku['checklist'] = json_encode($this->getDefaultChecklist($sku['status']));
                $sku['created_at'] = now();
                $sku['updated_at'] = now();
                unset($sku['status']); // ya no necesitamos status como campo separado
            }
        }
        
        DB::table('skus')->insert($skusData);
    }
    
    private function getDefaultChecklist($status)
    {
        $checklist = [
            'Modelo 3D' => false,
            'Planos' => false,
            'DXF' => false,
            'IGS' => false,
            'Instructivo' => false,
            'Ficha técnica' => false,
            'Precio lista' => false,
            'Alta ERP' => false,
            'Empaque' => false,
        ];
        
        if ($status === 'Completado') {
            foreach ($checklist as $key => $value) {
                $checklist[$key] = true;
            }
        } elseif ($status === 'En proceso') {
            $checklist['Modelo 3D'] = true;
            $checklist['Planos'] = true;
            $checklist['DXF'] = true;
        }
        
        return $checklist;
    }
}
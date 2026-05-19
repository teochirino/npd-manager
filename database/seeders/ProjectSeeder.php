<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Project;
use App\Models\User;
use App\Models\Sku;
use Illuminate\Support\Facades\DB;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener usuarios ingenieros
        $engineers = User::whereIn('perfil', ['RB', 'FR', 'AE', 'PR', 'RE'])->get();
        
        if ($engineers->isEmpty()) {
            $this->command->warn('No hay ingenieros en la base de datos. Crea usuarios primero.');
            return;
        }

        $projects = [
            [
                'project_id' => 'NPD-2026-001',
                'name' => 'Línea Benching AURA',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(45),
                'progress' => 75,
                'status' => 'En linea',
                'active_phase' => 'Desarrollo',
                'phase_date' => now()->subDays(10),
            ],
            [
                'project_id' => 'NPD-2026-002',
                'name' => 'Espacio maker',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(30),
                'progress' => 60,
                'status' => 'En linea',
                'active_phase' => 'Desarrollo',
                'phase_date' => now()->subDays(15),
            ],
            [
                'project_id' => 'NPD-2026-003',
                'name' => 'Aura 2.0',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(60),
                'progress' => 45,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => now()->subDays(5),
            ],
            [
                'project_id' => 'NPD-2026-004',
                'name' => 'Línea Filio 2.0',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(90),
                'progress' => 30,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => now()->subDays(3),
            ],
            [
                'project_id' => 'NPD-2026-005',
                'name' => 'Cabina Acústica',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(5),
                'progress' => 85,
                'status' => 'En linea',
                'active_phase' => 'Implementacion',
                'phase_date' => now()->subDays(20),
            ],
            [
                'project_id' => 'NPD-2025-028',
                'name' => 'Silla Ejecutiva Premium',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->subDays(10),
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Seguimiento',
                'phase_date' => now()->subDays(30),
            ],
            [
                'project_id' => 'NPD-2026-006',
                'name' => 'Mesa Colaborativa Smart',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(120),
                'progress' => 20,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => now()->subDays(2),
            ],
            [
                'project_id' => 'NPD-2026-007',
                'name' => 'Locker Inteligente',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(75),
                'progress' => 55,
                'status' => 'En linea',
                'active_phase' => 'Desarrollo',
                'phase_date' => now()->subDays(12),
            ],
            [
                'project_id' => 'NPD-2026-008',
                'name' => 'Silla Operativa Ergonómica',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(40),
                'progress' => 70,
                'status' => 'En linea',
                'active_phase' => 'Desarrollo',
                'phase_date' => now()->subDays(18),
            ],
            [
                'project_id' => 'NPD-2026-009',
                'name' => 'Módulo de Almacenamiento Vertical',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(15),
                'progress' => 90,
                'status' => 'En linea',
                'active_phase' => 'Lanzamiento',
                'phase_date' => now()->subDays(25),
            ],
            [
                'project_id' => 'NPD-2025-030',
                'name' => 'Escritorio Ajustable Eléctrico',
                'engineer_id' => $engineers->random()->id,
                'final_date' => null,
                'progress' => 0,
                'status' => 'En pausa',
                'active_phase' => 'Diseño',
                'phase_date' => now()->subDays(60),
            ],
            [
                'project_id' => 'NPD-2026-010',
                'name' => 'Pizarra Interactiva Digital',
                'engineer_id' => $engineers->random()->id,
                'final_date' => now()->addDays(100),
                'progress' => 35,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => now()->subDays(8),
            ],
        ];

        foreach ($projects as $projectData) {
            $project = Project::create($projectData);
            
            // Crear algunos SKUs para proyectos avanzados
            if ($project->progress >= 50) {
                $skuCount = rand(1, 3);
                for ($i = 1; $i <= $skuCount; $i++) {
                    Sku::create([
                        'project_id' => $project->id,
                        'sku_id' => $project->project_id . '-SKU' . str_pad($i, 2, '0', STR_PAD_LEFT),
                        'name' => $project->name . ' - Variante ' . $i,
                        'code' => 'SKU' . rand(10000, 99999),
                        'status' => $project->progress >= 80 ? 'Completado' : 'En proceso',
                        'checklist' => [
                            'Planos técnicos' => $project->progress >= 60,
                            'Fotografía' => $project->progress >= 70,
                            'Ficha técnica' => $project->progress >= 75,
                            'Precio definido' => $project->progress >= 80,
                            'Alta en ERP' => $project->progress >= 85,
                        ]
                    ]);
                }
            }
        }

        $this->command->info('✅ ' . count($projects) . ' proyectos de prueba creados exitosamente.');
    }
}

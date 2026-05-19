<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ProjectsTableSeeder extends Seeder
{
    public function run(): void
    {
        // Obtener IDs de los usuarios por su perfil
        $users = [];
        foreach (DB::table('users')->get() as $user) {
            $users[$user->perfil] = $user->id;
        }

        $projects = [
            [
                'project_id' => 'PROJ1',
                'name' => 'Línea Benching AURA',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-04-17',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2026-03-13'
            ],
            [
                'project_id' => 'PROJ2',
                'name' => 'Espacio maker',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2025-11-28',
                'progress' => 75,
                'status' => 'En pausa',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-11-28'
            ],
            [
                'project_id' => 'PROJ3',
                'name' => 'Aura 2.0',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-05-01',
                'progress' => 0,
                'status' => 'En pausa',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-03-06'
            ],
            [
                'project_id' => 'PROJ4',
                'name' => 'Línea Filio 2.0',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2025-12-19',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-08-08'
            ],
            [
                'project_id' => 'PROJ5',
                'name' => 'Comodines para Vegetación',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-03-06',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2026-03-06'
            ],
            [
                'project_id' => 'PROJ6',
                'name' => 'Línea Económica Distribuidores',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-08-08',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Implementacion',
                'phase_date' => '2026-01-02'
            ],
            [
                'project_id' => 'PROJ7',
                'name' => 'Línea Lincoln NBF',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2026-03-16',
                'progress' => 96,
                'status' => 'En pausa',
                'active_phase' => 'Implementacion',
                'phase_date' => '2026-02-27'
            ],
            [
                'project_id' => 'PROJ8',
                'name' => 'Cabina Acústica',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-07-24',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-10-24'
            ],
            [
                'project_id' => 'PROJ9',
                'name' => 'Linea de Mamparas Setto',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-08-25',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-07-24'
            ],
            [
                'project_id' => 'PROJ10',
                'name' => 'Línea Vetta',
                'engineer_id' => $users['AE'] ?? 1,
                'final_date' => '2025-08-25',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-07-25'
            ],
            [
                'project_id' => 'PROJ11',
                'name' => 'Propuestas ley silla',
                'engineer_id' => $users['AE'] ?? 1,
                'final_date' => '2025-08-08',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-08-07'
            ],
            [
                'project_id' => 'PROJ12',
                'name' => 'Mesas de comensales',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-07-28',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-08-08'
            ],
            [
                'project_id' => 'PROJ13',
                'name' => 'Mesas de capacitacion',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2025-07-29',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-07-28'
            ],
            [
                'project_id' => 'PROJ14',
                'name' => 'Mamparas tapizadas',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-07-30',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2025-07-29'
            ],
            [
                'project_id' => 'PROJ15',
                'name' => 'Archivo único de producto',
                'engineer_id' => $users['AE'] ?? 1,
                'final_date' => '2026-03-31',
                'progress' => 72,
                'status' => 'En pausa',
                'active_phase' => 'Lanzamiento',
                'phase_date' => '2026-02-13'
            ],
            [
                'project_id' => 'PROJ17',
                'name' => 'Typicals Funcionalidad Benching',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2025-08-15',
                'progress' => 100,
                'status' => 'Terminado',
                'active_phase' => 'Desarrollo',
                'phase_date' => '2025-12-31'
            ],
            [
                'project_id' => 'PROJ18',
                'name' => 'Mamparas Setto 2.0',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-12-31',
                'progress' => 98,
                'status' => 'Fuera de linea',
                'active_phase' => 'Implementacion',
                'phase_date' => '2026-02-06'
            ],
            [
                'project_id' => 'PROJ19',
                'name' => 'Remodelación iWork y Privatt',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2025-12-31',
                'progress' => 36,
                'status' => 'Fuera de linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-02-27'
            ],
            [
                'project_id' => 'PROJ21',
                'name' => 'Attesa 2.0',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2025-11-28',
                'progress' => 90,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-03-06'
            ],
            [
                'project_id' => 'PROJ22',
                'name' => 'Mesas de Junta High End',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-04-30',
                'progress' => 16,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-03'
            ],
            [
                'project_id' => 'PROJ23',
                'name' => 'Electrificacion',
                'engineer_id' => $users['RE'] ?? 1,
                'final_date' => '2025-10-31',
                'progress' => 5,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2025-01-30'
            ],
            [
                'project_id' => 'PROJ24',
                'name' => 'Modulos Directivos High End',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-04-30',
                'progress' => 16,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-03'
            ],
            [
                'project_id' => 'PROJ25a',
                'name' => 'Refresh linea Anzio',
                'engineer_id' => $users['AE'] ?? 1,
                'final_date' => '2026-03-09',
                'progress' => 75,
                'status' => 'En pausa',
                'active_phase' => 'Desarrollo',
                'phase_date' => '2026-02-27'
            ],
            [
                'project_id' => 'PROJ25b',
                'name' => 'Refresh linea Anzio (Diseño)',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2025-12-31',
                'progress' => 55,
                'status' => 'En pausa',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-02-27'
            ],
            [
                'project_id' => 'PROJ26',
                'name' => 'Linea Vetta segunda etapa',
                'engineer_id' => $users['AE'] ?? 1,
                'final_date' => '2026-03-09',
                'progress' => 90,
                'status' => 'En pausa',
                'active_phase' => 'Desarrollo',
                'phase_date' => '2026-02-27'
            ],
            [
                'project_id' => 'PROJ27',
                'name' => 'Actualizacion Fichas tecnicas',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2026-01-25',
                'progress' => 40,
                'status' => 'En linea',
                'active_phase' => 'Desarrollo',
                'phase_date' => '2026-03-27'
            ],
            [
                'project_id' => 'PROJ28',
                'name' => 'Prototipos sillones y taburetes',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2026-02-05',
                'progress' => 90,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-03-06'
            ],
            [
                'project_id' => 'PROJ29',
                'name' => 'Attesa 2.0 segunda etapa',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2026-03-02',
                'progress' => 45,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-30'
            ],
            [
                'project_id' => 'PROJ30',
                'name' => 'Sillon doble plaza',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2026-04-13',
                'progress' => 15,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-30'
            ],
            [
                'project_id' => 'PROJ31',
                'name' => 'Islas MultiUsos',
                'engineer_id' => $users['RB'] ?? 1,
                'final_date' => '2026-05-31',
                'progress' => 0,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-24'
            ],
            [
                'project_id' => 'PROJ32',
                'name' => 'Sillon con mampara',
                'engineer_id' => $users['PR'] ?? 1,
                'final_date' => '2026-04-06',
                'progress' => 15,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-06-01'
            ],
            [
                'project_id' => 'PROJ33',
                'name' => 'MAMPARA COMODIN',
                'engineer_id' => $users['FR'] ?? 1,
                'final_date' => '2026-05-15',
                'progress' => 0,
                'status' => 'En linea',
                'active_phase' => 'Diseño',
                'phase_date' => '2026-04-30'
            ],
        ];

        foreach ($projects as $project) {
            DB::table('projects')->updateOrInsert(
                ['project_id' => $project['project_id']],
                $project
            );
        }
    }
}
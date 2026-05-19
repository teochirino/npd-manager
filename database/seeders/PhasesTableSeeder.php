<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PhasesTableSeeder extends Seeder
{
    public function run(): void
    {
        $phases = [
            [
                'name' => 'Diseño',
                'gate_key' => 'Gate 1',
                'gate_name' => 'Concepto aprobado',
                'color' => '#534AB7',
                'bg_color' => '#EEEDFE',
                'text_color' => '#3C3489',
                'order' => 1
            ],
            [
                'name' => 'Desarrollo',
                'gate_key' => 'Gate 2',
                'gate_name' => 'Diseño liberado / Prototipo validado',
                'color' => '#185FA5',
                'bg_color' => '#E6F1FB',
                'text_color' => '#0C447C',
                'order' => 2
            ],
            [
                'name' => 'Implementacion',
                'gate_key' => 'Gate 3',
                'gate_name' => 'Proceso implementado',
                'color' => '#0F6E56',
                'bg_color' => '#E1F5EE',
                'text_color' => '#085041',
                'order' => 3
            ],
            [
                'name' => 'Lanzamiento',
                'gate_key' => 'Gate 4',
                'gate_name' => 'Lanzamiento autorizado',
                'color' => '#854F0B',
                'bg_color' => '#FAEEDA',
                'text_color' => '#633806',
                'order' => 4
            ],
            [
                'name' => 'Seguimiento',
                'gate_key' => 'Gate 5',
                'gate_name' => 'Cierre post-lanzamiento',
                'color' => '#993C1D',
                'bg_color' => '#FAECE7',
                'text_color' => '#712B13',
                'order' => 5
            ]
        ];

        foreach ($phases as $phase) {
            DB::table('phases')->updateOrInsert(
                ['gate_key' => $phase['gate_key']],
                $phase
            );
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Gerente
            [
                'name' => 'Gerente de Innovación',
                'email' => 'gerente@lineaitalia.com',
                'password' => Hash::make('gerente2026'),
                'perfil' => 'GI',
                'role' => 'Gerente de Innovación y Diseño',
                'area' => null,
                'initials' => 'GI'
            ],
            // Ingenieros de Producto
            [
                'name' => 'Ramón Barraza',
                'email' => 'rbarraza@lineaitalia.com',
                'password' => Hash::make('rb2026'),
                'perfil' => 'RB',
                'role' => 'Ingeniero de Producto',
                'area' => null,
                'initials' => 'RB'
            ],
            [
                'name' => 'Francisco de la Rosa',
                'email' => 'fdelarosa@lineaitalia.com',
                'password' => Hash::make('fr2026'),
                'perfil' => 'FR',
                'role' => 'Ingeniero de Producto',
                'area' => null,
                'initials' => 'FR'
            ],
            [
                'name' => 'Alexis Esparza',
                'email' => 'aesparza@lineaitalia.com',
                'password' => Hash::make('ae2026'),
                'perfil' => 'AE',
                'role' => 'Ingeniero de Producto',
                'area' => null,
                'initials' => 'AE'
            ],
            [
                'name' => 'Paulina Román',
                'email' => 'proman@lineaitalia.com',
                'password' => Hash::make('pr2026'),
                'perfil' => 'PR',
                'role' => 'Ingeniero de Producto',
                'area' => null,
                'initials' => 'PR'
            ],
            [
                'name' => 'René Esparza',
                'email' => 'resparza@lineaitalia.com',
                'password' => Hash::make('re2026'),
                'perfil' => 'RE',
                'role' => 'Ingeniero de Producto',
                'area' => null,
                'initials' => 'RE'
            ],
            // Áreas de soporte
            [
                'name' => 'Área de Calidad',
                'email' => 'calidad@lineaitalia.com',
                'password' => Hash::make('calidad2026'),
                'perfil' => 'CAL',
                'role' => 'Calidad',
                'area' => 'CAL',
                'area_color' => '#185FA5',
                'area_bg' => '#E6F1FB',
                'initials' => 'CA'
            ],
            [
                'name' => 'Área de Costos',
                'email' => 'costos@lineaitalia.com',
                'password' => Hash::make('costos2026'),
                'perfil' => 'COS',
                'role' => 'Costos',
                'area' => 'COS',
                'area_color' => '#0F6E56',
                'area_bg' => '#E1F5EE',
                'initials' => 'CO'
            ],
            [
                'name' => 'Área de Procesos',
                'email' => 'procesos@lineaitalia.com',
                'password' => Hash::make('proc2026'),
                'perfil' => 'PRO',
                'role' => 'Procesos',
                'area' => 'PRO',
                'area_color' => '#854F0B',
                'area_bg' => '#FAEEDA',
                'initials' => 'PR'
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->updateOrInsert(
                ['email' => $user['email']],
                $user
            );
        }
    }
}
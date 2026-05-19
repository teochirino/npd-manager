<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            // Gerente de Innovación
            [
                'name' => 'Gerente de Innovación',
                'email' => 'gerente@lineaitalia.com',
                'password' => Hash::make('gerente2026'),
                'perfil' => 'GI',
                'role' => 'Gerente de Innovación y Diseño',
                'initials' => 'GI',
            ],
            // Ingenieros de Producto
            [
                'name' => 'Ramon Barraza',
                'email' => 'rbarraza@lineaitalia.com',
                'password' => Hash::make('rb2026'),
                'perfil' => 'RB',
                'role' => 'Ingeniero de Producto',
                'initials' => 'RB',
            ],
            [
                'name' => 'Francisco de la Rosa',
                'email' => 'fdelarosa@lineaitalia.com',
                'password' => Hash::make('fr2026'),
                'perfil' => 'FR',
                'role' => 'Ingeniero de Producto',
                'initials' => 'FR',
            ],
            [
                'name' => 'Alexis Esparza',
                'email' => 'aesparza@lineaitalia.com',
                'password' => Hash::make('ae2026'),
                'perfil' => 'AE',
                'role' => 'Ingeniero de Producto',
                'initials' => 'AE',
            ],
            [
                'name' => 'Paulina Roman',
                'email' => 'proman@lineaitalia.com',
                'password' => Hash::make('pr2026'),
                'perfil' => 'PR',
                'role' => 'Ingeniero de Producto',
                'initials' => 'PR',
            ],
            [
                'name' => 'Rene Esparza',
                'email' => 'resparza@lineaitalia.com',
                'password' => Hash::make('re2026'),
                'perfil' => 'RE',
                'role' => 'Ingeniero de Producto',
                'initials' => 'RE',
            ],
            // Áreas de Soporte
            [
                'name' => 'Área de Calidad',
                'email' => 'calidad@lineaitalia.com',
                'password' => Hash::make('calidad2026'),
                'perfil' => 'CAL',
                'role' => 'Calidad',
                'area' => 'CAL',
                'area_color' => '#185FA5',
                'area_bg' => '#E6F1FB',
                'initials' => 'CAL',
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
                'initials' => 'COS',
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
                'initials' => 'PRO',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ ' . count($users) . ' usuarios creados/actualizados exitosamente.');
    }
}

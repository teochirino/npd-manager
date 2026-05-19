<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UsersTableSeeder::class,
            PhasesTableSeeder::class,
            TasksPredefinedTableSeeder::class,
            ProjectsTableSeeder::class,
            ProjectTasksTableSeeder::class,
            SkusTableSeeder::class,
            GateApprovalsTableSeeder::class,
        ]);
    }
}
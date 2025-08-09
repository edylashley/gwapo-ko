<?php

namespace Database\Seeders;

use App\Models\ProjectEngineer;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProjectEngineerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $engineers = [
            [
                'name' => 'John Smith',
                'email' => 'john.smith@company.com',
                'specialization' => 'Civil Engineering',
                'is_active' => true,
            ],
            [
                'name' => 'Maria Garcia',
                'email' => 'maria.garcia@company.com',
                'specialization' => 'Electrical Engineering',
                'is_active' => true,
            ],
            [
                'name' => 'David Chen',
                'email' => 'david.chen@company.com',
                'specialization' => 'Mechanical Engineering',
                'is_active' => true,
            ],
            [
                'name' => 'Sarah Johnson',
                'email' => 'sarah.johnson@company.com',
                'specialization' => 'Structural Engineering',
                'is_active' => true,
            ],
            [
                'name' => 'Michael Brown',
                'email' => 'michael.brown@company.com',
                'specialization' => 'Environmental Engineering',
                'is_active' => true,
            ],
        ];

        foreach ($engineers as $engineer) {
            ProjectEngineer::create($engineer);
        }
    }
}

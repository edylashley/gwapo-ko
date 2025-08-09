<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Engineer;

class EngineerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add some additional engineers for monthly assignments
        $additionalEngineers = [
            [
                'name' => 'Maria Santos',
                'email' => 'maria.santos@company.com',
                'specialization' => 'Civil Engineering',
                'can_be_project_engineer' => true,
                'can_be_monthly_engineer' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Carlos Rodriguez',
                'email' => 'carlos.rodriguez@company.com',
                'specialization' => 'Mechanical Engineering',
                'can_be_project_engineer' => false,
                'can_be_monthly_engineer' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Ana Dela Cruz',
                'email' => 'ana.delacruz@company.com',
                'specialization' => 'Electrical Engineering',
                'can_be_project_engineer' => false,
                'can_be_monthly_engineer' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Roberto Gonzales',
                'email' => 'roberto.gonzales@company.com',
                'specialization' => 'Structural Engineering',
                'can_be_project_engineer' => true,
                'can_be_monthly_engineer' => true,
                'is_active' => true,
            ],
            [
                'name' => 'Lisa Chen',
                'email' => 'lisa.chen@company.com',
                'specialization' => 'Environmental Engineering',
                'can_be_project_engineer' => false,
                'can_be_monthly_engineer' => true,
                'is_active' => true,
            ],
        ];

        foreach ($additionalEngineers as $engineer) {
            Engineer::create($engineer);
        }
    }
}

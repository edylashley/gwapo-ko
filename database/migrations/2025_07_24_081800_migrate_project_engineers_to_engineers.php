<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Migrate data from project_engineers to engineers table
        $projectEngineers = DB::table('project_engineers')->get();

        foreach ($projectEngineers as $projectEngineer) {
            DB::table('engineers')->insert([
                'id' => $projectEngineer->id,
                'name' => $projectEngineer->name,
                'email' => $projectEngineer->email,
                'specialization' => $projectEngineer->specialization,
                'can_be_project_engineer' => true,  // They were project engineers
                'can_be_monthly_engineer' => true,  // They can also work monthly
                'is_active' => $projectEngineer->is_active,
                'created_at' => $projectEngineer->created_at,
                'updated_at' => $projectEngineer->updated_at,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove migrated engineers (only those that came from project_engineers)
        $projectEngineerIds = DB::table('project_engineers')->pluck('id');
        DB::table('engineers')->whereIn('id', $projectEngineerIds)->delete();
    }
};

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
        Schema::table('monthly_assignments', function (Blueprint $table) {
            // Drop the problematic unique constraint that prevents multiple team members
            $table->dropUnique('unique_team_head_per_project_month');

            // Add a unique constraint to prevent the same engineer from being assigned
            // to the same project multiple times in the same month
            $table->unique(['project_id', 'engineer_id', 'year', 'month'], 'unique_engineer_project_month');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('monthly_assignments', function (Blueprint $table) {
            // Drop the new constraint
            $table->dropUnique('unique_engineer_project_month');

            // Restore the original problematic constraint
            $table->unique(['project_id', 'year', 'month', 'is_team_head'], 'unique_team_head_per_project_month');
        });
    }
};

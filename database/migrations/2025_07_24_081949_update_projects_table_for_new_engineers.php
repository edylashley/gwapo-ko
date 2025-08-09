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
        // First, let's check if the foreign key exists and drop it if it does
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'projects'
            AND COLUMN_NAME = 'project_engineer_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        if (!empty($foreignKeys)) {
            $constraintName = $foreignKeys[0]->CONSTRAINT_NAME;
            DB::statement("ALTER TABLE projects DROP FOREIGN KEY `{$constraintName}`");
        }

        // Add new foreign key to engineers table
        Schema::table('projects', function (Blueprint $table) {
            $table->foreign('project_engineer_id')->references('id')->on('engineers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Drop the new foreign key constraint
            $table->dropForeign(['project_engineer_id']);

            // Restore the old foreign key to project_engineers table
            $table->foreign('project_engineer_id')->references('id')->on('project_engineers')->onDelete('set null');
        });
    }
};

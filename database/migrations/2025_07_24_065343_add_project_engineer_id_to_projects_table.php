<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedBigInteger('project_engineer_id')->nullable()->after('fpp_code');
            $table->foreign('project_engineer_id')->references('id')->on('project_engineers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropForeign(['project_engineer_id']);
            $table->dropColumn('project_engineer_id');
        });
    }
};

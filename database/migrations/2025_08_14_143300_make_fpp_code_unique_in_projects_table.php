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
        // First, handle any null values
        \DB::table('projects')->whereNull('fpp_code')->update(['fpp_code' => \DB::raw('CONCAT("FPP-", id)')]);
        
        // Handle any duplicate FPP codes by appending a random string
        $duplicates = \DB::table('projects')
            ->select('fpp_code', \DB::raw('COUNT(*) as count'))
            ->groupBy('fpp_code')
            ->having('count', '>', 1)
            ->get();
            
        foreach ($duplicates as $duplicate) {
            $projects = \DB::table('projects')
                ->where('fpp_code', $duplicate->fpp_code)
                ->orderBy('id')
                ->skip(1) // Keep the first one as is
                ->get();
                
            foreach ($projects as $index => $project) {
                // Append a random string to make the FPP code unique
                $newCode = $project->fpp_code . '-' . strtolower(\Illuminate\Support\Str::random(4));
                \DB::table('projects')
                    ->where('id', $project->id)
                    ->update(['fpp_code' => $newCode]);
            }
        }
        
        // Now make the column not nullable and add unique constraint
        Schema::table('projects', function (Blueprint $table) {
            $table->string('fpp_code')->nullable(false)->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropUnique(['fpp_code']);
            $table->string('fpp_code')->nullable()->change();
        });
    }
};

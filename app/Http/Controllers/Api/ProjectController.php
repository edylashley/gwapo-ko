<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    /**
     * Display the specified project.
     *
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id): JsonResponse
    {
        try {
            $project = Project::with('projectEngineer')
                ->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $project->id,
                    'name' => $project->name,
                    'budget' => $project->budget,
                    'fpp_code' => $project->fpp_code,
                    'project_engineer_id' => $project->project_engineer_id,
                    'created_at' => $project->created_at,
                    'updated_at' => $project->updated_at,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
                'error' => $e->getMessage()
            ], 404);
        }
    }
}

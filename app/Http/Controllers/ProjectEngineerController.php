<?php

namespace App\Http\Controllers;

use App\Models\ProjectEngineer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProjectEngineerController extends Controller
{
    public function index()
    {
        $engineers = ProjectEngineer::active()->get();
        return response()->json($engineers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:project_engineers,email',
            'specialization' => 'nullable|string|max:255'
        ]);

        try {
            $engineer = ProjectEngineer::create($validated);

            Log::info('Project engineer created successfully', [
                'engineer_id' => $engineer->id,
                'name' => $engineer->name
            ]);

            return response()->json([
                'success' => true,
                'message' => "Engineer '{$engineer->name}' has been added successfully.",
                'engineer' => $engineer
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating project engineer', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding engineer. Please try again.'
            ], 500);
        }
    }

    public function show(ProjectEngineer $engineer)
    {
        $engineer->load('projects');
        return response()->json($engineer);
    }

    public function update(Request $request, ProjectEngineer $engineer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:project_engineers,email,' . $engineer->id,
            'specialization' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        try {
            $engineer->update($validated);

            return response()->json([
                'success' => true,
                'message' => "Engineer '{$engineer->name}' has been updated successfully.",
                'engineer' => $engineer
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating engineer. Please try again.'
            ], 500);
        }
    }

    public function destroy(ProjectEngineer $engineer)
    {
        try {
            // Soft delete by setting is_active to false
            $engineer->update(['is_active' => false]);

            return response()->json([
                'success' => true,
                'message' => "Engineer '{$engineer->name}' has been deactivated successfully."
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deactivating engineer. Please try again.'
            ], 500);
        }
    }
}

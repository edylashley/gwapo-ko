<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Engineer;
use Illuminate\Support\Facades\Log;

class EngineerController extends Controller
{
    public function index()
    {
        $engineers = Engineer::where('is_active', true)->get();
        return response()->json($engineers);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:engineers,email',
            'specialization' => 'nullable|string|max:255',
            'can_be_project_engineer' => 'boolean',
            'can_be_monthly_engineer' => 'boolean'
        ]);

        try {
            $engineer = Engineer::create($validated);

            Log::info('Engineer created successfully', [
                'engineer_id' => $engineer->id,
                'name' => $engineer->name
            ]);

            return response()->json([
                'success' => true,
                'message' => "Engineer '{$engineer->name}' has been added successfully.",
                'engineer' => $engineer
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating engineer', [
                'error' => $e->getMessage(),
                'data' => $validated
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error adding engineer. Please try again.'
            ], 500);
        }
    }

    public function getEngineersForDashboard()
    {
        try {
            $engineers = Engineer::orderBy('name')
                ->get()
                ->map(function($engineer) {
                    return [
                        'id' => $engineer->id,
                        'name' => $engineer->name,
                        'email' => $engineer->email,
                        'specialization' => $engineer->specialization,
                        'can_be_project_engineer' => $engineer->can_be_project_engineer,
                        'can_be_monthly_engineer' => $engineer->can_be_monthly_engineer,
                        'created_at' => $engineer->created_at->toISOString(),
                    ];
                });

            return response()->json($engineers);
        } catch (\Exception $e) {
            Log::error('Error in getEngineersForDashboard: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to load engineers'], 500);
        }
    }

}

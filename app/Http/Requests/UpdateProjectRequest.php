<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Allowing all authenticated users to update projects
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Get the project ID from the route parameters
        $projectId = $this->route('project') ? $this->route('project')->id : $this->route('id');
        
        return [
            'name' => 'required|string|max:255',
            'budget' => 'required|numeric|min:0',
            'fpp_code' => [
                'nullable',
                'string',
                'max:50',
                'regex:/^[A-Za-z0-9-]+$/',
                'unique:projects,fpp_code,' . $projectId,
            ],
            'project_engineer_id' => 'nullable|exists:engineers,id',
        ];
    }
    
    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'fpp_code.unique' => 'This FPP code is already in use. Please choose a different code or leave it blank to auto-generate one.',
            'fpp_code.regex' => 'The FPP code may only contain letters, numbers, and hyphens.',
        ];
    }
}

<!-- Team Display Partial -->
@if($monthAssignments->count() > 0)
    <!-- Current Team Display -->
    <div class="space-y-2">
        @if($teamHead)
            <!-- Team Head -->
            <div class="bg-blue-50 border border-blue-200 rounded-md p-3">
                <div class="flex items-center justify-between">
                    <div class="flex-1">
                        <div class="flex items-center">
                            <span class="font-medium text-blue-800">{{ $teamHead->engineer->name }}</span>
                            <span class="bg-blue-600 text-white px-2 py-1 rounded text-xs ml-2">TEAM HEAD</span>
                        </div>
                        <div class="text-xs text-blue-600 mt-1">
                            {{ $teamHead->engineer->specialization }} • Assigned: {{ $teamHead->assigned_at->format('M d, Y') }}
                        </div>
                        @if(auth()->user()->is_admin)
                            <div class="mt-2 flex items-center space-x-2">
                                <label class="text-xs text-blue-700 font-medium">Salary:</label>
                                <input
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    value="{{ $teamHead->salary ?? '' }}"
                                    placeholder="0.00"
                                    class="w-24 px-2 py-1 text-xs border border-blue-300 rounded focus:outline-none focus:ring-1 focus:ring-blue-500"
                                    id="salary_{{ $project->id }}_{{ $teamHead->engineer->id }}_{{ $year }}_{{ $assignmentMonth }}"
                                >
                                <span class="text-xs text-blue-600">₱</span>
                                <button
                                    id="apply_salary_{{ $project->id }}_{{ $teamHead->engineer->id }}_{{ $year }}_{{ $assignmentMonth }}"
                                    onclick="applySalary({{ $project->id }}, {{ $teamHead->engineer->id }}, {{ $year }}, {{ $assignmentMonth }}, '{{ $teamHead->engineer->name }}')"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors font-medium">
                                    Apply
                                </button>
                               
                                
                            </div>
                        @else
                            @if($teamHead->salary)
                                <div class="mt-1 text-xs text-blue-700">
                                    <strong>Salary: ₱{{ number_format($teamHead->salary, 2) }}</strong>
                                </div>
                            @endif
                        @endif
                    </div>
                    @if(auth()->user()->is_admin)
                        <button
                            onclick="removeEngineerFromTeam({{ $project->id }}, {{ $teamHead->engineer->id }}, {{ $year }}, {{ $assignmentMonth }}, '{{ $teamHead->engineer->name }}', '{{ $project->name }}')"
                            class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors ml-2"
                            title="Remove from team">
                            Remove
                        </button>
                    @endif
                </div>
            </div>
        @endif

        @if($teamMembers->count() > 0)
            <!-- Team Members -->
            @foreach($teamMembers as $member)
                <div class="bg-green-50 border border-green-200 rounded-md p-3">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <div class="flex items-center">
                                <span class="font-medium text-green-800">{{ $member->engineer->name }}</span>
                                <span class="bg-green-600 text-white px-2 py-1 rounded text-xs ml-2">MEMBER</span>
                            </div>
                            <div class="text-xs text-green-600 mt-1">
                                {{ $member->engineer->specialization }} • Assigned: {{ $member->assigned_at->format('M d, Y') }}
                            </div>
                            @if(auth()->user()->is_admin)
                                <div class="mt-2 flex items-center space-x-2">
                                    <label class="text-xs text-green-700 font-medium">Salary:</label>
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        value="{{ $member->salary ?? '' }}"
                                        placeholder="0.00"
                                        class="w-24 px-2 py-1 text-xs border border-green-300 rounded focus:outline-none focus:ring-1 focus:ring-green-500"
                                        id="salary_{{ $project->id }}_{{ $member->engineer->id }}_{{ $year }}_{{ $assignmentMonth }}"
                                    >
                                    <span class="text-xs text-green-600">₱</span>
                                    <button
                                        id="apply_salary_{{ $project->id }}_{{ $member->engineer->id }}_{{ $year }}_{{ $assignmentMonth }}"
                                        onclick="applySalary({{ $project->id }}, {{ $member->engineer->id }}, {{ $year }}, {{ $assignmentMonth }}, '{{ $member->engineer->name }}')"
                                        class="bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded text-xs transition-colors font-medium">
                                        Apply
                                    </button>
                                    <button
                                        onclick="setTeamHead({{ $project->id }}, {{ $member->engineer->id }}, {{ $year }}, {{ $assignmentMonth }}, '{{ $member->engineer->name }}', '{{ $project->name }}')"
                                        class="bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded text-xs transition-colors font-medium ml-1"
                                        title="Make Team Head">
                                        Make Head
                                    </button>
                                </div>
                            @else
                                @if($member->salary)
                                    <div class="mt-1 text-xs text-green-700">
                                        <strong>Salary: ₱{{ number_format($member->salary, 2) }}</strong>
                                    </div>
                                @endif
                            @endif
                        </div>
                        @if(auth()->user()->is_admin)
                            <button
                                onclick="removeEngineerFromTeam({{ $project->id }}, {{ $member->engineer->id }}, {{ $year }}, {{ $assignmentMonth }}, '{{ $member->engineer->name }}', '{{ $project->name }}')"
                                class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs transition-colors ml-2"
                                title="Remove from team">
                                Remove
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @endif

        <!-- Team Summary -->
        <div class="text-center text-sm text-green-100 mt-2">
            Total Team Size: {{ $monthAssignments->count() }} engineer{{ $monthAssignments->count() > 1 ? 's' : '' }}
        </div>
    </div>
@endif

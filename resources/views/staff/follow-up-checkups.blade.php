@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Follow-Up Checkups Management</h1>
            <p class="text-gray-600">Schedule and manage patient follow-up appointments</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Summary Statistics -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-blue-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-calendar text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-blue-600">Total Follow-ups</div>
                        <div class="text-2xl font-bold text-blue-900">{{ $totalFollowUps }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-error text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-red-600">Urgent</div>
                        <div class="text-2xl font-bold text-red-900">{{ $urgentFollowUps }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-orange-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-time text-orange-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-orange-600">Overdue</div>
                        <div class="text-2xl font-bold text-orange-900">{{ $overdueFollowUps }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-calendar-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-green-600">Today</div>
                        <div class="text-2xl font-bold text-green-900">{{ $todayFollowUps }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-6 flex space-x-4">
            <a href="{{ route('staff.urgent-follow-ups') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-error mr-2"></i>Urgent Follow-ups
            </a>
            <a href="{{ route('staff.overdue-follow-ups') }}" class="bg-orange-500 hover:bg-orange-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-time mr-2"></i>Overdue Follow-ups
            </a>
            <a href="{{ route('staff.today-follow-ups') }}" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-calendar-check mr-2"></i>Today's Follow-ups
            </a>
        </div>

        <!-- Create New Follow-up Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Schedule New Follow-up Checkup</h2>
            <form action="{{ route('follow-up-checkups.store') }}" method="POST" class="space-y-4">
                @csrf
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div>
                        <label for="patient_id" class="block text-sm font-medium text-gray-700 mb-1">Patient</label>
                        <select name="patient_id" id="patient_id" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Patient</option>
                            @foreach($patients as $patient)
                                <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="health_monitoring_id" class="block text-sm font-medium text-gray-700 mb-1">Health Monitoring (Optional)</label>
                        <select name="health_monitoring_id" id="health_monitoring_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Health Record</option>
                            @foreach($healthMonitorings as $monitoring)
                                <option value="{{ $monitoring->id }}">
                                    {{ $monitoring->patient->name }} - {{ $monitoring->recorded_date->format('M d, Y') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="staff_id" class="block text-sm font-medium text-gray-700 mb-1">Assigned Staff</label>
                        <select name="staff_id" id="staff_id" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Staff</option>
                            @foreach($staff as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="reason" class="block text-sm font-medium text-gray-700 mb-1">Reason for Follow-up</label>
                        <input type="text" name="reason" id="reason" placeholder="e.g., High blood pressure, Abnormal test results" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Follow-up Type</label>
                        <select name="type" id="type" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="general">General Checkup</option>
                            <option value="specialist">Specialist Consultation</option>
                            <option value="lab_test">Laboratory Test</option>
                            <option value="imaging">Imaging Study</option>
                            <option value="medication_review">Medication Review</option>
                        </select>
                    </div>

                    <div>
                        <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" id="priority" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="low">Low Priority</option>
                            <option value="medium" selected>Medium Priority</option>
                            <option value="high">High Priority</option>
                            <option value="urgent">Urgent</option>
                        </select>
                    </div>

                    <div>
                        <label for="scheduled_date" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Date</label>
                        <input type="date" name="scheduled_date" id="scheduled_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="scheduled_time" class="block text-sm font-medium text-gray-700 mb-1">Scheduled Time</label>
                        <input type="time" name="scheduled_time" id="scheduled_time" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">Duration (minutes)</label>
                        <input type="number" name="duration" id="duration" value="30" min="15" max="240" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                        <input type="text" name="location" id="location" placeholder="e.g., Room 101, Lab 2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div class="lg:col-span-3">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" id="description" rows="3" placeholder="Detailed description of why follow-up is needed..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="lg:col-span-3">
                        <div class="flex space-x-4">
                            <label class="flex items-center">
                                <input type="checkbox" name="is_urgent" value="1" id="is_urgent" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                                <span class="ml-2 text-sm text-gray-700">Mark as Urgent</span>
                            </label>
                        </div>
                    </div>

                    <div class="lg:col-span-3" id="urgent_reason_div" style="display: none;">
                        <label for="urgent_reason" class="block text-sm font-medium text-gray-700 mb-1">Urgent Reason</label>
                        <textarea name="urgent_reason" id="urgent_reason" rows="2" placeholder="Explain why this follow-up is urgent..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="lg:col-span-3">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition duration-200">
                        Schedule Follow-up
                    </button>
                </div>
            </form>
        </div>

        <!-- Follow-ups Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">All Follow-up Checkups</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Priority</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($followUps as $followUp)
                            <tr class="hover:bg-gray-50 {{ $followUp->isOverdue() ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $followUp->patient->name }}</div>
                                    @if($followUp->staff)
                                        <div class="text-sm text-gray-500">Assigned to: {{ $followUp->staff->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $followUp->reason }}</div>
                                    @if($followUp->isUrgent())
                                        <div class="text-xs text-red-600 font-semibold">URGENT</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $followUp->getTypeText() }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $followUp->scheduled_date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($followUp->scheduled_time)->format('g:i A') }}</div>
                                    @if($followUp->isOverdue())
                                        <div class="text-xs text-red-600">OVERDUE</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $followUp->getPriorityBadgeClass() }}">
                                        {{ $followUp->getPriorityText() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $followUp->getStatusBadgeClass() }}">
                                        {{ ucfirst(str_replace('_', ' ', $followUp->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('follow-up-checkups.show', $followUp->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        View Details
                                    </a>
                                    @if($followUp->status === 'scheduled')
                                        <form action="{{ route('follow-up-checkups.complete', $followUp->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                                Mark Complete
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('follow-up-checkups.destroy', $followUp->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this follow-up?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No follow-up checkups found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($followUps->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $followUps->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Set minimum date to today for scheduled_date
    document.getElementById('scheduled_date').min = new Date().toISOString().split('T')[0];
    
    // Show/hide urgent reason field based on urgent checkbox
    document.getElementById('is_urgent').addEventListener('change', function() {
        const urgentReasonDiv = document.getElementById('urgent_reason_div');
        const urgentReasonField = document.getElementById('urgent_reason');
        
        if (this.checked) {
            urgentReasonDiv.style.display = 'block';
            urgentReasonField.required = true;
        } else {
            urgentReasonDiv.style.display = 'none';
            urgentReasonField.required = false;
        }
    });
</script>
@endsection 
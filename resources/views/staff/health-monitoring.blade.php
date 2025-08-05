@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Health Monitoring Management</h1>
            <p class="text-gray-600">Record and track patient vital signs and health metrics</p>
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
                        <i class="bx bx-clipboard text-blue-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-blue-600">Total Records</div>
                        <div class="text-2xl font-bold text-blue-900">{{ $totalRecords }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-check text-green-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-green-600">Normal</div>
                        <div class="text-2xl font-bold text-green-900">{{ $normalRecords }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-yellow-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-exclamation text-yellow-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-yellow-600">Warning</div>
                        <div class="text-2xl font-bold text-yellow-900">{{ $warningRecords }}</div>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 rounded-lg p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <i class="bx bx-error text-red-600 text-2xl"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-red-600">Critical</div>
                        <div class="text-2xl font-bold text-red-900">{{ $criticalRecords }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mb-6 flex space-x-4">
            <a href="{{ route('staff.critical-alerts') }}" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-error mr-2"></i>Critical Alerts
            </a>
            <a href="{{ route('staff.today-health-records') }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-calendar-check mr-2"></i>Today's Records
            </a>
        </div>

        <!-- Create New Health Record Form -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <h2 class="text-xl font-semibold mb-4">Record New Health Data</h2>
            <form action="{{ route('health-monitoring.store') }}" method="POST" class="space-y-4">
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
                        <label for="recorded_date" class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                        <input type="date" name="recorded_date" id="recorded_date" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="recorded_time" class="block text-sm font-medium text-gray-700 mb-1">Time</label>
                        <input type="time" name="recorded_time" id="recorded_time" required class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Vital Signs -->
                    <div>
                        <label for="temperature" class="block text-sm font-medium text-gray-700 mb-1">Temperature (°C)</label>
                        <input type="number" name="temperature" id="temperature" step="0.1" min="30" max="45" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="blood_pressure_systolic" class="block text-sm font-medium text-gray-700 mb-1">BP Systolic (mmHg)</label>
                        <input type="number" name="blood_pressure_systolic" id="blood_pressure_systolic" min="70" max="250" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="blood_pressure_diastolic" class="block text-sm font-medium text-gray-700 mb-1">BP Diastolic (mmHg)</label>
                        <input type="number" name="blood_pressure_diastolic" id="blood_pressure_diastolic" min="40" max="150" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="pulse_rate" class="block text-sm font-medium text-gray-700 mb-1">Pulse Rate (bpm)</label>
                        <input type="number" name="pulse_rate" id="pulse_rate" min="30" max="200" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="respiratory_rate" class="block text-sm font-medium text-gray-700 mb-1">Respiratory Rate (bpm)</label>
                        <input type="number" name="respiratory_rate" id="respiratory_rate" min="8" max="40" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="oxygen_saturation" class="block text-sm font-medium text-gray-700 mb-1">Oxygen Saturation (%)</label>
                        <input type="number" name="oxygen_saturation" id="oxygen_saturation" step="0.1" min="70" max="100" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Body Measurements -->
                    <div>
                        <label for="weight" class="block text-sm font-medium text-gray-700 mb-1">Weight (kg)</label>
                        <input type="number" name="weight" id="weight" step="0.01" min="20" max="300" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="height" class="block text-sm font-medium text-gray-700 mb-1">Height (cm)</label>
                        <input type="number" name="height" id="height" step="0.01" min="50" max="250" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="blood_sugar" class="block text-sm font-medium text-gray-700 mb-1">Blood Sugar (mg/dL)</label>
                        <input type="number" name="blood_sugar" id="blood_sugar" step="0.01" min="50" max="600" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Pain and Mood -->
                    <div>
                        <label for="pain_level" class="block text-sm font-medium text-gray-700 mb-1">Pain Level (0-10)</label>
                        <input type="number" name="pain_level" id="pain_level" min="0" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="pain_location" class="block text-sm font-medium text-gray-700 mb-1">Pain Location</label>
                        <input type="text" name="pain_location" id="pain_location" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <div>
                        <label for="mood_score" class="block text-sm font-medium text-gray-700 mb-1">Mood Score (1-10)</label>
                        <input type="number" name="mood_score" id="mood_score" min="1" max="10" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Symptoms -->
                    <div class="lg:col-span-3">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Symptoms</label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            @php
                                $symptoms = ['Fever', 'Cough', 'Headache', 'Fatigue', 'Nausea', 'Dizziness', 'Chest Pain', 'Shortness of Breath', 'Joint Pain', 'Swelling', 'Rash', 'Insomnia'];
                            @endphp
                            @foreach($symptoms as $symptom)
                                <label class="flex items-center">
                                    <input type="checkbox" name="symptoms[]" value="{{ $symptom }}" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700">{{ $symptom }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="lg:col-span-3">
                        <label for="observations" class="block text-sm font-medium text-gray-700 mb-1">Observations</label>
                        <textarea name="observations" id="observations" rows="3" placeholder="Clinical observations..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="lg:col-span-3">
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                        <textarea name="notes" id="notes" rows="3" placeholder="Additional notes..." class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="lg:col-span-3">
                        <label class="flex items-center">
                            <input type="checkbox" name="requires_follow_up" value="1" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Requires Follow-up</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md transition duration-200">
                        Record Health Data
                    </button>
                </div>
            </form>
        </div>

        <!-- Health Records Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Recent Health Records</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vital Signs</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">BMI</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pain/Mood</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($monitorings as $monitoring)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $monitoring->patient->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $monitoring->recorded_date->format('M d, Y') }}</div>
                                    <div class="text-sm text-gray-500">{{ \Carbon\Carbon::parse($monitoring->recorded_time)->format('g:i A') }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>Temp: {{ $monitoring->temperature ? $monitoring->temperature . '°C' : 'N/A' }}</div>
                                    <div>BP: {{ $monitoring->getBloodPressureText() }}</div>
                                    <div>Pulse: {{ $monitoring->pulse_rate ? $monitoring->pulse_rate . ' bpm' : 'N/A' }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($monitoring->bmi)
                                        <div class="text-sm text-gray-900">{{ $monitoring->bmi }}</div>
                                        <div class="text-xs {{ $monitoring->getBmiCategoryClass() }}">{{ $monitoring->getBmiCategory() }}</div>
                                    @else
                                        <span class="text-gray-400">N/A</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div>Pain: {{ $monitoring->getPainLevelText() }}</div>
                                    <div>Mood: {{ $monitoring->getMoodText() }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $monitoring->getStatusBadgeClass() }}">
                                        {{ ucfirst($monitoring->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <a href="{{ route('health-monitoring.show', $monitoring->id) }}" class="text-blue-600 hover:text-blue-900 mr-3">
                                        View Details
                                    </a>
                                    <form action="{{ route('health-monitoring.destroy', $monitoring->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this record?')">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                    No health monitoring records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($monitorings->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $monitorings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Set default date to today
    document.getElementById('recorded_date').value = new Date().toISOString().split('T')[0];
    
    // Set default time to current time
    const now = new Date();
    const timeString = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');
    document.getElementById('recorded_time').value = timeString;
</script>
@endsection 
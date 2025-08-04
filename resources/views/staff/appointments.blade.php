@extends('layouts.app')

@section('content')
    <div x-data="appointmentHandler()" @keydown.escape.window="showModal = showEditModal = false">

        <div class="main-content">
            @if (session('success'))
                <div x-data="{ show: true }" x-init="setTimeout(() => show = false, 4000)" x-show="show"
                    x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2"
                    x-transition:enter-end="opacity-100 translate-y-0" x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0" x-transition:leave-end="opacity-0 translate-y-2"
                    class="fixed top-4 left-1/2 transform -translate-x-1/2 z-[9999] bg-green-100 border border-green-400 text-green-800 px-6 py-3 rounded-md shadow-md w-full max-w-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div class="text-sm leading-snug">
                            <strong class="block font-medium">Success</strong>
                            <span>{{ session('success') }}</span>
                        </div>
                        <button @click="show = false"
                            class="text-xl leading-none text-green-700 hover:text-green-900">&times;</button>
                    </div>
                </div>
            @endif

            <section id="appointments" class="content-section">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white">Appointment Management</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300">Manage scheduled appointments in the system</p>
                    </div>
                    <button id="addAppointmentBtn" @click="showModal = true"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm bg-blue-600 hover:bg-blue-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-offset-gray-800">
                        <ion-icon name="add-circle-outline" class="text-lg"></ion-icon> Add New Appointment
                    </button>
                </div>

                <br>
                <div class="p-4 sm:p-6 overflow-x-auto">
                    <table id="appointmentTable"
                        class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 text-sm font-semibold uppercase tracking-wide sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-3 text-left">ID</th>
                                <th class="px-6 py-3 text-left">Patient ID</th>
                                <th class="px-6 py-3 text-left">Type</th>
                                <th class="px-6 py-3 text-left">Scheduled At</th>
                                <th class="px-6 py-3 text-left">Status</th>
                                <th class="px-6 py-3 text-left">Notes</th>
                                <th class="px-6 py-3 text-left">Duration</th>
                                <th class="px-6 py-3 text-left">Location</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($appointments as $appointment)
                                <tr
                                    class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-all duration-150 ease-in-out transform hover:scale-[1.01]">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->id }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->patient->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->type }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->scheduled_at->format('M j, Y g:i A') }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->status }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->notes }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->duration }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $appointment->location }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <div class="inline-flex space-x-2">
                                            <a href="#" @click.prevent="fetchAppointment({{ $appointment->id }})"
                                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                <ion-icon name="create-outline" class="text-lg"></ion-icon>Edit
                                            </a>

                                            <form action="{{ route('appointments.destroy', $appointment->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this appointment?')"
                                                    class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 font-medium">
                                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="10" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No appointments found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <!-- Modal for Adding Appointment -->
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

            <div @click.away="showModal = false"
                class="w-full max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 space-y-6 transition-all duration-300">

                <!-- Header -->
                <div class="flex items-center justify-between border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Appointment</h2>
                    <button @click="showModal = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                        <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                    </button>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('appointments.store') }}" x-ref="form" class="space-y-6">
                    @csrf

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient</label>
                            <select name="patient_id" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <option value="medicine_pickup">Medicine Pickup</option>
                                <option value="checkup">Checkup</option>
                                <option value="consultation">Consultation</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled At</label>
                            <input name="scheduled_at" type="datetime-local" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="missed">Missed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                            <input name="location" type="text"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                       
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (minutes)</label>
                            <input name="duration" type="number" value="30" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="showModal = false; $refs.form.reset()"
                            class="px-4 py-2 text-sm font-medium bg-gray-100 dark:bg-gray-600 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            Save Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal for Appointments -->
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

            <div @click.away="showEditModal = false"
                class="w-full max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 space-y-6 transition-all duration-300">

                <div class="flex items-center justify-between border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Appointment</h2>
                    <button @click="showEditModal = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                        <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                    </button>
                </div>
                <form :action="'/appointments/' + editingAppointment.id" method="POST" x-ref="editForm" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Patient</label>
                            <select name="patient_id" x-model="editingAppointment.patient_id" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select Patient</option>
                                @foreach($patients as $patient)
                                    <option :value="{{ $patient->id }}">{{ $patient->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Type</label>
                            <select name="type" x-model="editingAppointment.type" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <option value="medicine_pickup">Medicine Pickup</option>
                                <option value="checkup">Checkup</option>
                                <option value="consultation">Consultation</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Scheduled At</label>
                            <input name="scheduled_at" type="datetime-local" x-model="editingAppointment.scheduled_at" 
                                :value="editingAppointment.scheduled_at" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                            <select name="status" x-model="editingAppointment.status" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="missed">Missed</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Location</label>
                            <input name="location" type="text" x-model="editingAppointment.location"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Duration (minutes)</label>
                            <input name="duration" type="number" x-model="editingAppointment.duration" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Notes</label>
                            <textarea name="notes" x-model="editingAppointment.notes"
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="showEditModal = false; editingAppointment = {}"
                            class="px-4 py-2 text-sm font-medium bg-gray-100 dark:bg-gray-600 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            Update Appointment
                        </button>
                    </div>
                </form>
            </div>
        </div>

     
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function appointmentHandler() {
        return {
            showModal: false,
            showEditModal: false,
            editingAppointment: {},
            fetchAppointment(id) {
                console.log('Fetching appointment with ID:', id);
                fetch(`/appointments/${id}/edit`)
                    .then(response => {
                        console.log('Response received:', response);
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Data fetched:', data);
                        
                        // Ensure scheduled_at is properly formatted for datetime-local input
                        if (data.scheduled_at) {
                            // If it's already a string in the correct format, use it as is
                            // If it's a date object, format it
                            if (typeof data.scheduled_at === 'string') {
                                // Make sure it's in the correct format for datetime-local
                                const date = new Date(data.scheduled_at);
                                if (!isNaN(date.getTime())) {
                                    data.scheduled_at = date.toISOString().slice(0, 16);
                                }
                            } else if (data.scheduled_at instanceof Date) {
                                data.scheduled_at = data.scheduled_at.toISOString().slice(0, 16);
                            }
                        }
                        
                        console.log('Formatted data:', data);
                        this.editingAppointment = data;
                        this.showEditModal = true;
                        
                        // Force Alpine to update the DOM after a brief delay
                        this.$nextTick(() => {
                            console.log('Edit modal should now be visible with data:', this.editingAppointment);
                            // Manually set the datetime input value as a backup
                            const datetimeInput = document.querySelector('input[name="scheduled_at"]');
                            if (datetimeInput && this.editingAppointment.scheduled_at) {
                                datetimeInput.value = this.editingAppointment.scheduled_at;
                                console.log('Manually set datetime input value to:', this.editingAppointment.scheduled_at);
                            }
                        });
                    })
                    .catch(error => {
                        console.error('Error fetching appointment:', error);
                        alert('Error loading appointment data. Please try again.');
                    });
            }
        };
    }
</script>
@endpush

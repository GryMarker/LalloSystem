@extends('layouts.app')

@section('content')
    <div x-data="patientHandler()" @keydown.escape.window="showModal = showEditModal = false">


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


            <section id="patients" class="content-section">
                <div
                    class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-semibold text-gray-800 dark:text-white">Patient Management</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-300">Manage registered patients in the system</p>
                    </div>
                    <button id="addPatientBtn" @click="showModal = true"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-lg font-medium text-sm bg-blue-600 hover:bg-blue-700 text-white focus:outline-none focus:ring-2 focus:ring-blue-400 dark:focus:ring-offset-gray-800">
                        <ion-icon name="add-circle-outline" class="text-lg"></ion-icon> Add New Patient
                    </button>
                </div>

                <br>
                <form method="GET" action="{{ route('patients.index') }}" class="px-4 sm:px-6 mb-4">
                    <div class="flex items-center gap-2">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search patients by name, contact, or address..."
                            class="w-full sm:max-w-xs px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            Search
                        </button>
                        @if(request('search'))
                            <a href="{{ route('patients.index') }}"
                                class="px-4 py-2 text-sm font-medium bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-md transition">
                                Clear
                            </a>
                        @endif
                    </div>
                </form>

                <div class="p-4 sm:p-6 overflow-x-auto">

                    <table id="patientTable"
                        class="min-w-full bg-white dark:bg-gray-800 shadow-md rounded-lg overflow-hidden">
                        <thead
                            class="bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-100 text-sm font-semibold uppercase tracking-wide sticky top-0 z-10 shadow-sm">
                            <tr>
                                <th class="px-6 py-3 text-left">ID</th>
                                <th class="px-6 py-3 text-left">Name</th>
                                <th class="px-6 py-3 text-left">Birthdate</th>
                                <th class="px-6 py-3 text-left">Gender</th>
                                <th class="px-6 py-3 text-left">Address</th>
                                <th class="px-6 py-3 text-left">Contact</th>
                                <th class="px-6 py-3 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($patients as $patient)
                                <tr
                                    class="hover:bg-blue-50 dark:hover:bg-gray-700 transition-all duration-150 ease-in-out transform hover:scale-[1.01]">
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->id }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->name }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->birthdate }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->gender }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->address }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm">{{ $patient->contact_number }}</td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-center">
                                        <div class="inline-flex space-x-2">
                                            <a href="#" @click.prevent="fetchPatient({{ $patient->id }})"
                                                class="inline-flex items-center gap-1 text-sm text-blue-600 hover:text-blue-800 font-medium">
                                                <ion-icon name="create-outline" class="text-lg"></ion-icon>Edit
                                            </a>

                                            <form action="{{ route('patients.destroy', $patient->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    onclick="return confirm('Are you sure you want to delete this patient?')"
                                                    class="inline-flex items-center gap-1 text-sm text-red-600 hover:text-red-800 font-medium">
                                                    <ion-icon name="trash-outline" class="text-lg"></ion-icon>Delete
                                                </button>
                                            </form>


                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        No patients found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    
                    



                </div>

                

            </section>

        </div>



        <!-- Modal (correctly inside x-data scope now) -->
        <!-- Modal -->
        <div x-show="showModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

            <div @click.away="showModal = false"
                class="w-full max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 space-y-6 transition-all duration-300">


                <!-- Header -->
                <div class="flex items-center justify-between border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Add New Patient</h2>
                    <button @click="showModal = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                        <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                    </button>
                </div>

                <!-- Form -->
                <!-- Form -->
                <form method="POST" action="{{ route('patients.store') }}" x-ref="form" class="space-y-6">
                    @csrf

                    <!-- Form Fields -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                            <input name="name" type="text" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birthdate</label>
                            <input name="birthdate" type="date" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                            <select name="gender" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact</label>
                            <input name="contact_number" type="text" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <input name="address" type="text" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
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
                            Save Patient
                        </button>
                    </div>
                </form>

            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-90" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-90"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm">

            <div @click.away="showEditModal = false"
                class="w-full max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-8 space-y-6 transition-all duration-300">

                <div class="flex items-center justify-between border-b pb-3">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Edit Patient</h2>
                    <button @click="showEditModal = false"
                        class="text-gray-400 hover:text-gray-700 dark:hover:text-white transition">
                        <ion-icon name="close-outline" class="text-2xl"></ion-icon>
                    </button>
                </div>
                <form :action="'/patients/' + editingPatient.id" method="POST" x-ref="editForm" class="space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Full Name</label>
                            <input name="name" x-model="editingPatient.name" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />

                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Birthdate</label>
                            <input name="birthdate" type="date" x-model="editingPatient.birthdate" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 
                                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
                                                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Gender</label>
                            <select name="gender" x-model="editingPatient.gender" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 
                                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
                                                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select</option>
                                <option>Male</option>
                                <option>Female</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Contact</label>
                            <input name="contact_number" x-model="editingPatient.contact_number" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 
                                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
                                                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Address</label>
                            <input name="address" x-model="editingPatient.address" required
                                class="w-full px-3 py-2 rounded-md border border-gray-300 dark:border-gray-600 
                                                                           bg-white dark:bg-gray-700 text-gray-900 dark:text-white 
                                                                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500" />
                        </div>

                    </div>
                    <div class="flex justify-end gap-3 pt-4 border-t">
                        <button type="button" @click="showEditModal = false; editingPatient = {}"
                            class="px-4 py-2 text-sm font-medium bg-gray-100 dark:bg-gray-600 dark:text-white rounded-md hover:bg-gray-200 dark:hover:bg-gray-500 transition">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium bg-blue-600 hover:bg-blue-700 text-white rounded-md transition">
                            Update Patient
                        </button>
                    </div>

                </form>
            </div>
        </div>






    </div>
@endsection
@push('scripts')
    <script>
        function patientHandler() {
            return {
                showModal: false,
                showEditModal: false,
                editingPatient: {},
                fetchPatient(id) {
                    fetch(`/patients/${id}/edit`)
                        .then(response => response.json())
                        .then(data => {
                            this.editingPatient = data;
                            this.showEditModal = true;
                        })
                        .catch(error => {
                            console.error('Error fetching patient:', error);
                        });
                }
            };
        }
    </script>
@endpush
@extends('layouts.app')

@section('content')
<div class="main-content">
    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Today's Medication Reminders</h1>
            <p class="text-gray-600">{{ now()->format('l, F d, Y') }}</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="mb-6 flex space-x-4">
            <a href="{{ route('staff.dosage-reminders') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-md transition duration-200">
                <i class="bx bx-arrow-back mr-2"></i>Back to All Reminders
            </a>
        </div>

        <!-- Today's Reminders -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-xl font-semibold">Pending Reminders</h2>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Patient</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medicine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dosage</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50 {{ $log->isOverdue() ? 'bg-red-50' : '' }}">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ \Carbon\Carbon::parse($log->scheduled_time)->format('g:i A') }}
                                    </div>
                                    @if($log->isOverdue())
                                        <div class="text-xs text-red-600">Overdue</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $log->patient->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->patient->contact_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $log->medicine->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $log->medicine->brand }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $log->dosageReminder->dosage_instruction }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $log->getStatusBadgeClass() }}">
                                        {{ ucfirst($log->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    @if($log->status === 'pending')
                                        <form action="{{ route('dosage-logs.taken', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-green-600 hover:text-green-900 mr-3">
                                                Mark as Taken
                                            </button>
                                        </form>
                                        <form action="{{ route('dosage-logs.skipped', $log->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="text-gray-600 hover:text-gray-900">
                                                Mark as Skipped
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400">
                                            @if($log->status === 'taken')
                                                Taken at {{ $log->taken_at->format('g:i A') }}
                                            @else
                                                {{ ucfirst($log->status) }}
                                            @endif
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                                    No pending reminders for today.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary Stats -->
        @if($logs->count() > 0)
            <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="bg-blue-50 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bx bx-time text-blue-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-blue-600">Pending</div>
                            <div class="text-2xl font-bold text-blue-900">{{ $logs->where('status', 'pending')->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-green-50 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bx bx-check text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-green-600">Taken</div>
                            <div class="text-2xl font-bold text-green-900">{{ $logs->where('status', 'taken')->count() }}</div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <i class="bx bx-x text-red-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <div class="text-sm font-medium text-red-600">Overdue</div>
                            <div class="text-2xl font-bold text-red-900">{{ $logs->filter(function($log) { return $log->isOverdue(); })->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection 
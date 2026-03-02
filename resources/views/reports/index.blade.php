@extends('layouts.app')

@section('page-title', 'Reports')
@section('page-description', 'Call center analytics and reports')

@section('content')
<div class="space-y-6">
    <!-- Daily Stats -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">Daily Statistics</h2>
        </div>
        <div class="p-6">
            <div id="daily-stats">
                <p class="text-gray-400 animate-pulse text-center py-8">Loading statistics...</p>
            </div>
        </div>
    </div>

    <!-- Call History -->
    <div class="bg-white rounded-lg shadow-md">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-xl font-bold">Call History</h2>
        </div>
        <div class="p-6">
            <div id="call-history">
                <p class="text-gray-400 animate-pulse text-center py-8">Loading call history...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadDailyStats() {
        try {
            const response = await fetch('/api/reports/daily-stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const stats = await response.json();
            
            document.getElementById('daily-stats').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    ${stats.map(stat => `
                    <div class="bg-gray-50 p-4 rounded">
                        <p class="text-sm text-gray-600">${stat.date}</p>
                        <p class="text-2xl font-bold text-gray-900">${stat.total_calls}</p>
                        <p class="text-xs text-gray-500">Total Calls</p>
                    </div>
                    `).join('')}
                </div>
            `;
        } catch (error) {
            console.error('Error loading daily stats:', error);
        }
    }

    async function loadCallHistory() {
        try {
            const response = await fetch('/api/reports/call-history', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            const calls = await response.json();
            
            document.getElementById('call-history').innerHTML = `
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date/Time</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Caller ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${calls.map(call => `
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${new Date(call.start_time).toLocaleString()}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${call.caller_id}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.destination}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.agent?.user?.name || 'N/A'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.duration || 0}s</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                    call.status === 'answered' ? 'bg-green-100 text-green-800' :
                                    call.status === 'missed' ? 'bg-red-100 text-red-800' :
                                    'bg-gray-100 text-gray-800'
                                }">
                                    ${call.status}
                                </span>
                            </td>
                        </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        } catch (error) {
            console.error('Error loading call history:', error);
        }
    }

    loadDailyStats();
    loadCallHistory();
</script>
@endpush

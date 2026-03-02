@extends('layouts.app')

@section('page-title', 'Active Calls')
@section('page-description', 'Monitor and manage active calls in real-time')

@section('content')
<div class="bg-white rounded-lg shadow-md">
    <div class="p-6 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-bold">Active Calls List</h2>
        <button onclick="refreshCalls()" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
            <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            Refresh
        </button>
    </div>
    <div class="p-6">
        <div id="calls-table" class="overflow-x-auto">
            <p class="text-gray-400 animate-pulse text-center py-8">Loading calls...</p>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function refreshCalls() {
        try {
            const response = await fetch('/api/dashboard/active-calls', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch calls');
            
            const calls = await response.json();
            
            const table = document.getElementById('calls-table');
            if (calls.length === 0) {
                table.innerHTML = '<p class="text-gray-500 text-center py-8">No active calls</p>';
                return;
            }

            table.innerHTML = `
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Caller ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Destination</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Queue</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        ${calls.map(call => {
                            const duration = call.answer_time ? Math.floor((new Date() - new Date(call.answer_time)) / 1000) : 0;
                            const minutes = Math.floor(duration / 60);
                            const seconds = duration % 60;
                            
                            return `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${call.caller_id}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.destination}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.agent?.user?.name || 'Unassigned'}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${call.queue?.name || 'N/A'}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                        call.status === 'answered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                    }">
                                        ${call.status}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${minutes}:${seconds.toString().padStart(2, '0')}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                    ${call.status === 'answered' ? `
                                    <button onclick="hangupCall('${call.unique_id}')" class="text-red-600 hover:text-red-900">Hangup</button>
                                    <button onclick="transferCall('${call.unique_id}')" class="text-blue-600 hover:text-blue-900">Transfer</button>
                                    ` : ''}
                                </td>
                            </tr>
                            `;
                        }).join('')}
                    </tbody>
                </table>
            `;
        } catch (error) {
            console.error('Error fetching calls:', error);
            document.getElementById('calls-table').innerHTML = '<p class="text-red-500 text-center py-4">Error loading calls</p>';
        }
    }

    async function hangupCall(uniqueId) {
        if (!confirm('Are you sure you want to hangup this call?')) return;
        
        try {
            const response = await fetch('/api/calls/hangup', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin',
                body: JSON.stringify({ unique_id: uniqueId })
            });
            
            if (response.ok) {
                refreshCalls();
            } else {
                alert('Failed to hangup call');
            }
        } catch (error) {
            console.error('Error hanging up call:', error);
            alert('Error hanging up call');
        }
    }

    function transferCall(uniqueId) {
        const extension = prompt('Enter extension to transfer to:');
        if (!extension) return;
        
        // Transfer call logic here
        alert('Transfer functionality will be implemented');
    }

    // Initial load
    refreshCalls();

    // Auto-refresh every 3 seconds
    setInterval(refreshCalls, 3000);
</script>
@endpush

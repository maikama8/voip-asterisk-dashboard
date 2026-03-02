@extends('layouts.app')

@section('title', 'Call History')
@section('page-title', 'Call History')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6 mb-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-lg font-semibold">Filter Calls</h3>
        <button onclick="loadCalls()" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            Refresh
        </button>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date From</label>
            <input type="date" id="date-from" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Date To</label>
            <input type="date" id="date-to" class="w-full border border-gray-300 rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select id="status-filter" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">All</option>
                <option value="answered">Answered</option>
                <option value="ringing">Ringing</option>
                <option value="missed">Missed</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Agent</label>
            <select id="agent-filter" class="w-full border border-gray-300 rounded px-3 py-2">
                <option value="">All Agents</option>
            </select>
        </div>
    </div>
</div>

<div class="bg-white rounded-lg shadow-md p-6">
    <h3 class="text-lg font-semibold mb-4">Call Records</h3>
    <div class="overflow-x-auto">
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
            <tbody id="calls-table" class="bg-white divide-y divide-gray-200">
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-400">Loading...</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadCalls() {
        try {
            const response = await fetch('/api/reports/call-history', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch');
            
            const calls = await response.json();
            
            document.getElementById('calls-table').innerHTML = calls.length > 0 ? calls.map(call => {
                const duration = call.duration ? `${Math.floor(call.duration / 60)}:${String(call.duration % 60).padStart(2, '0')}` : '-';
                const startTime = new Date(call.start_time).toLocaleString();
                
                return `
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${startTime}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${call.caller_id}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${call.destination}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${call.agent?.user?.name || 'Unassigned'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${duration}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ${
                                call.status === 'answered' ? 'bg-green-100 text-green-800' :
                                call.status === 'ringing' ? 'bg-yellow-100 text-yellow-800' :
                                'bg-red-100 text-red-800'
                            }">
                                ${call.status}
                            </span>
                        </td>
                    </tr>
                `;
            }).join('') : '<tr><td colspan="6" class="px-6 py-4 text-center text-gray-500">No calls found</td></tr>';
        } catch (error) {
            console.error('Error loading calls:', error);
            document.getElementById('calls-table').innerHTML = '<tr><td colspan="6" class="px-6 py-4 text-center text-red-500">Error loading calls</td></tr>';
        }
    }

    async function loadAgents() {
        try {
            const response = await fetch('/api/dashboard/agents', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch');
            
            const agents = await response.json();
            const select = document.getElementById('agent-filter');
            
            agents.forEach(agent => {
                const option = document.createElement('option');
                option.value = agent.id;
                option.textContent = agent.user.name;
                select.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading agents:', error);
        }
    }

    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    const weekAgo = new Date(Date.now() - 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
    document.getElementById('date-from').value = weekAgo;
    document.getElementById('date-to').value = today;

    loadCalls();
    loadAgents();
</script>
@endpush

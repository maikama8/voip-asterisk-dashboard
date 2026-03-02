@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500 text-sm font-medium">Active Calls</h3>
        <p class="text-3xl font-bold text-blue-600 mt-2" id="active-calls">
            <span class="inline-block animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500 text-sm font-medium">Waiting Calls</h3>
        <p class="text-3xl font-bold text-yellow-600 mt-2" id="waiting-calls">
            <span class="inline-block animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500 text-sm font-medium">Online Agents</h3>
        <p class="text-3xl font-bold text-green-600 mt-2" id="online-agents">
            <span class="inline-block animate-pulse">...</span>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="text-gray-500 text-sm font-medium">Total Today</h3>
        <p class="text-3xl font-bold text-purple-600 mt-2" id="total-calls">
            <span class="inline-block animate-pulse">...</span>
        </p>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-8">
    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M2 3a1 1 0 011-1h2.153a1 1 0 01.986.836l.74 4.435a1 1 0 01-.54 1.06l-1.548.773a11.037 11.037 0 006.105 6.105l.774-1.548a1 1 0 011.059-.54l4.435.74a1 1 0 01.836.986V17a1 1 0 01-1 1h-2C7.82 18 2 12.18 2 5V3z"/>
            </svg>
            Active Calls
        </h2>
        <div id="calls-list" class="space-y-2 min-h-[100px]">
            <p class="text-gray-400 animate-pulse">Loading calls...</p>
        </div>
    </div>

    <div class="bg-white p-6 rounded-lg shadow-md">
        <h2 class="text-xl font-bold mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z"/>
            </svg>
            Agents Status
        </h2>
        <div id="agents-list" class="space-y-2 min-h-[100px]">
            <p class="text-gray-400 animate-pulse">Loading agents...</p>
        </div>
    </div>
</div>

<div class="mt-4 text-center text-sm text-gray-500">
    <span id="last-update">Last updated: <span class="font-medium">Never</span></span>
    <span class="mx-2">•</span>
    <span>Auto-refresh: <span class="font-medium text-green-600">Every 5s</span></span>
</div>
@endsection

@push('scripts')
<script>
    let lastUpdateTime = null;

    function updateLastUpdateTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString();
        document.getElementById('last-update').innerHTML = `Last updated: <span class="font-medium">${timeStr}</span>`;
    }

    async function fetchStats() {
        try {
            const response = await fetch('/api/dashboard/stats', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch stats');
            
            const data = await response.json();
            
            document.getElementById('active-calls').textContent = data.active_calls;
            document.getElementById('waiting-calls').textContent = data.waiting_calls;
            document.getElementById('online-agents').textContent = data.online_agents;
            document.getElementById('total-calls').textContent = data.total_calls_today;
            
            updateLastUpdateTime();
        } catch (error) {
            console.error('Error fetching stats:', error);
        }
    }

    async function fetchActiveCalls() {
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
            
            const callsList = document.getElementById('calls-list');
            callsList.innerHTML = calls.length > 0 ? calls.map(call => `
                <div class="border-l-4 ${call.status === 'answered' ? 'border-green-500' : 'border-yellow-500'} pl-4 py-3 bg-gray-50 rounded">
                    <p class="font-semibold text-gray-800">${call.caller_id} → ${call.destination}</p>
                    <p class="text-sm text-gray-600 mt-1">
                        <span class="inline-block px-2 py-0.5 rounded text-xs ${call.status === 'answered' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'}">${call.status}</span>
                        <span class="ml-2">${call.agent?.user?.name || 'Unassigned'}</span>
                    </p>
                </div>
            `).join('') : '<p class="text-gray-500 text-center py-8">No active calls</p>';
        } catch (error) {
            console.error('Error fetching calls:', error);
        }
    }

    async function fetchAgents() {
        try {
            const response = await fetch('/api/dashboard/agents', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch agents');
            
            const agents = await response.json();
            
            const agentsList = document.getElementById('agents-list');
            agentsList.innerHTML = agents.length > 0 ? agents.map(agent => `
                <div class="flex justify-between items-center border-b pb-3 hover:bg-gray-50 px-2 py-2 rounded transition">
                    <div>
                        <span class="font-medium text-gray-800">${agent.user.name}</span>
                        <span class="text-gray-500 text-sm ml-2">(Ext: ${agent.extension})</span>
                    </div>
                    <span class="px-3 py-1 rounded-full text-sm font-medium ${
                        agent.status === 'online' ? 'bg-green-100 text-green-800' :
                        agent.status === 'busy' ? 'bg-yellow-100 text-yellow-800' :
                        'bg-gray-100 text-gray-800'
                    }">${agent.status}</span>
                </div>
            `).join('') : '<p class="text-gray-500 text-center py-8">No agents</p>';
        } catch (error) {
            console.error('Error fetching agents:', error);
        }
    }

    // Initial fetch
    fetchStats();
    fetchActiveCalls();
    fetchAgents();

    // Auto-refresh every 5 seconds
    setInterval(() => {
        fetchStats();
        fetchActiveCalls();
        fetchAgents();
    }, 5000);
</script>
@endpush

@extends('layouts.app')

@section('title', 'Agents')
@section('page-title', 'Agent Management')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">All Agents</h3>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Add Agent
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="agents-grid">
        <div class="text-center text-gray-400 py-8">Loading agents...</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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
            
            document.getElementById('agents-grid').innerHTML = agents.map(agent => `
                <div class="border rounded-lg p-6 hover:shadow-lg transition">
                    <div class="flex items-center justify-between mb-4">
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                            <span class="text-2xl font-bold text-blue-600">${agent.user.name.charAt(0)}</span>
                        </div>
                        <span class="px-3 py-1 rounded-full text-sm font-medium ${
                            agent.status === 'online' ? 'bg-green-100 text-green-800' :
                            agent.status === 'busy' ? 'bg-yellow-100 text-yellow-800' :
                            'bg-gray-100 text-gray-800'
                        }">
                            ${agent.status}
                        </span>
                    </div>
                    <h4 class="text-lg font-semibold mb-1">${agent.user.name}</h4>
                    <p class="text-sm text-gray-600 mb-2">${agent.user.email}</p>
                    <div class="space-y-1 text-sm">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Extension:</span>
                            <span class="font-medium">${agent.extension}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">SIP Peer:</span>
                            <span class="font-medium">${agent.sip_peer}</span>
                        </div>
                    </div>
                    <div class="mt-4 flex space-x-2">
                        <button class="flex-1 bg-blue-50 text-blue-600 px-3 py-2 rounded text-sm hover:bg-blue-100 transition">
                            Edit
                        </button>
                        <button class="flex-1 bg-red-50 text-red-600 px-3 py-2 rounded text-sm hover:bg-red-100 transition">
                            Delete
                        </button>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading agents:', error);
        }
    }

    loadAgents();
</script>
@endpush

@extends('layouts.app')

@section('title', 'Queues')
@section('page-title', 'Queue Management')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-lg font-semibold">Call Queues</h3>
        <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition">
            + Add Queue
        </button>
    </div>

    <div class="space-y-4" id="queues-list">
        <div class="text-center text-gray-400 py-8">Loading queues...</div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    async function loadQueues() {
        try {
            const response = await fetch('/api/dashboard/queues', {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                credentials: 'same-origin'
            });
            
            if (!response.ok) throw new Error('Failed to fetch');
            
            const queues = await response.json();
            
            document.getElementById('queues-list').innerHTML = queues.map(queue => `
                <div class="border rounded-lg p-6 hover:shadow-md transition">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h4 class="text-lg font-semibold">${queue.name}</h4>
                            <p class="text-sm text-gray-600">Asterisk Queue: ${queue.asterisk_queue_name}</p>
                        </div>
                        <div class="flex space-x-2">
                            <button class="bg-blue-50 text-blue-600 px-3 py-1 rounded text-sm hover:bg-blue-100 transition">
                                Edit
                            </button>
                            <button class="bg-red-50 text-red-600 px-3 py-1 rounded text-sm hover:bg-red-100 transition">
                                Delete
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-3 gap-4">
                        <div class="bg-blue-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Current Calls</p>
                            <p class="text-2xl font-bold text-blue-600">${queue.current_calls || 0}</p>
                        </div>
                        <div class="bg-yellow-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Waiting Calls</p>
                            <p class="text-2xl font-bold text-yellow-600">${queue.waiting_calls || 0}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded">
                            <p class="text-sm text-gray-600">Max Wait Time</p>
                            <p class="text-2xl font-bold text-gray-600">${queue.max_wait_time}s</p>
                        </div>
                    </div>
                </div>
            `).join('');
        } catch (error) {
            console.error('Error loading queues:', error);
        }
    }

    loadQueues();
    setInterval(loadQueues, 10000); // Refresh every 10 seconds
</script>
@endpush

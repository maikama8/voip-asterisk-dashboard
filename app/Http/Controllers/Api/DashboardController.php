<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use App\Models\Agent;
use App\Models\Queue;
use App\Services\AsteriskAMIService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function __construct(private AsteriskAMIService $ami) {}

    public function stats(): JsonResponse
    {
        return response()->json([
            'active_calls' => Call::where('status', 'answered')->count(),
            'waiting_calls' => Call::where('status', 'ringing')->count(),
            'online_agents' => Agent::where('status', 'online')->count(),
            'total_calls_today' => Call::whereDate('created_at', today())->count(),
        ]);
    }

    public function activeCalls(): JsonResponse
    {
        $calls = Call::with(['agent.user', 'queue'])
            ->whereIn('status', ['ringing', 'answered'])
            ->latest()
            ->get();

        return response()->json($calls);
    }

    public function agents(): JsonResponse
    {
        $agents = Agent::with('user')->get();
        return response()->json($agents);
    }

    public function queues(): JsonResponse
    {
        $queues = Queue::all();
        return response()->json($queues);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Call;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function callHistory(Request $request): JsonResponse
    {
        $query = Call::with(['agent.user', 'queue']);

        if ($request->has('date_from')) {
            $query->where('start_time', '>=', $request->date_from);
        }

        if ($request->has('date_to')) {
            $query->where('start_time', '<=', $request->date_to);
        }

        if ($request->has('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        $calls = $query->latest('start_time')->paginate(50);

        return response()->json($calls);
    }

    public function dailyStats(): JsonResponse
    {
        $today = Call::whereDate('created_at', today());
        
        $stats = [
            'total_calls' => $today->count(),
            'answered_calls' => $today->where('status', 'answered')->count(),
            'missed_calls' => $today->where('status', 'missed')->count(),
            'avg_duration' => $today->whereNotNull('duration')->avg('duration') ?? 0,
        ];
        
        // Format average duration as MM:SS
        $avgSeconds = (int) $stats['avg_duration'];
        $stats['avg_duration'] = sprintf('%d:%02d', floor($avgSeconds / 60), $avgSeconds % 60);

        return response()->json($stats);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AsteriskAMIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CallController extends Controller
{
    public function __construct(private AsteriskAMIService $ami) {}

    public function hangup(Request $request): JsonResponse
    {
        $request->validate(['channel' => 'required|string']);

        if ($this->ami->connect()) {
            $success = $this->ami->hangup($request->channel);
            return response()->json(['success' => $success]);
        }

        return response()->json(['success' => false, 'message' => 'AMI connection failed'], 500);
    }

    public function transfer(Request $request): JsonResponse
    {
        $request->validate([
            'channel' => 'required|string',
            'extension' => 'required|string',
        ]);

        if ($this->ami->connect()) {
            $success = $this->ami->redirect($request->channel, $request->extension);
            return response()->json(['success' => $success]);
        }

        return response()->json(['success' => false, 'message' => 'AMI connection failed'], 500);
    }
}

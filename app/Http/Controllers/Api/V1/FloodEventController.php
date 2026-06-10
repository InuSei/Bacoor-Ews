<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FloodEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FloodEventController extends Controller
{
    /**
     * 📡 HARDWARE INGESTION: ESP32 posts data here.
     */
    public function store(Request $request): JsonResponse
    {
        // 1. Validate the incoming ESP32 payload
        $validated = $request->validate([
            'location'      => ['required', 'string', 'max:100'],
            'warning_level' => ['required', 'string', 'in:CRITICAL,MODERATE,LOW,SAFE'],
        ]);

        // 2. Persist directly to the database
        $event = FloodEvent::create([
            'location'      => $validated['location'],
            'warning_level' => $validated['warning_level'],
            'alert_sent'    => false
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Flood status event recorded successfully.',
            'data'    => $event
        ], 201);
    }

    /**
     * 🗺️ MAP STREAM: Your JavaScript polls this.
     */
    public function latest(): JsonResponse
    {
        $latestEvent = FloodEvent::latest('id')->first();

        if (!$latestEvent) {
            return response()->json([
                'status'        => 'empty',
                'warning_level' => 'SAFE',
            ], 200);
        }

        return response()->json([
            'status'        => 'success',
            'location'      => $latestEvent->location,
            'warning_level' => $latestEvent->warning_level,
            'updated_at'    => $latestEvent->created_at ? $latestEvent->created_at->toIso8601String() : null
        ], 200);
    }
}
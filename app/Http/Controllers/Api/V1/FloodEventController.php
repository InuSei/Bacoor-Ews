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
        $request->merge([
            'water_level' => $request->has('water_level') ? strtoupper(trim((string)$request->input('water_level'))) : null,
            'location'    => $request->has('location') ? trim((string)$request->input('location')) : null
        ]);

        // 1. Validate the incoming ESP32 payload
        $validated = $request->validate([
            'location'    => ['required', 'string', 'max:100'],
            'water_level' => ['required', 'string', 'in:CRITICAL,MODERATE,LOW,SAFE'],
        ]);

        // 2. Persist directly to the database
        $event = FloodEvent::create([
            'location'    => $validated['location'],
            'water_level' => $validated['water_level'],
            'alert_sent'  => false
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
                'status'      => 'empty',
                'water_level' => 'SAFE',
            ], 200);
        }

        return response()->json([
            'status'      => 'success',
            'location'    => $latestEvent->location,
            'water_level' => $latestEvent->water_level, // 🌟 FIXED: Changed from warning_level to water_level column
            'updated_at'  => $latestEvent->created_at ? $latestEvent->created_at->toIso8601String() : null
        ], 200);
    }

    public function history(string $location): JsonResponse
    {
        $events = FloodEvent::where('location', $location)
                    ->latest('id')
                    ->limit(10)
                    ->get();

        return response()->json([
            'status' => 'success',
            'data'   => $events
        ], 200);
    }
}
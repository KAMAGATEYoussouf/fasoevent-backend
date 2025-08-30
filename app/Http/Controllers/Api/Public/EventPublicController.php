<?php

namespace App\Http\Controllers\Api\Public;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class EventPublicController extends Controller
{
    /**
     * Display a listing of active events in descending order of creation.
     */
    public function index()
    {
        $events = Event::with('city')
            ->where('is_active', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'message' => 'Événements actifs récupérés avec succès.',
            'data' => $events,
            'status' => 'success'
        ], 200);
    }

    /**
     * Display the specified event by ID.
     */
    public function show(string $id)
    {
        $event = Event::with('city')->find($id);

        if (!$event) {
            throw ValidationException::withMessages([
                'id' => ['Événement non trouvé.'],
            ]);
        }

        return response()->json([
            'message' => 'Événement récupéré avec succès.',
            'data' => $event,
            'status' => 'success'
        ], 200);
    }
}

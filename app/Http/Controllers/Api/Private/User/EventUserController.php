<?php

namespace App\Http\Controllers\Api\Private\User;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\AuthenticationException;

class EventUserController extends Controller
{

    /**
     * Liste des événements réservés par l'utilisateur connecté
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            throw new AuthenticationException('Non authentifié.');
        }

        $events = $user->events()->with('city')->get();

        return response()->json([
            'message' => 'Événements réservés récupérés avec succès.',
            'data'    => $events,
            'status'  => 'success'
        ], 200);
    }

    /**
     * Réserver un événement
     */
    public function reserve(Request $request, string $eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            throw ValidationException::withMessages([
                'event_id' => ['Événement non trouvé.'],
            ]);
        }

        $user = Auth::user();
        if (!$user) {
            throw new AuthenticationException('Non authentifié.');
        }

        if ($user->events()->where('event_id', $eventId)->exists()) {
            throw ValidationException::withMessages([
                'event_id' => ['Vous êtes déjà inscrit à cet événement.'],
            ]);
        }

        $user->events()->attach($eventId);

        return response()->json([
            'message' => 'Réservation effectuée avec succès.',
            'data'    => $event->load('city'),
            'status'  => 'success'
        ], 200);
    }

    /**
     * Annuler une réservation
     */
    public function cancel(Request $request, string $eventId)
    {
        $event = Event::find($eventId);

        if (!$event) {
            throw ValidationException::withMessages([
                'event_id' => ['Événement non trouvé.'],
            ]);
        }

        $user = Auth::user();
        if (!$user) {
            throw new AuthenticationException('Non authentifié.');
        }

        if (!$user->events()->where('event_id', $eventId)->exists()) {
            throw ValidationException::withMessages([
                'event_id' => ['Vous n\'êtes pas inscrit à cet événement.'],
            ]);
        }

        $user->events()->detach($eventId);

        return response()->json([
            'message' => 'Réservation annulée avec succès.',
            'status'  => 'success'
        ], 200);
    }
}

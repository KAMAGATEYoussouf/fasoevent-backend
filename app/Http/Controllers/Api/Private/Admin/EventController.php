<?php

namespace App\Http\Controllers\Api\Private\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class EventController extends Controller
{

    /**
     * Store base64 image and return the path.
     */
    private function storeBase64Image(string $base64Data): string
    {
        // Vérifier si c'est une image base64 valide
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Data, $type)) {
            $data = substr($base64Data, strpos($base64Data, ',') + 1);
            $type = strtolower($type[1]); // jpg, png, gif

            if (!in_array($type, ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
                throw new \Exception('Type d\'image non supporté');
            }

            $data = base64_decode($data);

            if ($data === false) {
                throw new \Exception('Données base64 invalides');
            }
        } else {
            throw new \Exception('Format base64 invalide');
        }

        // Générer un nom de fichier unique
        $filename = 'events/' . Str::uuid() . '.' . $type;

        // Stocker l'image
        Storage::put($filename, $data);

        return $filename;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|string', // base64
            'is_active' => 'boolean',
            'city_id' => 'required|uuid|exists:cities,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $validated = $validator->validated();

            // Gestion de l'image base64
            if (!empty($validated['image'])) {
                $imageData = $validated['image'];
                $imagePath = $this->storeBase64Image($imageData);
                $validated['image'] = $imagePath;
            }

            $event = Event::create($validated);

            // Charger la relation city
            $event->load('city');

            return response()->json([
                'success' => true,
                'message' => 'Événement créé avec succès',
                'data' => $event
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la création de l\'événement',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Les autres méthodes (index, show, update, destroy, toggleActive) restent inchangées
    public function index()
    {
        $events = Event::with('city')->get();
        return response()->json([
            'message' => 'Événements récupérés avec succès.',
            'data' => $events,
            'status' => 'success'
        ], 200);
    }

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

    public function update(Request $request, string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            throw ValidationException::withMessages([
                'id' => ['Événement non trouvé.'],
            ]);
        }

        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'price' => 'nullable|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'city_id' => 'sometimes|required|uuid|exists:cities,id',
        ], [
            'title.required' => 'Le titre de l\'événement est obligatoire.',
            'end_date.after_or_equal' => 'La date de fin doit être postérieure ou égale à la date de début.',
            'city_id.required' => 'L\'ID de la ville est obligatoire.',
            'city_id.exists' => 'La ville spécifiée n\'existe pas.',
            'image.image' => 'Le fichier doit être une image.',
            'image.mimes' => 'L\'image doit être de type jpeg, png, jpg ou gif.',
            'image.max' => 'L\'image ne doit pas dépasser 2 Mo.',
        ]);

        // Gestion de l'image pour la mise à jour
        if ($request->hasFile('image') && $request->file('image')->isValid()) {
            // Supprimer l'ancienne image si elle existe
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $validatedData['image'] = $request->file('image')->store('events', 'public');
        }

        $event->update($validatedData);

        return response()->json([
            'message' => 'Événement mis à jour avec succès.',
            'data' => $event->load('city'),
            'status' => 'success'
        ], 200);
    }

    public function destroy(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            throw ValidationException::withMessages([
                'id' => ['Événement non trouvé.'],
            ]);
        }

        // Supprimer l'image associée si elle existe
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return response()->json([
            'message' => 'Événement supprimé avec succès.',
            'status' => 'success'
        ], 200);
    }

    public function toggleActive(string $id)
    {
        $event = Event::find($id);

        if (!$event) {
            throw ValidationException::withMessages([
                'id' => ['Événement non trouvé.'],
            ]);
        }

        $event->is_active = !$event->is_active;
        $event->save();

        return response()->json([
            'message' => 'Statut de l\'événement mis à jour avec succès.',
            'data' => $event->load('city'),
            'status' => 'success'
        ], 200);
    }
}

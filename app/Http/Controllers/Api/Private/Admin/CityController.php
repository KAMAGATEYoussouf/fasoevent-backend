<?php

namespace App\Http\Controllers\Api\Private\Admin;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        return response()->json([
            'message' => 'Villes récupérées avec succès.',
            'data' => $cities,
            'status' => 'success'
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom de la ville est obligatoire.',
            'name.string' => 'Le nom de la ville doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la ville ne doit pas dépasser 255 caractères.',
        ]);

        $city = City::create([
            'id' => Str::uuid(),
            'name' => $validatedData['name'],
        ]);

        return response()->json([
            'message' => 'Ville créée avec succès.',
            'data' => $city,
            'status' => 'success'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $city = City::find($id);

        if (!$city) {
            throw ValidationException::withMessages([
                'id' => ['Ville non trouvée.'],
            ]);
        }

        return response()->json([
            'message' => 'Ville récupérée avec succès.',
            'data' => $city,
            'status' => 'success'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $city = City::find($id);

        if (!$city) {
            throw ValidationException::withMessages([
                'id' => ['Ville non trouvée.'],
            ]);
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
        ], [
            'name.required' => 'Le nom de la ville est obligatoire.',
            'name.string' => 'Le nom de la ville doit être une chaîne de caractères.',
            'name.max' => 'Le nom de la ville ne doit pas dépasser 255 caractères.',
        ]);

        $city->update([
            'name' => $validatedData['name'],
        ]);

        return response()->json([
            'message' => 'Ville mise à jour avec succès.',
            'data' => $city,
            'status' => 'success'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $city = City::find($id);

        if (!$city) {
            throw ValidationException::withMessages([
                'id' => ['Ville non trouvée.'],
            ]);
        }

        $city->delete();

        return response()->json([
            'message' => 'Ville supprimée avec succès.',
            'status' => 'success'
        ], 200);
    }
}

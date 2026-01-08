<?php

namespace App\Http\Controllers\Api;

use App\Models\CustomPokemon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;

class CustomPokemonController extends Controller
{
    public function index()
    {
        return response()->json([
            'custom_pokemons' => CustomPokemon::all()
        ]);
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'types' => 'nullable|array',
            'abilities' => 'nullable|array',
            'sprite' => 'nullable|string|url',
        ]);
        
        $name = strtolower($request->name);
        
        if (CustomPokemon::exists($name)) {
            return response()->json([
                'error' => 'Custom pokemon with this name already exists'
            ], 409);
        }
        
        try {
            $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$name}");
            if ($response->successful()) {
                return response()->json([
                    'error' => 'Pokemon already exists in PokeAPI'
                ], 409);
            }
        } catch (\Exception $e) {
        }
        
        $pokemon = CustomPokemon::create([
            'name' => $name,
            'height' => $request->height,
            'weight' => $request->weight,
            'types' => $request->types,
            'abilities' => $request->abilities,
            'sprite' => $request->sprite,
        ]);
        
        return response()->json([
            'message' => 'Custom pokemon created successfully',
            'data' => $pokemon
        ], 201);
    }

    public function show(string $name)
    {
        $pokemon = CustomPokemon::where('name', strtolower($name))->first();
        
        if (!$pokemon) {
            return response()->json([
                'error' => 'Custom pokemon not found'
            ], 404);
        }
        
        return response()->json($pokemon);
    }
    
    public function update(Request $request, string $name)
    {
        $pokemon = CustomPokemon::where('name', strtolower($name))->first();
        
        if (!$pokemon) {
            return response()->json([
                'error' => 'Custom pokemon not found'
            ], 404);
        }
        
        $request->validate([
            'height' => 'nullable|integer',
            'weight' => 'nullable|integer',
            'types' => 'nullable|array',
            'abilities' => 'nullable|array',
            'sprite' => 'nullable|string|url',
        ]);
        
        $pokemon->update($request->only([
            'height', 'weight', 'types', 'abilities', 'sprite'
        ]));
        
        return response()->json([
            'message' => 'Custom pokemon updated successfully',
            'data' => $pokemon
        ]);
    }
    
    public function destroy(string $name)
    {
        $pokemon = CustomPokemon::where('name', strtolower($name))->first();
        
        if (!$pokemon) {
            return response()->json([
                'error' => 'Custom pokemon not found'
            ], 404);
        }
        
        $pokemon->delete();
        
        return response()->json([
            'message' => 'Custom pokemon deleted successfully'
        ]);
    }
}
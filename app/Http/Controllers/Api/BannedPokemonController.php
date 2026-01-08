<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BannedPokemon;

class BannedPokemonController extends Controller
{
    public function index() {
        return response()->json([
            'banned_pokemons' => BannedPokemon::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);
        
        $name = strtolower($request->name);
        
        if (BannedPokemon::isBanned($name)) {
            return response()->json([
                'error' => 'Pokemon already banned'
            ], 409);
        }
        
        $banned = BannedPokemon::create(['name' => $name]);
        
        return response()->json([
            'message' => 'Pokemon banned successfully',
            'data' => $banned
        ], 201);
    }
    
    public function destroy(string $name)
    {
        $name = strtolower($name);
        
        $banned = BannedPokemon::where('name', $name)->first();
        
        if (!$banned) {
            return response()->json([
                'error' => 'Pokemon not found in banned list'
            ], 404);
        }
        
        $banned->delete();
        
        return response()->json([
            'message' => 'Pokemon unbanned successfully'
        ], 200);
    }
}

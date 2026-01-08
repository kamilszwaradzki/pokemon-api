<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InfoController extends Controller
{
    /**
     * Action return information about selected pokemon
     * example request: GET /api/info?pokemons=pikachu,charizard
     */
    public function show(Request $request) {
        $allowed_pkmns = [];
        foreach(explode(',', $request->get('pokemons')) as $name) {
            $pokemon = Http::get("https://pokeapi.co/api/v2/pokemon/{$name}")->throw()->json();
            Cache::remember("pokemon_{$name}", now()->addDay(), function() use ($pokemon) { return $pokemon; });
            $allowed_pkmns[$name] = $pokemon;
        }
        return response($allowed_pkmns, 200)
        ->header('Content-Type', 'application/json');
    }
}

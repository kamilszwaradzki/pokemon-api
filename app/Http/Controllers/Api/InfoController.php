<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BannedPokemon;
use App\Models\CustomPokemon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class InfoController extends Controller
{
    public function index(Request $request)
    {
        $pokemons = explode(',', $request->input('pokemons', ''));
        $bannedNames = BannedPokemon::getBannedNames();
        
        $results = [];
        
        foreach ($pokemons as $name) {
            $name = strtolower(trim($name));
            
            if (in_array($name, $bannedNames)) {
                continue;
            }
            
            $customPokemon = CustomPokemon::where('name', $name)->first();
            if ($customPokemon) {
                $c_pokemon = $customPokemon->toApiFormat();
                $results[] = $c_pokemon;
                Cache::remember("pokemon_{$name}", now()->addDay(), function() use ($c_pokemon) { return $c_pokemon; });
                continue;
            }
            
            try {
                $response = Http::get("https://pokeapi.co/api/v2/pokemon/{$name}");
                if ($response->successful()) {
                    $data = $response->json();
                    $pokemon = [
                        'name' => $data['name'],
                        'height' => $data['height'],
                        'weight' => $data['weight'],
                        'types' => array_map(fn($t) => $t['type']['name'], $data['types']),
                        'abilities' => array_map(fn($a) => $a['ability']['name'], $data['abilities']),
                        'sprite' => $data['sprites']['front_default'] ?? null,
                        'is_custom' => false,
                    ];
                    $results[] = $pokemon;
                    Cache::remember("pokemon_{$name}", now()->addDay(), function() use ($pokemon) { return $pokemon; });
                }
            } catch (\Exception $e) {
            }
        }
        
        return response()->json([
            'pokemons' => $results
        ]);
    }
}

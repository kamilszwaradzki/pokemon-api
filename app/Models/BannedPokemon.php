<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BannedPokemon extends Model
{
    protected $fillable = ['name'];
    
    public static function isBanned(string $name): bool
    {
        return self::where('name', strtolower($name))->exists();
    }
    
    public static function getBannedNames(): array
    {
        return self::pluck('name')->toArray();
    }
}
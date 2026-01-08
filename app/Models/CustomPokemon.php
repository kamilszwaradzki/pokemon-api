<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomPokemon extends Model
{
    protected $fillable = [
        'name',
        'height',
        'weight',
        'types',
        'abilities',
        'sprite'
    ];

    protected $casts = [
        'types' => 'array',
        'abilities' => 'array',
    ];
    
    public static function exists(string $name): bool
    {
        return self::where('name', strtolower($name))->exists();
    }
    
    public function toApiFormat(): array
    {
        return [
            'name' => $this->name,
            'height' => $this->height,
            'weight' => $this->weight,
            'types' => $this->types,
            'abilities' => $this->abilities,
            'sprite' => $this->sprite,
            'is_custom' => true,
        ];
    }
}
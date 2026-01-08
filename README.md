# Pokemon API

Serwis do zarządzania informacjami o Pokemonach, integrujący się z PokeAPI i umożliwiający dodawanie własnych, niestandardowych stworzeń.

## Wymagania

- PHP 8.1+
- Composer
- SQLite
- Laravel 11.x

## Instalacja i uruchomienie

Sklonuj repo i zainstaluj zależności:

```bash
git clone https://github.com/twoj-username/pokemon-api.git
cd pokemon-api
composer install
```

Przygotuj środowisko:

```bash
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
```

W pliku `.env` ustaw:

```env
DB_CONNECTION=sqlite
SUPER_SECRET_KEY=moj-sekretny-klucz
```

Wykonaj migracje i uruchom serwer:

```bash
php artisan migrate
php artisan serve
```

Aplikacja powinna być dostępna pod `http://localhost:8000`

## API Documentation

Wszystkie endpointy z prefiksem `/api`. Chronione ścieżki wymagają nagłówka `X-SUPER-SECRET-KEY`.

### Banned Pokemons

**GET /api/banned** - lista zbanowanych pokemonów

```bash
curl http://localhost:8000/api/banned \
  -H "X-SUPER-SECRET-KEY: twoj-klucz"
```

Response:
```json
{
  "banned_pokemons": [
    {"id": 1, "name": "pikachu", "created_at": "...", "updated_at": "..."}
  ]
}
```

**POST /api/banned** - dodaj do bana

```bash
curl -X POST http://localhost:8000/api/banned \
  -H "X-SUPER-SECRET-KEY: twoj-klucz" \
  -H "Content-Type: application/json" \
  -d '{"name": "pikachu"}'
```

Response 201:
```json
{
  "message": "Pokemon banned successfully",
  "data": {"id": 1, "name": "pikachu", ...}
}
```

Możliwe błędy: `409` jeśli pokemon już zbanowany

**DELETE /api/banned/{name}** - usuń z bana

```bash
curl -X DELETE http://localhost:8000/api/banned/pikachu \
  -H "X-SUPER-SECRET-KEY: twoj-klucz"
```

Response 200:
```json
{"message": "Pokemon unbanned successfully"}
```

Błąd `404` gdy pokemon nie był zbanowany

### Info

**GET /api/info** - pobierz dane o pokemonach

Parametr `pokemons` - lista nazw oddzielona przecinkami

```bash
curl "http://localhost:8000/api/info?pokemons=pikachu,charizard"
```

Response:
```json
{
  "pokemons": [
    {
      "name": "pikachu",
      "height": 4,
      "weight": 60,
      "types": ["electric"],
      "abilities": ["static", "lightning-rod"],
      "sprite": "https://...",
      "is_custom": false
    }
  ]
}
```

Uwagi:
- Zbanowane pokemony są automatycznie filtrowane
- `is_custom: true` oznacza własnego pokemona, `false` - z PokeAPI
- Nieistniejące nazwy są ignorowane

### Custom Pokemons

**GET /api/custom** - lista własnych pokemonów

```bash
curl http://localhost:8000/api/custom \
  -H "X-SUPER-SECRET-KEY: twoj-klucz"
```

**POST /api/custom** - dodaj własnego

```bash
curl -X POST http://localhost:8000/api/custom \
  -H "X-SUPER-SECRET-KEY: twoj-klucz" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "supermon",
    "height": 20,
    "weight": 100,
    "types": ["electric", "dragon"],
    "abilities": ["super-power"]
  }'
```

Pola:
- `name` (wymagane) - unikalna nazwa
- `height`, `weight` (opcjonalne, int)
- `types`, `abilities` (opcjonalne, array)
- `sprite` (opcjonalne, url)

Response 201:
```json
{
  "message": "Custom pokemon created successfully",
  "data": {...}
}
```

Błąd `409` gdy nazwa jest zajęta (lokalnie lub w PokeAPI)

**GET /api/custom/{name}** - pojedynczy pokemon

```bash
curl http://localhost:8000/api/custom/supermon \
  -H "X-SUPER-SECRET-KEY: twoj-klucz"
```

**PUT /api/custom/{name}** - aktualizuj

```bash
curl -X PUT http://localhost:8000/api/custom/supermon \
  -H "X-SUPER-SECRET-KEY: twoj-klucz" \
  -H "Content-Type: application/json" \
  -d '{"height": 25, "weight": 120}'
```

**DELETE /api/custom/{name}** - usuń

```bash
curl -X DELETE http://localhost:8000/api/custom/supermon \
  -H "X-SUPER-SECRET-KEY: twoj-klucz"
```

## Autoryzacja

Chronione endpointy (`/banned`, `/custom`) sprawdzają nagłówek `X-SUPER-SECRET-KEY` względem wartości z `.env`.

Kody błędów:
- `401` - brak nagłówka
- `403` - zły klucz

## Co zrobiono

**Etap 1** - CRUD dla banned pokemonów - zrobione\
**Etap 2** - Middleware autoryzacji - zrobione\
**Etap 3** - Pobieranie info z PokeAPI + filtrowanie - zrobione\
**Etap 4** - CRUD własnych pokemonów + walidacja nazw - zrobione\
**Etap 5** - Cache nie zaimplementowany (brak czasu)

## Struktura

```
app/Http/Controllers/
  - BannedPokemonController.php
  - CustomPokemonController.php
  - PokemonController.php
app/Http/Middleware/
  - CheckSecretKey.php
app/Models/
  - BannedPokemon.php
  - CustomPokemon.php
```

Baza: SQLite, dwie tabele (`banned_pokemons`, `custom_pokemons`). 

Wszystkie nazwy pokemonów przechowywane jako lowercase dla spójności. Custom pokemony sprawdzane przed wywołaniem do PokeAPI.

## Notatki

Banned i custom pokemony to globalne listy, nie przypisane do konkretnych użytkowników. W `/info` endpoint najpierw sprawdzam czy pokemon jest custom, potem odpytuję PokeAPI - dzięki temu własne pokemony mają priorytet.

Brak cache'owania wyników z PokeAPI ze względu na limit czasowy (3h na całe zadanie). W produkcji dodałbym Redis z TTL wyliczanym do najbliższych 12:00 UTC+1.
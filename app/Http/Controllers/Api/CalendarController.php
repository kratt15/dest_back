<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CalendarFormRequest;
use App\Models\Calendar;
use Illuminate\Http\JsonResponse;

class CalendarController extends Controller
{

    // Liste des dates du calendrier
    public function list(): JsonResponse
    {
        $calendars = Calendar::all();

        return response()->json([$calendars]);
    }

    // Ajouter une date au calendrier
    public function store(CalendarFormRequest $request): JsonResponse
    {
        $calendar = Calendar::create($request->all());

        return response()->json(['message' => "Date créée avec succès !", 'date' => $calendar], 201);
    }

    // Mise à jour de date
    public function update(CalendarFormRequest $request, Calendar $calendar): JsonResponse
    {
        $calendar->update($request->all());

        return response()->json(['message' => "Date mise à jour avec succès !", 'date' => $calendar], 200);
    }

    // Suppression de date
    public function delete(Calendar $calendar): JsonResponse
    {
        // Si la date n'existe pas, renvoyer une page not found
        if (!$calendar) {
            return response()->json(['error' => "Date non trouvée !"], 404);
        }

        // Suppression logique
        $calendar->delete();

        return response()->json(['message' => "Date supprimée avec succès !"], 200);
    }
}

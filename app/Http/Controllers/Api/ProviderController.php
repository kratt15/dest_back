<?php

namespace App\Http\Controllers\Api;

use App\Models\Provider;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProviderFormRequest;
use Illuminate\Http\JsonResponse;

class ProviderController extends Controller
{

    // Liste des fournisseur
    public function list(): JsonResponse
    {
        $providers = Provider::orderByDesc('created_at')->get();

        return response()->json($providers);
    }

    // Ajout de fournisseur
    public function store(ProviderFormRequest $request): JsonResponse
    {
        $provider = Provider::create($request->all());

        return response()->json(['message' => 'Fournisseur créé avec succès !', 'provider' => $provider], 201);
    }

    // Mise à jour de fournisseur
    public function update(ProviderFormRequest $request, Provider $provider): JsonResponse
    {
        $provider->update($request->all());

        return response()->json(['message' => 'Fournisseur mis à jour avec succès !', 'provider' => $provider], 200);
    }

    // Suppression de fournisseur
    public function delete(Provider $provider): JsonResponse
    {
        // Si le fournisseur n'existe pas, renvoyer une page not found
        if (!$provider) {
            return response()->json(['error' => 'Fournisseur non trouvé !'], 404);
        }

        // Supprimer logiquement le fournisseur
        $provider->delete();

        return response()->json(['message' => 'Fournisseur supprimé avec succès !'], 200);
    }
    public function search( Request $request){

        $query = $request->input('query');

        $providers = Provider::search($query)->get();

        return response()->json($providers);


     }
}

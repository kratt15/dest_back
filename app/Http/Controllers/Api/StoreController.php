<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreFormRequest;
use App\Models\Calendar;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StoreController extends Controller
{

    // liste des magasins
    public function list(): JsonResponse
    {
        $stores = Store::with('items_stock')->get();

        $store_table = [];

        foreach ($stores as $store) {
            // Id du magasin
            $store_id = $store->id;

            // Nom du magasin
            $store_name = $store->name;

            // Localisation du magain
            $store_location = $store->location;

            // Nombre total d'articles dans le magasin
            $total_items_number = 0;
            $items = $store->items_stock()->get();
            foreach ($items as $item) {
                $quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;
                $total_items_number += $quantity;
            }

            $store_table[] = [
                "id" => $store_id,
                "store_name" => $store_name,
                "store_location" => $store_location,
                "total_items" => $total_items_number,
                "created_at" => $store->created_at,
                "updated_at" => $store->updated_at,
            ];

            usort($store_table, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return response()->json($store_table);
    }

    // Aperçu de liste des magasins
    public function listPreview(): JsonResponse
    {
        $stores = Store::with('items_stock')->get();

        $store_table = [];

        foreach ($stores as $store) {
            // Id du magasin
            $store_id = $store->id;

            // Nom du magasin
            $store_name = $store->name;

            // Localisation du magain
            $store_location = $store->location;

            // Nombre total d'articles dans le magasin
            $total_items_number = 0;
            $items = $store->items_stock()->get();
            foreach ($items as $item) {
                $quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;
                $total_items_number += $quantity;
            }

            $store_table[] = [
                "id" => $store_id,
                "store_name" => $store_name,
                "store_location" => $store_location,
                "total_items" => $total_items_number,
                "created_at" => $store->created_at,
                "updated_at" => $store->updated_at,
            ];
        }

        $store_table = array_slice($store_table, 0, 4);

        return response()->json($store_table);
    }

    // Ajout de magasin
    public function store(StoreFormRequest $request)
    {
        // Récupérer le gérant
        // $manager_name = $request->input('manager_name');
        // $manager = User::where('name', $manager_name)->first();

        // Créer la date dans le calendrier
        // $calendar = Calendar::create();

        // Créer le magasin
        $store = Store::create([
            'name' => $request->input('name'),
            'location' => $request->input('location'),
        ]);

        // Lier le gérant au magasin avec la date d'embauche
        // $store->users_manage()->attach($manager->id, ['calendar_id' => $calendar->id]);

        return response()->json(['message' => "Magasin créé avec succès !", 'store' => $store], 201);
    }

    // Mise à jour de magasin
    public function update(Store $store, StoreFormRequest $request)
    {
        // Modifier le magasin
        $store->update([
            'name' => $request->input('name'),
            'location' => $request->input('location'),
        ]);

        // // Récupérer le gérant
        // $manager_name = $request->input('manager_name');

        // // Vérifier si le gérant a changé
        // if ($request->old('manager_name') !== $manager_name) {

        //     $manager = User::where('name', $manager_name)->first();

        //     // Créer la date dans le calendrier
        //     $calendar = Calendar::create();

        //     // Lier le nouveau gérant au magasin avec la date d'embauche
        //     $store->users_manage()->attach($manager->id, ['calendar_id' => $calendar->id]);
        // }

        return response()->json(['message' => "Magasin modifié avec succès !", 'store' => $store], 200);
    }

    // Suppression de magasin
    public function delete(Store $store)
    {

        // Si le magasin n'existe pas, renvoyer une page not found
        if (!$store) {
            return response()->json(['error' => "Magasin non trouvé !"], 404);
        }

        // Supprimer logiquement le magasin
        $store->delete();

        // Retourner un message de succès de suppression
        return response()->json(['message' => "Magasin supprimé avec succès"], 200);
    }

    public function search(Request $request)
    {

        $query = $request->input('query');

        $stores = Store::search($query)->get();

        return response()->json($stores);
    }
}

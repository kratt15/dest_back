<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchaseFormRequest;
use App\Models\Customer;
use App\Models\Item;
use App\Models\Purchase;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class PurchaseController extends Controller
{

    // Liste des achats
    public function list(): JsonResponse
    {
        $purchases = Purchase::with('store', 'items')->get();

        $purchases_table = [];

        foreach ($purchases as $purchase) {
            $item = $purchase->items()->first();
            // Référence de la vente
            $ref_purchase = $purchase->ref_purchase;

            // Nom du magasin
            $store_name = $purchase->store->name;

            // Articles achetés
            $items = $purchase->items()->get();

            $items_names = [];
            $quantities = [];

            foreach ($items as $item) {
                // Noms des articles
                $items_names[] = $item->name;

                // Quantités achetées
                $quantities[] = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
            }

            // Ville
            $city = $purchase->store->location;

            $purchases_table[] = [
                'id' => $purchase->id,
                'ref_purchase' => $ref_purchase,
                'store_name' => $store_name,
                'items_names' => $items_names,
                'quantities' => $quantities,
                'city' => $city,
                'created_at' => $purchase->created_at,
                'updated_at' => $purchase->updated_at,
                'today' => date('Y-m-d'),
                'purchase_date_time' => $purchase->purchase_date_time
            ];

            usort($purchases_table, function ($a, $b) {
                return strtotime($b['created_at']) - strtotime($a['created_at']);
            });
        }

        return response()->json($purchases_table);
    }

    // Liste des ventes journalières
    public function dailySalesList(): JsonResponse
    {
        // $purchases = Purchase::with('store', 'items')->where('purchase_date_time', date('Y-m-d'))->get();
        $purchases = Purchase::with('store', 'items')
            ->whereDate('purchase_date_time', '=', date('Y-m-d'))
            ->get();

        $purchases_table = [];

        foreach ($purchases as $purchase) {
            // Référence de la vente
            $ref_purchase = $purchase->ref_purchase;

            // Nom du magasin
            $store_name = $purchase->store->name;

            // Articles achetés
            $items = $purchase->items()->get();

            $items_names = [];
            $quantities = [];

            foreach ($items as $item) {
                // Noms des articles
                $items_names[] = $item->name;

                // Quantités achetées
                $quantities[] = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
            }



            // Ville
            $city = $purchase->store->location;

            $purchases_table[] = [
                'id' => $purchase->id,
                'ref_purchase' => $ref_purchase,
                'store_name' => $store_name,
                'items_names' => $items_names,
                'quantities' => $quantities,
                'city' => $city,
                'created_at' => $purchase->created_at,
                'updated_at' => $purchase->updated_at,
            ];
        }

        $purchases = array_slice($purchases_table, 0, 4);

        return response()->json($purchases_table);
    }

    // Ajout d'un achat
    public function store(PurchaseFormRequest $request): JsonResponse
    {
        // Récupérer le client
        $customer_name = $request->input('customer_name');
        $customer = Customer::where('name_customer', $customer_name)->first();

        // Récupérer le magasin
        $store_name = $request->input('store_name');
        $store = Store::where('name', $store_name)->first();

        // Récupérer les produits et leurs quantités
        foreach ($request->input('items_names') as $item_name) {
            $items_ids[] = Item::where('name', $item_name)->first()->id;
        }

        foreach ($request->input('quantities') as $quantity) {
            $quantities[] = $quantity;
        }

        // Vérifier si les quantités demandées sont disponibles pour chaque article
        $associations = array_map(function ($item_id, $quantity) {
            return [
                'item_id' => $item_id,
                'quantity' => $quantity,
            ];
        }, $items_ids, $quantities);

        // Initialisation du tableau des erreurs
        $errors = [];

        foreach ($associations as $ask) {
            // Déterminer l'article et la quantité demandée
            $item_ask = Item::where('id', $ask['item_id'])->first();
            $quantity_ask = $ask['quantity'];

            // Déterminer la quantité en stock
            $stock_quantity = $item_ask->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

            // Si la quantité en stock dans le magasin est inférieure à celle demandée,
            // renvoyer un message d'erreur.
            if ($stock_quantity < $quantity_ask) {
                $errors[] = ['error' => "Le client demande $quantity_ask quantités de l'article $item_ask->name. Il n'en reste que $stock_quantity en stock dans ce magasin."];
            }
        }

        // Retourner les erreurs s'il y en a
        if (!empty($errors)) {
            return response()->json(['errors' => $errors], 400);
        }

        // Créer l'achat
        $customer_name = $request->input('customer_name');
        $date = date('YmdHis');

        $ref_purchase = sprintf('%s%s', $customer_name, $date);
        $purchase_date_time = date('Y-m-d H:i:s');

        $purchase = Purchase::create([
            'ref_purchase' => $ref_purchase,
            'purchase_date_time' => $purchase_date_time,
        ]);

        // Associer le client et le magasin
        $purchase->customer()->associate($customer);
        $purchase->store()->associate($store);
        $purchase->save();

        // Attacher l'achat aux différents articles
        $purchase->items()->attach($associations);

        // Mettre à jour les quantités des différents articles dans le magasin
        foreach ($associations as $ask) {
            // Déterminer l'article et la quantité demandée
            $item_ask = Item::where('id', $ask['item_id'])->first();
            $quantity_ask = $ask['quantity'];

            // Déterminer la quantité en stock
            $stock_quantity = $item_ask->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

            // Nouvelle quantité
            $new_quantity = $stock_quantity - $quantity_ask;

            // Mettre à jour la quantité dans le magasin
            $item_ask->stores_stock()->updateExistingPivot($store->id, ['quantity' => $new_quantity]);
        }

        return response()->json(['message' => "Achat créé avec succès !", 'purchase' => $purchase], 201);
    }

    // Mise à jour d'achat
    public function update(PurchaseFormRequest $request, Purchase $purchase): JsonResponse
    {
        // Modifier l'achat
        $purchase->update([
            'purchase_date_time' => date('Y-m-d H:i:s'),
        ]);

        // Récupérer le client
        $customer_name = $request->input('customer_name');
        $customer = Customer::where('name_customer', $customer_name)->first();

        // Récupérer le magasin
        $store_name = $request->input('store_name');
        $store = Store::where('name', $store_name)->first();

        // Associer le client et le magasin à l'achat
        $purchase->customer()->associate($customer);
        $purchase->store()->associate($store);

        // Sauvegarder
        $purchase->save();

        // Récupérer les produits et leurs quantités
        foreach ($request->input('items_names') as $item_name) {
            $items_ids[] = Item::where('name', $item_name)->first()->id;
        }

        foreach ($request->input('quantities') as $quantity) {
            $quantities[] = $quantity;
        }

        // Vérifier si les quantités demandées sont disponibles pour chaque article
        $associations = array_map(function ($item_id, $quantity) {
            return [
                'item_id' => $item_id,
                'quantity' => $quantity,
            ];
        }, $items_ids, $quantities);

        foreach ($associations as $ask) {
            // Déterminer l'article et la quantité demandée
            $item_ask = Item::where('id', $ask['item_id'])->first();
            $quantity_ask = $ask['quantity'];

            // Déterminer la quantité en stock actuellement
            $stock_quantity = $item_ask->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

            // Déterminer l'ancienne quantité achetée
            $old_quantity = $purchase->items()->where('item_id', $item_ask->id)->first()->pivot->quantity;

            // Déterminer la quantité de départ (sans le précédent achat)
            $departure_quantity = $stock_quantity + $old_quantity;

            // Si la quantité en stock dans le magasin est inférieure à celle demandée,
            // renvoyer un message d'erreur.
            if ($departure_quantity < $quantity_ask) {
                $errors[] = ['error' => "Le client demande $quantity_ask quantités de l'article $item_ask->name. Il n'en reste que $stock_quantity en stock dans ce magasin."];
            }
        }

        // Retourner les erreurs s'il y en a
        if (!empty($errors)) {
            return response()->json(['errors' => $errors]);
        }

        // Récupérer les anciennes quantités des articles de l'achat
        $old_purchase_items = $purchase->items()->get();
        foreach ($old_purchase_items as $item) {
            // Déterminer la quantité en stock actuellement
            $stock_quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

            // Déterminer l'ancienne quantité achetée
            $old_quantity = $purchase->items()->where('item_id', $item->id)->first()->pivot->quantity;

            // Revenir à la quantité de départ (cela veut dire qu'on annule l'ancien achat pour faire la modification)
            $departure_quantity = $stock_quantity + $old_quantity;

            $item->stores_stock()->updateExistingPivot($store->id, ['quantity' => $departure_quantity]);
        }

        // Attacher l'achat aux différents articles
        $purchase->items()->detach();
        $purchase->items()->attach($associations);

        // Mettre à jour les quantités des différents articles dans le magasin
        foreach ($associations as $ask) {
            // Déterminer l'article et la quantité demandée
            $item_ask = Item::where('id', $ask['item_id'])->first();
            $quantity_ask = $ask['quantity'];

            // Déterminer la quantité en stock
            $stock_quantity = $item_ask->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

            // Nouvelle quantité
            $new_quantity = $stock_quantity - $quantity_ask;

            // Mettre à jour la quantité dans le magasin
            $item_ask->stores_stock()->updateExistingPivot($store->id, ['quantity' => $new_quantity]);
        }

        return response()->json(['message' => "Achat modifié avec succès !", 'purchase' => $purchase], 200);
    }

    // Suppression d'achat

    public function delete(Purchase $purchase): JsonResponse
    {
        // Si l'achat n'existe pas, renvoyer vers une page not found
        if (!$purchase) {
            return response()->json(['error' => "Achat non trouvé !"], 404);
        }

        // Récupérer le magasin de l'achat
        $store = $purchase->store;

        // Restaurer les quantités des articles achetés et supprimer leurs liens avec l'achat
        $items_purchased = $purchase->items()->get();
        foreach ($items_purchased as $item) {
            // Restauration de la quantité
            $quantity = $item->purchases()->wherePivot('purchase_id', $purchase->id)->first()->pivot->quantity;
            $stock_quantity = $store->items_stock()->wherePivot('item_id', $item->id)->first()->pivot->quantity;
            $new_quantity = $stock_quantity + $quantity;
            $store->items_stock()->updateExistingPivot($item->id, ['quantity' => $new_quantity]);

            // Suppression du lien
            $purchase->items()->updateExistingPivot($item->id, ['deleted_at' => now()]);
        }

        // Suppression logique
        $purchase->delete();

        return response()->json(['message' => "Achat supprimé avec succès !"], 200);
    }
}

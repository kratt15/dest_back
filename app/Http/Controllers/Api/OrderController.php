<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderFormRequest;
use App\Models\Item;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\JsonResponse;

class OrderController extends Controller
{

    // Liste des commandes
    public function list(): JsonResponse
    {
        $orders = Order::with('store', 'items')->get();

        // Initialisation de la liste
        $orders_table = [];

        foreach ($orders as $order) {
            // Nom du produit
            $item_name = $order->items()->first()->name;

            // Coût du produit
            $item_cost = $order->items()->first()->cost;

            // Quantité commandée
            $quantity = $order->items()->first()->pivot->quantity;

            // Coût total
            $total_cost = $item_cost * $quantity;

            // Nom du magasin
            $store_name = $order->store->name;

            // Date d'émission de la commande
            $issue_date = $order->issue_date;

            // Date prévue de livraison
            $predicted_date = $order->predicted_date;

            // Date de réception de la commande
            $reception_date = $order->reception_date;

            $orders_table[] = [

                "id" => $order->id,
                "item_name" => $item_name,
                "quantity" => $quantity,
                "total_cost" => $total_cost,
                "store_name" => $store_name,
                "issue_date" => $issue_date,
                "predicted_date" => $predicted_date,
                "reception_date" => $reception_date,
                "created_at" => $order->created_at,
                "updated_at" => $order->updated_at,
            ];
        }

        usort($orders_table, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json($orders_table);
    }

    // Liste des commandes acceptées
    public function accepted_orders_list(): JsonResponse
    {
        $orders = Order::with('store', 'items')->get();

        // Initialisation de la liste
        $orders_table = [];

        foreach ($orders as $order) {
            // Vérifier si la commande n'est pas acceptée
            if ($order->reception_date) {
                // Nom du produit
                $item_name = $order->items()->first()->name;

                // Coût du produit
                $item_cost = $order->items()->first()->cost;

                // Quantité commandée
                $quantity = $order->items()->first()->pivot->quantity;

                // Coût total
                $total_cost = $item_cost * $quantity;

                // Nom du magasin
                $store_name = $order->store->name;

                // Date d'émission de la commande
                $issue_date = $order->issue_date;

                // Date prévue de livraison
                $predicted_date = $order->predicted_date;

                // Date de réception de la commande
                $reception_date = $order->reception_date;

                $orders_table[] = [
                    "id" => $order->id,
                    "item_name" => $item_name,
                    "quantity" => $quantity,
                    "total_cost" => $total_cost,
                    "store_name" => $store_name,
                    "issue_date" => $issue_date,
                    "predicted_date" => $predicted_date,
                    "reception_date" => $reception_date,
                    "created_at" => $order->created_at,
                    "updated_at" => $order->updated_at,
                ];
            }
        }

        return response()->json($orders_table);
    }

    // Liste des commandes non acceptées
    public function unaccepted_orders_list(): JsonResponse
    {
        $orders = Order::with('store', 'items')->get();

        // Initialisation de la liste
        $orders_table = [];

        foreach ($orders as $order) {
            // Vérifier si la commande n'est pas acceptée
            if (!$order->reception_date) {
                // Nom du produit
                $item_name = $order->items()->first()->name;

                // Coût du produit
                $item_cost = $order->items()->first()->cost;

                // fournisseur du produit
                $provider_name = $order->items()->first()->provider->name_provider;

                // Quantité commandée
                $quantity = $order->items()->first()->pivot->quantity;

                // Coût total
                $total_cost = $item_cost * $quantity;

                // Nom du magasin
                $store_name = $order->store->name;

                // Date d'émission de la commande
                $issue_date = $order->issue_date;

                // Date prévue de livraison
                $predicted_date = $order->predicted_date;

                $orders_table[] = [
                    "id" => $order->id,
                    "item_name" => $item_name,
                    "quantity" => $quantity,
                    "provider_name" => $provider_name,
                    "total_cost" => $total_cost,
                    "store_name" => $store_name,
                    "issue_date" => $issue_date,
                    "predicted_date" => $predicted_date,
                    "created_at" => $order->created_at,
                    "updated_at" => $order->updated_at,
                ];
            }
        }

        usort($orders_table, function ($a, $b) {
            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json($orders_table);
    }

    // Ajout d'une commande
    public function store(OrderFormRequest $request): JsonResponse
    {
        // Créer la commande d'approvisionnement
        $order = Order::create([
            'issue_date' => date('Y-m-d H:i:s'),
            'predicted_date' => $request->input('predicted_date'),
        ]);

        // Récupérer l'article
        $item_name = $request->input('item_name');
        $item = Item::where('name', $item_name)->first();

        // Récupérer le magasin
        $store_name = $request->input('store_name');
        $store = Store::where('name', $store_name)->first();

        // Lier le magasin à la commande
        $order->store()->associate($store);
        $order->save();

        // Lier l'article à la commande avec la quantité commandée
        $quantity = $request->input('quantity');
        $order->items()->attach($item, ['quantity' => $quantity]);

        return response()->json(['message' => "Commande créée avec succès !", 'order' => $order], 201);
    }

    // Mise à jour de commande
    public function update(OrderFormRequest $request, Order $order): JsonResponse
    {
        // Mettre à jour la commande d'approvisionnement
        $order->update([
            'issue_date' => date('Y-m-d H:i:s'),
            'predicted_date' => $request->input('predicted_date'),
        ]);

        // Récupérer le nouvel article
        $item_name = $request->input('item_name');
        $item = Item::where('name', $item_name)->first();

        // Récupérer le nouveau magasin
        $store_name = $request->input('store_name');
        $store = Store::where('name', $store_name)->first();

        // Lier le nouveau magasin à la commande
        $order->store()->associate($store);
        $order->save();

        // Lier le nouvel article à la commande avec la quantité commandée
        $quantity = $request->input('quantity');
        $order->items()->detach();
        $order->items()->attach($item, ['quantity' => $quantity]);

        return response()->json(['message' => "Commande modifiée avec succès !", 'order' => $order], 200);
    }

    // Suppression de commande
    public function delete(Order $order): JsonResponse
    {
        // Si la commande n'existe pas, renvoyer vers une page not found
        if (!$order) {
            return response()->json(['error' => "Commande non trouvée"], 404);
        }

        // Vérifier si on a reçu la commande.
        // Si non on peut la supprimer logiquement avec ses liaisons.
        if (!$order->reception_date) {
            $order->delete();
            // Récupérer l'article
            $item = $order->items()->first();
            $order->items()->updateExistingPivot($item->id, ['deleted_at' => now()]);
            return response()->json(['message' => "Commande supprimée avec succès !"], 200);
        } else {
            return response()->json(['error' => "Les commandes acceptées ne peuvent pas être supprimées !"], 200);
        }
    }

    // Acceptation de commande

    public function accept_order(Order $order): JsonResponse
    {
        if (!$order) {
            return response()->json(['error' => "Commande non trouvée"], 404);
        }

        // Récupérer l'article de la commande
        $item = $order->items()->first();

        // Récupérer le magasin de la commande
        $store = $order->store()->first();

        // Quantité à ajouter
        $add_quantity = $order->items()->where('item_id', $item->id)->first()->pivot->quantity;

        // Récupérer l'ancienne quantité si l'article existe déjà dans le magasin
        // Sinon associer l'article au magasin et y ajouter la quantité
        if ($item->stores_stock()->wherePivot('store_id', $store->id)->exists()) {
            // Vérifier si la commande n'a pas déjà été acceptée
            if (!$order->reception_date) {
                // Ancienne quantité
                $old_quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

                // Nouvelle quantité
                $new_quantity = $old_quantity + $add_quantity;

                // Après avoir accepté la commande, ajouter la quantité au produit commandé.
                $item->stores_stock()->updateExistingPivot($store->id, ['quantity' => $new_quantity]);

                // Mettre à jour la commande (renseigner sa date de réception)
                $order->reception_date = now();
                $order->save();
            } else {
                return response()->json(['error' => "Cette commande a déjà été réceptionnée !"]);
            }
        } else {
            // Lier l'article au magasin et ajouter la quantité.
            $item->stores_stock()->attach($store->id, ['quantity' => $add_quantity]);
        }

        return response()->json(['message' => "Commande acceptée avec succès !"]);
    }


    // public function send_delivery_notifications()
    // {
    //     $orders = Order::whereNotNull('predicted_date')->get();

    //     foreach ($orders as $order) {
    //         $predictedDate = $order->predicted_date;
    //         $oneDayBefore = date('Y-m-d', strtotime('-1 day', strtotime($predictedDate)));

    //         if (date('Y-m-d') == $oneDayBefore) {
    //             $users = User::where('role', 'admin')->orWhere('role', 'gerant')->get();

    //             foreach ($users as $user) {
    //                 $email = $user->email;
    //                 $data = [
    //                     'name' => $user->name,
    //                     'order_id' => $order->id,
    //                     'item_name' => $order->items()->first()->name,
    //                     'quantity' => $order->items()->first()->pivot->quantity,
    //                     'predicted_date' => $predictedDate,
    //                 ];

    //                 Mail::to($email)->send(new DeliveryNotification($data));
    //             }
    //         }
    //     }

    // }
}

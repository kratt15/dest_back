<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Store;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;

class ArticleListeController extends Controller
{
    //
    public function sortByStore(Store $store)
    {


        // Récupérer le tableau du magasin choisi
        $stores = Store::with('items_stock')->where('id', $store->id)->get();

        // Initialisation de la liste des articles à afficher
        $list = [];

        // Récupérer la liste des articles
        foreach ($stores as $store) {
            foreach ($store->items_stock as $item) {
                $item = Item::with('category', 'provider')->where('id', $item->id)->first();

                if ($item) {
                    // Récupérer la quantité de l'article courant dans le magasin choisi
                    $storesStock = $item->stores_stock()->wherePivot('store_id', $store->id)->first();

                    if ($storesStock) {
                        $quantity = $storesStock->pivot->quantity;
                    } else {
                        $quantity = 0; // Ou une valeur par défaut appropriée si la relation n'existe pas
                    }

                    $list[] = [
                        'name' => $item->name,
                        'reference' => $item->reference,
                        'quantity' => $quantity,

                    ];
                }
            }
        }


        // Ranger les articles par magasin (ordre croissant)
        usort($list, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return $list;
    }



    // Liste des articles par magasin


    public function listParStore()
{
    // Initialisation de la liste des articles à afficher
    $allItems = [];

    // Récupérer la liste des magasins
    $stores = Store::all();

    // Récupérer les articles pour chaque magasin et les ajouter à la liste
    foreach ($stores as $store) {
        $itemsForStore = $this->sortByStore($store);
        $allItems[$store->name] = $itemsForStore;
    }

    return $allItems;
}


    public function getItemsListFromStore()
    {

        $itemsJson = $this->listParStore();

        // Décodez les données JSON en tableau associatif
        // $items = json_decode($itemsJson);
        // return $itemsJson;
        // return view('listes.articleParMag', ['data' => $itemsJson]);
        $pdf = Pdf::loadView('listes.articleParMag', ['data' => $itemsJson]);
        return $pdf->stream();
    }

    public function supplyFlowListe($startDate = null, $endDate = null)
{
    $ordersQuery = Order::with('items')->orderBy('created_at', 'desc');

    if ($startDate && $endDate) {
        // Si les dates de début et de fin sont spécifiées, filtrer les commandes en conséquence
        $ordersQuery->whereBetween('created_at', [$startDate, $endDate]);
    } elseif (!$startDate && !$endDate) {
        // Si aucune date n'est spécifiée, récupérer les 20 dernières commandes
        $ordersQuery->take(20);
    }

    // Récupérer les commandes
    $orders = $ordersQuery->get();

    // Initialisation de la liste
    $supply_list = [];

    // Type de mouvement
    $movement = "Approvisionnement";

    // Approvisionnements
    foreach ($orders as $order) {
        // Vos traitements ici...

           // Nom du magasin
           $store_name = $order->store->name;

           // Date de création de la commande
           $created_at = $order->created_at;

           // Date de livraison
           $reception_date = ($order->reception_date === null) ? 'Non reçu' : $order->reception_date;

           // Date prévue de la livraison
           $predicted_date = $order->predicted_date;

           // Articles liés à cette commande
           $items = $order->items;

           // Remplir la liste des approvisionnements
           foreach ($items as $item) {
               // Nom de l'article
               $item_name = $item->name;

               // Quantité fournie
               $quantity = $item->pivot->quantity;

               // Nom du fournisseur
               $provider_name = $item->provider->name_provider;

               $supply_list[] = [
                   'movement' => $movement,
                   'store_name' => $store_name,
                   'item_name' => $item_name,
                   'quantity' => $quantity,
                   'provider_name' => $provider_name,
                   "reception_date" => $reception_date,
                   'predicted_date' => $predicted_date,
                   'created_at' => $created_at,
               ];
           }
    }
    $pdf = Pdf::loadView('listes.approvionnement',compact('supply_list'));
    return $pdf->stream();
    // return view('listes.approvionnement',compact('supply_list'));
}

}


<?php

namespace App\Http\Controllers;

use App\Models\Item;
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
}

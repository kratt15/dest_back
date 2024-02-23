<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use App\Models\Order;
use App\Models\Store;
use App\Models\Brands;
use App\Models\Calendar;
use App\Models\Category;
use App\Models\Provider;
use App\Models\Purchase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use App\Http\Requests\ItemFormRequest;
use App\Http\Requests\TransferItemFormRequest;

class ItemController extends Controller
{
    // Fonction permettant de récupérer la liste des articles
    private function items_list(): array
    {
        $items = Item::with('category', 'provider', 'purchases', 'stores_stock')->get();

        $list = [];

        foreach ($items as $item) {
            // Déterminer la quantité totale vendue pour cet article
            $totalSoldQuantity = 0;
            $totalSoldQuantity += $item->purchases()->sum('quantity');

            // Récupérer la catégorie
            $category = $item->category->title;

            // Récupérer le fournisseur
            // $provider = $item->provider->name_provider;
            // Récupérer le fournisseur
            $provider = $item->provider ? $item->provider->name_provider : null;


            // Déterminer le total des quantités dans chaque magasin
            $totalAvailableQuantity = 0;
            $totalAvailableQuantity += $item->stores_stock()->sum('quantity');

            // Ajouter l'article à la liste
            $list[] = [

                'id' => $item->id,
                'name' => $item->name,
                'reference' => $item->reference,
                // 'expiration_date' => $item->expiration_date,
                'cost' => $item->cost,
                'price' => $item->price,
                'totalAvailableQuantity' => $totalAvailableQuantity,
                'totalSoldQuantity' => $totalSoldQuantity,
                'category' => $category,
                'provider' => $provider,
                'updated_at' => $item->updated_at,
                'created_at' => $item->created_at,

            ];
        }

        return $list;
    }

    // Liste des articles
    public function list(): JsonResponse
    {
        $list = $this->items_list();

        usort($list, function ($a, $b) {

            return strtotime($b['created_at']) - strtotime($a['created_at']);
        });

        return response()->json($list);
    }

    // Tri par ordre croissant
    public function sortAscendingList(): JsonResponse
    {
        $list = $this->items_list();

        // Fonction de comparaison pour trier en fonction du nom de l'article en ordre croissant
        usort($list, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json($list);
    }

    // Tri par ordre décroissant
    public function sortDescendingList(): JsonResponse
    {
        $list = $this->items_list();

        // Fonction de comparaison pour trier en fonction du nom de l'article en ordre croissant
        usort($list, function ($a, $b) {
            return strcmp($b['name'], $a['name']);
        });

        return response()->json($list);
    }

    // Liste des 5 articles les plus vendus
    public function getMostSoldItems(): JsonResponse
    {
        $list = $this->items_list();

        // Ranger les articles par ordre décroissant du nombre de vente
        usort($list, function ($a, $b) {
            return $b['totalSoldQuantity'] - $a['totalSoldQuantity'];
        });

        $list = array_slice($list, 0, 4);

        return response()->json($list);
    }

    // Liste des articles par catégorie
    public function sortByCategory(): JsonResponse
    {
        $list = $this->items_list();

        // Ranger les articles par catégorie (ordre croissant)
        usort($list, function ($a, $b) {
            return strcmp($a['category'], $b['category']);
        });

        return response()->json($list);
    }

    // Liste des articles par fournisseur
    public function sortByProvider(): JsonResponse
    {
        $list = $this->items_list();

        // Ranger les articles par fournisseur (ordre croissant)
        usort($list, function ($a, $b) {
            return strcmp($a['provider'], $b['provider']);
        });

        return response()->json($list);
    }

    // Liste des articles par magasin
    public function sortByStore(Store $store): JsonResponse
    {
        // // Récupérer le tableau du magasin choisi
        // $stores = Store::with('items_stock')->where('id', $store->id)->get();

        // // Initialisation de la liste des articles à afficher
        // $list = [];

        // // Récupérer la liste des articles
        // foreach ($stores as $store) {
        //     foreach ($store->items_stock as $item) {
        //         $item = Item::with('category', 'provider')->where('id', $item->id)->get();

        //         // Récupérer la quantité de l'article courant dans le magasin choisi
        //         $quantity = $item[0]->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

        //         $list[] = [
        //             'name' => $item[0]->name,
        //             'reference' => $item[0]->reference,
        //             'expiration_date' => $item[0]->expiration_date,
        //             'cost' => $item[0]->cost,
        //             'price' => $item[0]->price,
        //             'quantity' => $quantity,
        //             'category' => $item[0]->category->title,
        //             'provider' => $item[0]->provider->name_provider,
        //             'updated_at' => $item[0]->updated_at,
        //         ];
        //     }
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
                        'expiration_date' => $item->expiration_date,
                        'cost' => $item->cost,
                        'price' => $item->price,
                        'quantity' => $quantity,
                        'category' => $item->category->title,
                        'provider' => $item->provider->name_provider,
                        'updated_at' => $item->updated_at,
                    ];
                }
            }
        }


        // Ranger les articles par magasin (ordre croissant)
        usort($list, function ($a, $b) {
            return strcmp($a['name'], $b['name']);
        });

        return response()->json($list);
    }

    // Transfert d'articles d'un magasin à un autre
    public function transfer(TransferItemFormRequest $request, Store $store): JsonResponse
    {
        // Récupérer le nom du magasin de départ
        $departure_store_name = $store->name;

        // Récupérer l'article à partir de son nom
        $item_name = $request->input('item_name');
        $item = Item::with('stores_stock')->where('name', $item_name)->first();

        // Vérifier si le magasin de départ ne possède pas cet article (pour prévenir les bugs)
        if (!$item->stores_stock()->wherePivot('store_id', $store->id)->exists()) {
            return response()->json(['error' => "Le magasin $departure_store_name ne dispose pas de l'article $item_name."]);
        }

        // Vérifier si la quantité disponible de cet article dans le magasin est nulle.
        $item_available_quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;
        if ($item_available_quantity === 0) {
            return response()->json(['error' => "L'article $item_name est en rupture de stock dans le magasin $departure_store_name."]);
        }

        // Récupérer la quantité à transférer
        $transfer_quantity = $request->input('quantity');

        // Vérifier si la quantité à transférer est supérieure à la quantité disponible dans le magasin de départ.
        if ($transfer_quantity > $item_available_quantity) {
            return response()->json(['error' => "Il y a $item_available_quantity $item_name dans le magasin $departure_store_name. La quantité que vous souhaitez transférer est plus grande que celle disponible."]);
        }

        // Récupérer le magasin de destination à partir de son nom
        $destination_store_name = $request->input('destination_store_name');
        $destination_store = Store::where('name', $destination_store_name)->first();

        // Créer un nouvel enregistrement dans le calendrier
        $calendar = Calendar::create();

        // Enregistrer le transfert dans la table pivot
        $store->items_transfer()->attach($item->id, ['calendar_id' => $calendar->id, 'destination_store' => $destination_store->id, 'quantity' => $transfer_quantity]);

        // Retirer la quantité de l'article dans le magasin de départ
        $old_departure_quantity = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;
        $new_departure_quantity = $old_departure_quantity - $transfer_quantity;
        $item->stores_stock()->updateExistingPivot($store->id, ['quantity' => $new_departure_quantity]);

        // Vérifier si le magasin de destination possède l'article qu'on veut lui transférer
        // Si non, associer l'article au magasin de destination et y ajouter la quantité.
        if ($item->stores_stock()->wherePivot('store_id', $destination_store->id)->exists()) {
            // Ajouter la quantité de l'article dans le magasin de destination
            $old_destination_quantity = $item->stores_stock()->wherePivot('store_id', $destination_store->id)->first()->pivot->quantity;
            $new_destination_quantity = $old_destination_quantity + $transfer_quantity;
            $item->stores_stock()->updateExistingPivot($destination_store->id, ['quantity' => $new_destination_quantity]);
        } else {
            // Asscocier l'article au magasin de destination avec la quantité transférée
            $destination_store->items_stock()->attach($item->id, ['quantity' => $transfer_quantity]);
        }


        // return response()->json(['message' => "Transfert effectué avec succès !"], 200);
        return response()->json(['message' => "Transfert de $transfer_quantity unités de l'article $item_name du magasin $store->name au magasin $destination_store_name effectué avec succès !"]);
    }

    // Fonction permettant de récupérer la liste des mouvements d'approvisionnement (ordre croissant de création)
    private function supplyFlowList(): array
    {
        // Récupérer les commandes (approvisionnement) rangées par ordre croissant de création
        $orders = Order::with('items')->orderBy('created_at', 'asc')->get();

        // Initialisation de la liste
        $supply_list = [];

        // Type de mouvement
        $movement = "Approvisionnement";

        // Approvisionnements
        foreach ($orders as $order) {
            // Nom du magasin
            $store_name = $order->store->name;

            // Date de création de la commande
            $created_at = $order->created_at;

            // Date de livraison
            $reception_date = '';

            if ($order->reception_date === null) {
                $reception_date = 'Non reçu';
            } else {
                $reception_date = $order->reception_date;
            }





            // Date prévue de la livraison
            $predicted_date = $order->predicted_date;

            // Articles liés à cette commande
            $items = $order->items()->where('order_id', $order->id)->get();

            // Remplir la liste des approvisionnements
            foreach ($items as $item) {
                // Nom de l'article
                $item_name = $item->name;
                // Quantité fournie
                $quantity = $item->orders()->wherePivot('order_id', $order->id)->first()->pivot->quantity;
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

        return $supply_list;
    }

    // Fonction permettant de récupérer la liste des mouvements de vente (ordre croissant de création)
    private function salesFlowList(): array
    {
        // Récupérer les achats (vente) rangés par ordre croissant de création
        $purchases = Purchase::with('items')->orderBy('created_at', 'asc')->get();

        // Initialisation de la liste
        $sales_list = [];

        // Type de mouvement
        $movement = "Vente";

        // Ventes
        foreach ($purchases as $purchase) {
            // Nom du magasin
            $store_name = $purchase->store->name;

            // Date de création de la vente
            $created_at = $purchase->created_at;

            // Articles liés à cet achat
            $items = $purchase->items()->where('purchase_id', $purchase->id)->get();

            // Nom du client
            $customer_name = $purchase->customer->name_customer;

            // Vérifier si le client à tout soldé
            $dueAmount = 0;
            $items = $purchase->items()->get();
            foreach ($items as $item) {
                $item_price = $item->price;
                $item_qte = $item->purchases()->wherePivot("purchase_id", $purchase->id)->first()->pivot->quantity;
                $amount = $item_price * $item_qte;
                $dueAmount += $amount;
            }

            $paidAmount = 0;
            $payments = $purchase->payments()->get();
            foreach ($payments as $payment) {
                $payment_amount = $payment->amount;
                $paidAmount += $payment_amount;
            }

            // Statut de la vente (soldé ou non soldé)
            if ($purchase->payments()->where('purchase_id', $purchase->id)->exists() && $paidAmount === $dueAmount) {
                $statut = "Soldé";
            } else {
                $statut = "Non soldé";
            }

            // Remplir la liste des ventes
            foreach ($items as $item) {
                // Nom de l'article
                $item_name = $item->name;
                // Quantité vendue
                $quantity = $item->purchases()->wherePivot('purchase_id', $purchase->id)->first()->pivot->quantity;

                $sales_list[] = [
                    'movement' => $movement,
                    'store_name' => $store_name,
                    'item_name' => $item_name,
                    'quantity' => $quantity,
                    'customer_name' => $customer_name,
                    'statut' => $statut,
                    'created_at' => $created_at,
                ];
            }
        }

        return $sales_list;
    }

    // Fonction permettant de récupérer la liste des mouvements d'approvisionnement et de vente (non rangée)
    private function supplyAndSalesFlowList(): array
    {
        // Récupérer les approvisionnements
        $supply_list = $this->supplyFlowList();

        // Récupérer les ventes
        $sales_list = $this->salesFlowList();

        // Réunir les tableaux d'approvisionnement et de vente
        $supply_sales_list[] = array_merge($supply_list, $sales_list);

        return $supply_sales_list;
    }

    // Liste des mouvements d'approvisionnement et de vente (par ordre croissant)
    // Il s'agit de la liste par défaut à afficher.
    public function supplyAndSalesFlowAsc(): JsonResponse
    {
        // Récupérer la liste des approvisionnements et ventes
        $supply_sales_list = $this->supplyAndSalesFlowList();

        // Ranger la liste des approvisionnements et ventes par ordre de création
        usort($supply_sales_list[0], function ($a, $b) {
            return strcmp($a['created_at'], $b['created_at']);
        });

        return response()->json($supply_sales_list);
    }

    // Liste des mouvements d'approvisionnement et de vente (par ordre décroissant)
    public function supplyAndSalesFlowDesc(): JsonResponse
    {
        // Récupérer la liste des approvisionnements et ventes
        $supply_sales_list = $this->supplyAndSalesFlowList();

        // Ranger la liste des approvisionnements et ventes par ordre décroissant
        usort($supply_sales_list[0], function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return response()->json($supply_sales_list);
    }

    // Liste des approvisionnements (par ordre décroissant)
    public function supplyList(): JsonResponse
    {
        // Récupérer les approvisionnements
        $supply_list = $this->supplyFlowList();

        // Ranger la liste des approvisionnements par ordre décroissant
        usort($supply_list, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return response()->json($supply_list);
    }

    // Liste des ventes (par ordre décroissant)
    public function salesList(): JsonResponse
    {
        // Récupérer les ventes
        $sales_list = $this->salesFlowList();

        // Ranger la liste des ventes par ordre décroissant
        usort($sales_list, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        return response()->json($sales_list);
    }

    // Liste des ventes non soldées (par ordre croissant)
    public function openSalesList(): JsonResponse
    {
        // Récupérer les ventes
        $sales_list = $this->salesFlowList();

        // Initialisation du tableau des ventes non soldées
        $open_sales_list = [];

        // Récupérer uniquement les ventes non soldées
        foreach ($sales_list as $sale) {
            if ($sale['statut'] === "Non soldé") {
                $open_sales_list[] = $sale;
            }
        }

        return response()->json($open_sales_list);
    }

    // Ajout d'un article
    public function store(ItemFormRequest $request): JsonResponse
    {

        // Récupérer la catégorie
        $category = Category::where('title', $request->input('category_title'))->first();

        // récuperer la marque
        $brand = Brands::where('title', $request->input('brand_title'))->first();

        // Récupérer le fournisseur
        $provider = Provider::where('name_provider', $request->input('provider_name'))->first();

        // Créer l'article
        $item = Item::create([
            'name' => $request->input('name'),
            'reference' => $request->input('reference'),
            // 'expiration_date' => $request->input('expiration_date'),
            'cost' => $request->input('cost'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
        ]);

        // Lier l'article à sa catégorie
        $item->category()->associate($category);

        // Lier l'article à son fournisseur
        $item->provider()->associate($provider);

        // Lier l'article à sa marque
        $item->brands()->associate($brand);

        // Sauvegarder les associations
        $item->save();

        // Récupérer le magasin, la quantité et la quantité de sécurité
        $store_name = $request->input('store_name');
        $store_id = Store::where('name', $store_name)->get();
        $quantity = $request->input('quantity');
        $security_quantity = $request->input('security_quantity');

        // Lier l'article au magasin avec la quantité et quantité de sécurité
        $item->stores_stock()->attach($store_id, ['quantity' => $quantity, 'security_quantity' => $security_quantity]);

        // // Récupérer les magasins, les quantités et les quantités de sécurité
        // foreach ($request->input('stores_names') as $store_name) {
        //     $stores_ids[] = Store::where('name', $store_name)->first()->id;
        // }

        // foreach ($request->input('quantities') as $quantity) {
        //     $quantities[] = $quantity;
        // }

        // foreach ($request->input('security_quantities') as $quantity) {
        //     $security_quantities[] = $quantity;
        // }

        // // Lier l'article aux magasins avec les quantités et quantités de sécurité
        // $associations = array_map(function ($store_id, $quantity, $security_quantity) {
        //     return [
        //         'store_id' => $store_id,
        //         'quantity' => $quantity,
        //         'security_quantity' => $security_quantity,
        //     ];
        // }, $stores_ids, $quantities, $security_quantities);

        // $item->stores_stock()->attach($associations);


        return response()->json(['message' => "Article ajouté avec succès !", 'item' => $item], 201);
    }

    // Mise à jour d'un article
    public function update(Item $item, ItemFormRequest $request): JsonResponse
    {
        // Modifier l'article
        $item->update([

            'name' => $request->input('name'),
            'reference' => $request->input('reference'),
            // 'expiration_date' => $request->input('expiration_date'),
            'cost' => $request->input('cost'),
            'price' => $request->input('price'),
            'description' => $request->input('description'),
        ]);

        // Récupérer la catégorie
        $category_title = $request->input('category_title');

        // Récupérer le fournisseur
        $provider_name = $request->input('provider_name');

        // Récupérer la marque
        $brand = $request->input('brand_title');

        // Lier l'article à sa nouvelle catégorie=
        $category = Category::where('title', $category_title)->first();
        $item->category()->associate($category);

        // Lier l'article à son nouveau fournisseur
        $provider = Provider::where('name_provider', $provider_name)->first();
        $item->provider()->associate($provider);

        // Lier l'article à sa nouvelle marque
        $brand = Brands::where('title', $brand)->first();
        $item->brands()->associate($brand);
        // Sauvegarder les associations
        $item->save();

        // Récupérer le magasin, la quantité et la quantité de sécurité
        $store_name = $request->input('store_name');
        $store_id = Store::where('name', $store_name)->get();
        $quantity = $request->input('quantity');
        $security_quantity = $request->input('security_quantity');

        // Lier l'article au magasin avec la quantité et quantité de sécurité
        $item->stores_stock()->detach();
        $item->stores_stock()->attach($store_id, ['quantity' => $quantity, 'security_quantity' => $security_quantity]);

        // // Récupérer les magasins, les quantités et les quantités de sécurité
        // foreach ($request->input('stores_names') as $store_name) {
        //     $stores_ids[] = Store::where('name', $store_name)->first()->id;
        // }

        // foreach ($request->input('quantities') as $quantity) {
        //     $quantities[] = $quantity;
        // }

        // foreach ($request->input('security_quantities') as $quantity) {
        //     $security_quantities[] = $quantity;
        // }

        // // Lier l'article aux magasins avec les quantités et quantités de sécurité
        // $associations = array_map(function ($store_id, $quantity, $security_quantity) {
        //     return [
        //         'store_id' => $store_id,
        //         'quantity' => $quantity,
        //         'security_quantity' => $security_quantity,
        //     ];
        // }, $stores_ids, $quantities, $security_quantities);

        // $item->stores_stock()->detach();
        // $item->stores_stock()->attach($associations);

        return response()->json(['message' => "Article modifié avec succès !", 'item' => $item], 200);
    }

    // Suppression d'un article
    public function delete(Item $item): JsonResponse
    {
        // Si l'artile n'existe pas, renvoyer vers une page not found
        if (!$item) {
            return response()->json(['error' => "Article non trouvé"], 404);
        }

        // Supprimer logiquement les liaisons avec les magasins
        $stores = $item->stores_stock()->wherePivot('item_id', $item->id)->get();
        foreach ($stores as $store) {
            $store->items_stock()->updateExistingPivot($item->id, ['deleted_at' => now()]);
        }

        // Suppimer logiquement l'article
        $item->delete();

        return response()->json(['message' => "Article supprimé avec succès !"], 200);
    }

    // public function getMostsoldItemsCount(Item $item): JsonResponse
    // {
    //     $items = Item::withCount('sales')->orderBy('sales_count', 'desc')->take(5)->get();
    //     return response()->json($items);
    // }

    public function search(Request $request): JsonResponse
    {

        $query = $request->input('query');

        $items = Item::search($query)->get();
        $items->load('category', 'provider');

        return response()->json($items);
    }
}

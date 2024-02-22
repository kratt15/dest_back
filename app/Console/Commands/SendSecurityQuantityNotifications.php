<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\User;
use App\Notifications\SecurityQuantityNotifications;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;

class SendSecurityQuantityNotifications extends Command
{
    use Notifiable;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-security-quantity-notifications';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $items = Item::has('stores_stock', '>=', 1)->with('stores_stock')->get();

        // Initialisation de tableaux
        $notifications = [];

        foreach ($items as $item) {
            // Magasins dans lesquels l'article est stocké
            $item_stores = $item->stores_stock()->get();

            foreach ($item_stores as $store) {
                // Nom de l'article
                $item_name = $item->name;

                // Nom du magasin
                $store_name = $store->name;

                // Quantité en stock
                $item_qte = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->quantity;

                // Quantité de sécurité
                $item_security_qte = $item->stores_stock()->wherePivot('store_id', $store->id)->first()->pivot->security_quantity;

                if ($item_qte <= $item_security_qte || ($item_security_qte === NULL && $item_qte <= 5)) {
                    $users = User::where('role', 'admin')->orWhere('role', 'gerant')->get();

                    foreach ($users as $user) {
                        $data = [
                            'user_name' => $user->name,
                            'item_name' => $item_name,
                            'store_name' => $store_name,
                            'item_qte' => $item_qte,
                            'security_qte' => $item_security_qte,
                        ];

                        $user->notify(new SecurityQuantityNotifications($data));
                    }

                    $this->info('Notification envoyée avec succès!');
                } else {
                    $this->info('Aucune notification envoyée');
                }
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Purchase;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Notifications\Notifiable;
use App\Notifications\UnsettledPaymentNotifications;

class SendUnsettledPaymentNotifications extends Command
{
    use Notifiable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-unsettled-payment-notifications';

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

        // Récupérer les achats
        $purchases = Purchase::with('items')->get();

        // initialisation des tableaux
        $notifications = [];

        if (date('w') == 6) {
            foreach ($purchases as $purchase) {
                // Nom du magasin
                $store_name = $purchase->store->name;

                // Articles liés à cet achat
                $items = $purchase->items()->where('purchase_id', $purchase->id)->get();

                // Nom du client
                $customer_name = $purchase->customer->name_customer;

                // Référence de l'achat
                $ref_purchase = $purchase->ref_purchase;

                // Date de l'achat
                $purchase_date_time = $purchase->purchase_date_time;

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

                if ($statut === "Non soldé") {
                    // Rechercher les admins / gérants qui doivent recevoir la notification de payement non soldé.
                    $users = User::where('role', 'admin')->orWhere('role', 'gerant')->get();

                    foreach ($users as $user) {
                        $data = [
                            'user_name' => $user->name,
                            'ref_purchase' => $ref_purchase,
                            'purchase_date_time' => $purchase_date_time,
                            'customer_name' => $customer_name,
                            'store_name' => $store_name,
                        ];

                        $user->notify(new UnsettledPaymentNotifications($data));
                    }

                    $this->info('Unsettled payment notifications sent successfully.');
                } else {
                    $this->info('No unsettled payment notifications to send.');
                }
            }
        }
    }
}

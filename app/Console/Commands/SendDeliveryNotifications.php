<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Notifications\DeliveryNotification;
use Illuminate\Notifications\Notifiable;

class SendDeliveryNotifications extends Command
{
    use Notifiable;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:send-delivery-notifications';

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
        //

        $orders = Order::whereNotNull('predicted_date')->get();

        foreach ($orders as $order) {
            $predictedDate = $order->predicted_date;
            $oneDayBefore = date('Y-m-d', strtotime('-1 day', strtotime($predictedDate)));

            if (date('Y-m-d') == $oneDayBefore) {
                $users = User::where('role', 'admin')->orWhere('role', 'gerant')->get();

                foreach ($users as $user) {
                    //$email = $user->email;
                    $data = [
                        'name' => $user->name,
                        'order_id' => $order->id,
                        'item_name' => $order->items()->first()->name,
                        'quantity' => $order->items()->first()->pivot->quantity,
                        'predicted_date' => $predictedDate,
                    ];

                   $user->notify(new DeliveryNotification($data));
                }
            }
        }
        if (count($orders) > 0) {
            $this->info('Delivery notifications sent successfully.');
        } else {
            $this->info('No delivery notifications to send.');
        }

    }


}

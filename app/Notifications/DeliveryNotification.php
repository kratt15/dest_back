<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Contracts\Mail\Mailable;
use Illuminate\Notifications\Notification;

class DeliveryNotification extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
        //
        $this->data = $data;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
        ->from('noreply@oba-app.xyz', 'OBA zone OAPI')
        // ->subject('vous avez une livraison a recevoir dans moins de 24h !')
        // ->line('verifiez vos commandes, et soyez pret pour recevoir votre commande dans moins de 24h !')
        // ->line('Thank you for using our application!');
        ->subject('Notification de livraison')
        ->greeting('Cher '. $this->data['name'] . ',')
        ->line('Votre commande avec l\'ID ' . $this->data['order_id'] . ' sera livrée demain.')
        ->line('Nom du produit : ' . $this->data['item_name'])
        ->line('Quantité commande : ' . $this->data['quantity'])
        ->line('Date prévue de livraison : ' . $this->data['predicted_date'])
        ->line('Merci de votre confiance.')
        ->line('Cordialement,');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SecurityQuantityNotifications extends Notification
{
    use Queueable;

    private $data;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $data)
    {
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
        if (isset($this->data['security_qte'])) {
            $security_qte = $this->data['security_qte'];
        } else {
            $security_qte = "Non définie";
        }
        return (new MailMessage)
        
            ->from('noreply@oba-app.xyz', 'OBA zone OAPI')
            ->subject('Notification de quantité de stock')
            ->greeting('Cher ' . $this->data['user_name'] . ',')
            ->line('Le produit ' . $this->data['item_name'] . ' a atteint sa quantité de sécurité dans le magasin ' . $this->data['store_name'] . '.')
            ->line('Quantité restante : ' . $this->data['item_qte'])
            ->line('Quantité de sécurité : ' . $security_qte)
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

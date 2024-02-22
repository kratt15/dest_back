<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UnsettledPaymentNotifications extends Notification
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
        return (new MailMessage)
            ->from('noreply@oba-app.xyz', 'OBA zone OAPI')
            ->subject('Notification de payement non soldé')
            ->greeting('Cher ' . $this->data['user_name'] . ',')
            ->line('Le client ' . $this->data['customer_name'] . ' n\'a pas encore soldé son achat du ' . $this->data['purchase_date_time'] . '.')
            ->line('Référence d\'achat : ' . $this->data['ref_purchase'])
            ->line('Magasin d\'achat : ' . $this->data['store_name'])
            // ->line('Cliquez sur le lien suivant pour accéder à cet achat : ')
            // ->action('Accéder à l\'achat du client', url('/'))
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

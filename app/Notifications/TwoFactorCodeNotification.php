<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TwoFactorCodeNotification extends Notification
{
    use Queueable;

    public function __construct(
        private readonly string $code,
        private readonly int $expiresInMinutes,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre code de securite PIOS')
            ->greeting('Connexion a PIOS')
            ->line('Utilisez ce code pour terminer votre connexion :')
            ->line($this->code)
            ->line("Ce code expire dans {$this->expiresInMinutes} minutes.")
            ->line('Si vous n\'etes pas a l\'origine de cette demande, ignorez cet e-mail.');
    }
}

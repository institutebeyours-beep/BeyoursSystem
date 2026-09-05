<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemAlert extends Notification
{
    use Queueable;

    protected $message;
    protected $type;

    public function __construct($message, $type = 'info')
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject("⚠️ Alerta del Sistema")
            ->greeting("¡Hola {$notifiable->name}!")
            ->line($this->message)
            ->action('Ver Sistema', url('/admin/maintenance'))
            ->line('Revisa el sistema si es necesario.');
    }
}
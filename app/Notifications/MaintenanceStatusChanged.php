<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceStatusChanged extends Notification
{
    use Queueable;

    protected $status;
    protected $user;

    public function __construct($status, $user)
    {
        $this->status = $status;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $statusText = $this->status ? 'ACTIVADO' : 'DESACTIVADO';
        $icon = $this->status ? '🔒' : '🔓';

        return (new MailMessage)
            ->subject($icon . ' Modo Mantenimiento ' . $statusText)
            ->greeting('¡Hola ' . $notifiable->name . '!')
            ->line('El Super-Admin **' . $this->user->name . '** ha ' . ($this->status ? 'activado' : 'desactivado') . ' el modo mantenimiento.')
            ->line('Estado actual: **' . $statusText . '**')
            ->action('Ver Panel de Mantenimiento', '/admin/maintenance')
            ->line('Si no realizaste esta acción, contacta al administrador.')
            ->salutation('Saludos, equipo de Beyours');
    }
}
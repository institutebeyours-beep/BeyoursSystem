<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupDeleted extends Notification
{
    use Queueable;

    protected $backup;
    protected $user;

    public function __construct($backup, $user)
    {
        $this->backup = $backup;
        $this->user = $user;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🗑️ Backup Eliminado')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line("El Super-Admin **{$this->user->name}** ha eliminado un backup.")
            ->line("**Archivo:** {$this->backup->filename}")
            ->line("**Tamaño:** {$this->backup->size}")
            ->action('Ver Backups', url('/admin/maintenance'))
            ->line('Si no realizaste esta acción, contacta al administrador.');
    }
}
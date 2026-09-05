<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BackupCreated extends Notification
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
            ->subject('💾 Nuevo Backup Creado')
            ->greeting("¡Hola {$notifiable->name}!")
            ->line("El Super-Admin **{$this->user->name}** ha creado un nuevo backup.")
            ->line("**Archivo:** {$this->backup->filename}")
            ->line("**Tamaño:** {$this->backup->size}")
            ->line("**Tipo:** {$this->backup->type}")
            ->action('Ver Backups', url('/admin/maintenance'))
            ->line('Mantén tus backups seguros.');
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendTestEmail extends Command
{
    protected $signature = 'send:test {recipient}';
    protected $description = 'Enviar un correo de prueba por SMTP';

    public function handle()
    {
        $recipient = $this->argument('recipient');

        Mail::raw('Correo de prueba desde Laravel con Outlook SMTP', function ($message) use ($recipient) {
            $message->to($recipient)
                    ->subject('Prueba SMTP Outlook');
        });

        $this->info("Correo de prueba enviado a {$recipient}");
    }
}

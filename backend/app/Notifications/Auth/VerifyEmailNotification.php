<?php

namespace App\Notifications\Auth;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class VerifyEmailNotification extends Notification
{
    public function __construct(public string $verificationCode) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Confirme seu e-mail - '.config('app.name'))
            ->greeting('Olá, '.$notifiable->name.'!')
            ->line('Obrigado por se registrar no '.config('app.name').'.')
            ->line('Para completar seu cadastro, confirme seu e-mail usando o código abaixo:')
            ->line('**Código de Verificação: '.$this->verificationCode.'**')
            ->line('Este código expira em 15 minutos.')
            ->action('Verificar E-mail', config('app.frontend_url').'/verificar-email?email='.urlencode($notifiable->email))
            ->line('Se você não criou uma conta, pode ignorar este e-mail.')
            ->salutation('Atenciosamente, equipe '.config('app.name'));
    }
}

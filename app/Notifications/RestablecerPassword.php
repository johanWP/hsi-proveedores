<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RestablecerPassword extends Notification
{
    use Queueable;

    public $token;

    /**
     * Create a notification instance.
     *
     * @param  string  $token
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->from('proveedores@jockeyclub.com.ar')
                    ->subject('Jockey Club - Restablecer contraseña')
                    ->line('Está recibiendo este correo porque recibimos una petición para reestablecer su contraseña 
                    del sistema para proveedores del Jockey Club A.C.')
                    ->line('Si no ha solicitado cambiar su contraseña, puede hacer caso omiso de este mensaje.')
                    ->action('Restablecer Contraseña', url('password/reset', $this->token))
                    ->line('Gracias por usar nuestra aplicación.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}

<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Password;

class Bienvenida extends Notification
{
    use Queueable;

    public $user;
    public $token;

    /**
     * Genera el token que se envía en el
     * @param User $user
     * @param String $token
     */
    public function __construct($user)
    {
        $this->user = $user;
        $this->token = Password::getRepository()->create($user);
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
            ->from('proveedores@jockeyclub.com.ar', 'Proveedores Jockey Club A.C.')
            ->subject('Bienvenido al Sistema de Proveedores del Jockey Club A.C.')
            ->greeting('Estimado proveedor,')
            ->line('Desde el Jockey Club A.C. queremos darle la bienvenida al sistema de información para proveedores, 
            desde donde podrá acceder a información relacionada al estatus de sus facturas pendientes y los pagos recibidos.')
            ->line('Haga click en el botón para establecer su primera contraseña.')
            ->action('Establecer Contraseña', url('/password/reset/' . $this->token))
            ->line('También puede revisar la ayuda del sistema en el archivo adjunto a este correo.')
            ->line('Gracias por usar nuestra aplicación')
            ->attach(public_path().'/Sistema Proveedores - Manual de ingreso v1.pdf', [
                'mime' => 'application/pdf',
            ]);
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

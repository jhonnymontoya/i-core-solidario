<?php

namespace App\Listeners\Creditos;


use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\Creditos\SolicitudCreditoAprobado as MailSolicitudCreditoAprobado;
use App\Events\Creditos\SolicitudCreditoAprobado;
use Mail;

class EnviarCorreoSolicitudCreditoAprobado implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {}

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SolicitudCreditoAprobado $event)
    {
        $solicitudCredito = $event->solicitudCredito;
        $tercero = $solicitudCredito->tercero;

        $mail = null;
        $contactos = $tercero->contactos;
        if($contactos){
            foreach($contactos as $contacto) {
                if($contacto->es_preferido) {
                    $mail = $contacto->email ? $contacto->email : $mail;
                    if($mail)break;
                }
                $mail = $contacto->email;
            }
        }

        if($mail) {
            Mail::to($mail)->send(new MailSolicitudCreditoAprobado($solicitudCredito, $tercero));
        }
    }
}

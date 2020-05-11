<?php

namespace App\Listeners\Creditos;

use Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Creditos\SolicitudCreditoDesembolsado;
use App\Mail\Creditos\SolicitudCreditoDesembolsado as MailSolicitudCreditoDesembolsado;


class EnviarCorreoSolicitudCreditoDesembolsada implements ShouldQueue
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
    public function handle(SolicitudCreditoDesembolsado $event)
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
            Mail::to($mail)->send(new MailSolicitudCreditoDesembolsado($solicitudCredito, $tercero));
        }
    }
}

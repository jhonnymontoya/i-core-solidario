<?php

namespace App\Listeners\Creditos;

use Mail;
use App\Models\Socios\Socio;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Creditos\SolicitudCreditoDigitalEnviada;
use App\Mail\Creditos\SolicitudCreditoDigitalEnviadaConfirmacion;

class EnviaCorreoSolicitudCreditoDigitalEnviada implements ShouldQueue
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
    public function handle(SolicitudCreditoDigitalEnviada $event)
    {
        $solicitudCredito = SolicitudCredito::find($event->solicitudCreditoId);
        $socio = Socio::find($event->socioId);
        $tercero = $socio->tercero;

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
            $mailable = new SolicitudCreditoDigitalEnviadaConfirmacion(
                $solicitudCredito,
                $socio,
                $tercero
            );
            Mail::to($mail)->send($mailable);
        }
    }
}

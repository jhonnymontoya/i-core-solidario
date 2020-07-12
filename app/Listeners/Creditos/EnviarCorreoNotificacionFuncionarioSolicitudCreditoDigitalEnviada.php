<?php

namespace App\Listeners\Creditos;

use Mail;
use App\Models\Socios\Socio;
use App\Models\Notificaciones\Funcion;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\Creditos\SolicitudCreditoDigitalEnviada;
use App\Mail\Creditos\SolicitudCreditoDigitalEnviadaNotificarFuncionarios;

/*
    Enviar correo a funcionarios parametrizados en módulo notificaciones
    sobre nueva solicitud de crédito enviada por usuario desde
    canal digital (Sucursal web o Aplicación Movil)
*/
class EnviarCorreoNotificacionFuncionarioSolicitudCreditoDigitalEnviada implements ShouldQueue
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

        $funcion = Funcion::moduloId(7)
            ->funcion('SocioRadicaSolicitudCredito')
            ->first();

        if($funcion == null) {
            return;
        }

        $mails = $funcion->getCorreos($tercero->entidad_id);

        if(count($mails) > 0) {
            $mailable = new SolicitudCreditoDigitalEnviadaNotificarFuncionarios(
                $solicitudCredito,
                $socio,
                $tercero
            );
            Mail::to($mails)->send($mailable);
        }
    }
}

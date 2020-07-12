<?php

namespace App\Mail\Creditos;

use Illuminate\Support\Str;
use App\Models\Socios\Socio;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\General\Tercero;
use Illuminate\Queue\SerializesModels;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Contracts\Queue\ShouldQueue;

class SolicitudCreditoDigitalEnviadaNotificarFuncionarios extends Mailable
{
    use Queueable, SerializesModels;

    private $solicitudCredito;
    private $socio;
    private $tercero;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SolicitudCredito $solicitudCredito, Socio $socio, Tercero $tercero)
    {
        $this->solicitudCredito = $solicitudCredito;
        $this->socio = $socio;
        $this->tercero = $tercero;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $entidad = $this->tercero->entidad;
        $sigla = $entidad->terceroEntidad->sigla;
        $data = (object)[
            "fecha" => $this->getFecha($this->solicitudCredito->fecha_solicitud),
            "solicitante" => $this->tercero->nombre_completo,
            "numeroSolicitud" => $this->solicitudCredito->id,
            "modalidad" => $this->solicitudCredito->modalidadCredito->nombre,
            "fechaSolicitud" => $this->solicitudCredito->fecha_solicitud,
            "empresa" => $this->socio->pagaduria->terceroEmpresa->nombre,
            "valor" => sprintf("$%s", number_format($this->solicitudCredito->valor_solicitud, 0)),
            "tasa" => sprintf("%s%%", number_format($this->solicitudCredito->tasa, 3)),
            "plazo" => $this->solicitudCredito->plazo,
            "periodicidad" => Str::title($this->solicitudCredito->periodicidad),
            "sigla" => $sigla
        ];

        $subject = "Notificación envío solicitud de crédito";

        $subcopy = "Este es un mensaje automático, favor abstenerse de contestarlo";

        $this->withSwiftMessage(function($message) {
            $message->getHeaders()
                ->addTextHeader('X-ICore-Tag', 'NotificacionEnvioCreditoSocioCanalDigital');
        });

        $from = config('mail.from.address', 'noresponder@i-core.co');
        return $this->from($from, $sigla)
            ->subject($subject)
            ->markdown('emails.creditos.envioNotificacionCreditoCanalDigital')
            ->withData($data)
            ->withSubcopy($subcopy);
    }

    private function getFecha($fechaAprobacion){
        $fecha = "%s %s de %s del %s";
        $fecha = sprintf(
            $fecha,
            $this->getDia($fechaAprobacion->format("N")),
            $fechaAprobacion->format("j"),
            $this->getMes($fechaAprobacion->format("n")),
            $fechaAprobacion->format("Y")
        );
        return $fecha;
    }

    private function getMes($mes){
        switch ($mes) {
            case 1: return 'Enero';
            case 2: return 'Febrero';
            case 3: return 'Marzo';
            case 4: return 'Abril';
            case 5: return 'Mayo';
            case 6: return 'Junio';
            case 7: return 'Julio';
            case 8: return 'Agosto';
            case 9: return 'Septiembre';
            case 10: return 'Octubre';
            case 11: return 'Novimebre';
            case 12: return 'Diciembre';

            default: return $mes;
        }
    }

    private function getDia($dia){
        switch ($dia) {
            case 1: return 'Lunes';
            case 2: return 'Martes';
            case 3: return 'Miércoles';
            case 4: return 'Jueves';
            case 5: return 'Viernes';
            case 6: return 'Sábado';
            case 7: return 'Domingo';

            default: return $dia;
        }
    }
}

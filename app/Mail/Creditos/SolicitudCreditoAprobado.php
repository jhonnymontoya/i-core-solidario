<?php

namespace App\Mail\Creditos;

use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\General\Tercero;
use Illuminate\Queue\SerializesModels;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Contracts\Queue\ShouldQueue;

class SolicitudCreditoAprobado extends Mailable
{
    use Queueable, SerializesModels;

    private $solicitudCredito;
    private $tercero;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(SolicitudCredito $solicitudCredito, Tercero $tercero)
    {
        $this->solicitudCredito = $solicitudCredito;
        $this->tercero = $tercero;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "Señor" . (optional(optional($this->tercero)->sexo)->codigo == 2 ? "a " : " ") . $this->tercero->nombre;
        $entidad = $this->tercero->entidad;
        $sigla = $entidad->terceroEntidad->sigla;
        $data = (object)[
            "fecha" => $this->getFecha($this->solicitudCredito->fecha_aprobacion),
            "titulo" => $titulo,
            "entidad" => $entidad->terceroEntidad->nombre,
            "sigla" => $sigla,
            "valor" => sprintf("$%s", number_format($this->solicitudCredito->valor_credito, 0)),
            "plazo" => $this->getPlazo($this->solicitudCredito->plazo, $this->solicitudCredito->periodicidad),
            "modalidad" => $this->solicitudCredito->modalidadCredito->nombre
        ];

        $subject = "Acerca de su solicitud de crédito";

        return $this->from(env('MAIL_FROM_ADDRESS', 'noresponder@i-core.co'), $sigla)
            ->subject($subject)
            ->markdown('emails.creditos.aprobado')
            ->withData($data);
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

    private function getPlazo($plazo, $periodicidad) {
        $formato = "%s %s %s";
        return sprintf(
            $formato,
            $plazo,
            $plazo == 1 ? 'cuota' : 'cuotas',
            $this->getPeriodicidad($periodicidad, $plazo == 1)
        );
    }

    private function getPeriodicidad($periodicidad, $singular = true){
        //ANUAL, SEMESTRAL, CUATRIMESTRAL, TRIMESTRAL, BIMESTRAL, MENSUAL, QUINCENAL, CATORCENAL, DECADAL, SEMANAL
        $periodicidad = Str::title($periodicidad);
        if($singular == false) {
            $periodicidad .= 'es';
        }
        return $periodicidad;
    }
}

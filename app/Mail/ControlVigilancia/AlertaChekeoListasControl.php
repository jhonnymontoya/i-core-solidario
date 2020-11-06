<?php

namespace App\Mail\ControlVigilancia;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AlertaChekeoListasControl extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $oficialCumplimiento;
    private $periodicidad;
    private $archivo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($oficialCumplimiento, $periodicidad, $archivo)
    {
        $this->oficialCumplimiento = $oficialCumplimiento;
        $this->periodicidad = $periodicidad;
        $this->archivo = $archivo;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "Estimada/a " . $this->oficialCumplimiento->nombre;
        $entidad = $this->oficialCumplimiento->entidad;
        $sigla = $entidad->terceroEntidad->sigla;
        $subject = "Reporte log listas de control %s";
        $subject = sprintf(
            $subject,
            mb_convert_case($this->periodicidad, MB_CASE_TITLE, "UTF-8")
        );

        $nombreArchivo = "LogListasControl%s_%s.csv";
        $nombreArchivo = sprintf(
            $nombreArchivo,
            mb_convert_case($this->periodicidad, MB_CASE_TITLE, "UTF-8"),
            date("Y-m-d")
        );

        $this->withSwiftMessage(function($message) {
            $message->getHeaders()
                ->addTextHeader('X-ICore-Tag', 'ReporteLogListasControl');
        });

        $from = config('mail.from.address', 'noresponder@i-core.co');
        return $this->from($from, $sigla)
            ->subject($subject)
            ->markdown('emails.controlVigilancia.alertaChekeoListasControl')
            ->withSigla($sigla)
            ->withTitulo($titulo)
            ->attach($this->archivo, [
                'as' => $nombreArchivo,
                'mime' => 'text/csv'
            ]);
    }
}

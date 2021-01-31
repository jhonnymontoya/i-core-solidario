<?php

namespace App\Mail\Consulta;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DocumenacionCertificadoTributario extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    private $tercero;
    private $certificado;
    private $anio;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($tercero, $certificado, $anio)
    {
        $this->tercero = $tercero;
        $this->certificado = $certificado;
        $this->anio = $anio;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $titulo = "Estimado/a " . $this->tercero->nombre_corto;
        $entidad = $this->tercero->entidad;
        $sigla = $entidad->terceroEntidad->sigla;
        $subject = "Certificado tributario " . $this->anio;

        $nombreArchivo = "Certificadoributario%s.pdf";
        $nombreArchivo = sprintf($nombreArchivo, $this->anio);

        $this->withSwiftMessage(function($message) {
            $message->getHeaders()
                ->addTextHeader('X-ICore-Tag', 'DocumentacionCertificadoTributario');
        });

        $from = config('mail.from.address', 'noresponder@i-core.co');
        return $this->from($from, $sigla)
            ->subject($subject)
            ->markdown('emails.consulta.documentacionCertificadoTributario')
            ->withSigla($sigla)
            ->withTitulo($titulo)
            ->withAnio($this->anio)
            ->attach($this->certificado, [
                'as' => $nombreArchivo,
                'mime' => 'application/pdf'
            ]);
    }
}

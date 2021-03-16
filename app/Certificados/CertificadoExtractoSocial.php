<?php

namespace App\Certificados;

use PDF;
use Auth;
use Exception;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;


class CertificadoExtractoSocial
{

    private $socio = null;
    private $anio = null;
    private $configuracion = null;
    private $vista = "pdf.socios.certificadoExtractoSocial";
    private $path = null;

    /**
     * Constructor
     */
    public function __construct($socio, $anio, $configuracion) {
        $this->socio = $socio;
        $this->anio = $anio;
        $this->$configuracion = $configuracion;
        $entidad = $this->getEntidad();
        $path = $this->getPath();

        $dataAhorros = DB::select("EXEC reportes.sp_certificado_extracto_social_ahorros ?, ?", [$socio->id, $anio]);
        $dataCreditos = DB::select("EXEC reportes.sp_certificado_extracto_social_creditos ?, ?", [$socio->id, $anio]);
        $dataConvenios = DB::select("EXEC reportes.sp_certificado_extracto_social_convenios ?, ?", [$socio->id, $anio]);
        $pdf = PDF::loadView($this->vista, compact("socio", "anio", "entidad", "dataAhorros", "dataCreditos", "dataConvenios", "configuracion"))
            ->setPaper('letter', 'portait')
            ->setWarnings(false);
        $pdf->save($path);
        $this->path = $path;
    }

    /**
     * Retorna la entidad actual
     * @return type
     */
    protected function getEntidad() {
        $entidad = $this->socio->tercero->entidad;
        return $entidad;
    }

    public function getRuta() {
        return $this->path;
    }

    public function getPath() {
        $tercero = $this->socio->tercero;
        $nombre = sprintf("%s %s", Str::random(10), $tercero->nombre_completo);
        $nombre = Str::slug($nombre, "_") . ".pdf";
        $path = "app%spdf%s%s";
        $path = sprintf($path, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $nombre);
        $path = storage_path($path);
        return $path;
    }
}

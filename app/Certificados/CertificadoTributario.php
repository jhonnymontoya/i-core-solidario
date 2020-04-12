<?php

namespace App\Certificados;

use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use PDF;
use Auth;
use Illuminate\Support\Str;

class CertificadoTributario
{

	private $socio = null;
	private $anio = null;
	private $vista = "pdf.socios.certificadoTributario";
	private $path = null;

	/**
	 * Constructor
	 */
	public function __construct($socio, $anio) {
		$this->socio = $socio;
		$this->anio = $anio;
		$entidad = $this->getEntidad();
		$path = $this->getPath();

		$data = DB::select("EXEC socios.sp_certificado_tributario ?, ?", [$socio->id, $anio]);
		$pdf = PDF::loadView($this->vista, compact("socio", "anio", "entidad", "data"))
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
		return Auth::getSession()->get('entidad');
	}

	public function getRuta() {
		return $this->path;
	}

	public function getPath() {
		$tercero = $this->socio->tercero;
		$nombre = sprintf("%s %s", Str::random(10), $tercero->nombre_completo);
		$nombre = str_slug($nombre, "_") . ".pdf";
		$path = "app%spdf%s%s";
		$path = sprintf($path, DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR, $nombre);
		$path = storage_path($path);
		return $path;
	}
}
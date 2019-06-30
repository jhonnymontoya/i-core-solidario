<?php

namespace App\CierreCartera;

use App\Models\General\Entidad;
use App\Models\Socios\Socio as SocioDeEntidad;
use Carbon\Carbon;
use Exception;

class Socios
{

	private $entidad = null;
	private $fechaComparacion = null;

	/**
	 * Constructor
	 */
	public function __construct($entidad, $fechaComparacion) {
		$this->entidad = $entidad;
		$this->fechaComparacion = $fechaComparacion;

		if(!($this->entidad instanceof Entidad)) {
			throw new Exception("Se espera una entidad válida", 1);			
		}

		if(!($this->fechaComparacion instanceof Carbon)) {
			throw new Exception("Se espera una fecha válida", 1);			
		}
	}

	/**
	 * Obtiene array con socios en proceso
	 * Se obtienen los socios en estado proceso con alertas tipo A,B,C para la fecha de comparación
	 * @return array
	 */
	public function obtenerSociosEnProceso() {
		$sociosProceso = ['A' => [], 'B' => [], 'C' => []];
		$sociosEnProceso = $this->entidad->terceros()->with('socio')->whereHas('socio', function($query){
			$query->where('estado', 'PROCESO');
		})->get();
		foreach ($sociosEnProceso as $socio) {
			if(!empty($socio->created_at)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->created_at, false);
				if($diasDiferencia > 60) {
					array_push($sociosProceso['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosProceso['B'], $socio);    
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosProceso['C'], $socio);
				}
			}
			else {
				array_push($sociosProceso['A'], $socio);
			}
		}
		return $sociosProceso;
	}

	/**
	 * Obtiene array con socios en retiro
	 * Se obtienen los socios en estado retiro con alertas tipo A,B,C para la fecha de comparación
	 * @return array
	 */
	public function obtenerSociosEnRetiro() {
		$sociosRetiro = ['A' => [], 'B' => [], 'C' => []];
		$sociosEnRetiro = SocioDeEntidad::entidad($this->entidad->id)
			->whereEstado('RETIRO')
			->with('tercero')
			->get();
		foreach ($sociosEnRetiro as $socio) {
			if(!empty($socio->fecha_retiro)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->fecha_retiro, false);
				if($diasDiferencia > 60) {
					array_push($sociosRetiro['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosRetiro['B'], $socio); 
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosRetiro['C'], $socio);
				}
			}
			else {
				array_push($sociosRetiro['A'], $socio);
			}
		}
		return $sociosRetiro;
	}

	/**
	 * Obtiene array con socios activos y sin cuotas obligatorias
	 * Se obtienen los socios en estado activo pero sin cuotas obligatorias con alertas tipo A,B,C
	 * para la fecha de comparación
	 * @return array
	 */
	public function obtenerSociosSinCuotasObligatorias() {
		$sociosCuotasObligatorias = ['A' => [], 'B' => [], 'C' => []];
		$sociosSinCuotasObligatorias = SocioDeEntidad::entidad($this->entidad->id)
			->whereEstado('ACTIVO')
			->doesntHave('cuotasObligatorias')
			->with('tercero')
			->get();
		foreach ($sociosSinCuotasObligatorias as $socio) {
			if(!empty($socio->fecha_afiliacion)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->fecha_afiliacion, false);
				if($diasDiferencia > 60) {
					array_push($sociosCuotasObligatorias['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosCuotasObligatorias['B'], $socio); 
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosCuotasObligatorias['C'], $socio);
				}
			}
			else {
				array_push($sociosCuotasObligatorias['A'], $socio);
			}
		}
		return $sociosCuotasObligatorias;
	}

	/**
	 * Obtiene array con socios activos y contrato vencido
	 * Se obtienen los socios con contratos vencidos
	 * @return array
	 */
	public function obtenerSociosConContratosVencido() {
		$sociosContratoVencido = ['A' => [], 'B' => [], 'C' => []];
		$sociosConContratoVencido = SocioDeEntidad::entidad($this->entidad->id)
			->whereEstado('ACTIVO')
			->whereNotNull('fecha_fin_contrato')
			->with('tercero')
			->get();
		foreach ($sociosConContratoVencido as $socio) {
			if(!empty($socio->fecha_fin_contrato)) {
				$diasDiferencia = -$this->fechaComparacion->diffInDays($socio->fecha_fin_contrato, false);
				if($diasDiferencia > 60) {
					array_push($sociosContratoVencido['A'], $socio);
				}
				elseif($diasDiferencia > 30 && $diasDiferencia <= 60) {
					array_push($sociosContratoVencido['B'], $socio);    
				}
				elseif($diasDiferencia > 0 && $diasDiferencia <= 30) {
					array_push($sociosContratoVencido['C'], $socio);
				}
			}
			else {
				array_push($sociosContratoVencido['A'], $socio);
			}
		}
		return $sociosContratoVencido;
	}

	/**
	 * Valida que en el caso no existan alertas tipo A
	 * @param type $caso 
	 * @return boolean
	 */
	private function tieneAlertasTipoA($caso) {
		return count($caso['A']) ? true : false;
	}

	/**
	 * Valida si el proceso se puede cerrar de acuerdo a la relas
	 * @return boolean
	 */
	public function validoParaCierre() {

		//Se valida que los casos de socios no tengan alertas tipo A
		if($this->tieneAlertasTipoA($this->obtenerSociosEnProceso()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerSociosEnRetiro()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerSociosSinCuotasObligatorias()))
			return false;

		if($this->tieneAlertasTipoA($this->obtenerSociosConContratosVencido()))
			return false;

		return true;
	}
}
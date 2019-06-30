<?php

namespace App\Traits;

use App\Models\General\ControlCierreModulo;
use App\Models\Sistema\LogEvento;
use Auth;
use Carbon\Carbon;
use Log;
use Request;

/**
 * Métodos de uso general
 */
trait FonadminTrait
{

	/**
	 * Retorna la entidad actual
	 * @return type
	 */
	protected function getEntidad() {
		return Auth::getSession()->get('entidad');
	}

	/**
	 * Valida que el objeto pertenezca a la entidad
	 * @param type $obj 
	 * @return type
	 */
	public function objEntidad($obj, $mensaje = 'No esta autorizado') {
		if($obj->entidad_id != $this->getEntidad()->id) {
			return abort(401, $mensaje);
		}
	}

	/**
	 * Retorna el usuario actual
	 * @return type
	 */
	public function getUser() {
		return Auth::user();
	}

	/**
	 * Valida si se encuentra cerrado para la fecha dada el módulo
	 * @param integer $modulo 
	 * @param Carbon|string $fecha 
	 * @return booblean
	 */
	public function moduloCerrado($modulo, $fecha = null) {
		if(empty($modulo))return false;
		try{
			$fecha = empty($fecha) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fecha)->startOfDay();
		}
		catch(\InvalidArgumentException $e){
			return false;
		}
		$cierre = ControlCierreModulo::entidadId()->whereModuloId($modulo)->orderBy('fecha_cierre', 'desc')->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($fecha)) {
				return true;
			}
		}
		return false;
	}

	protected function log($descripcion = null, $tipoEvento = 'INGRESAR') {
		try{
			$metadata = $this->getMetaDataLog();

			$evento = new LogEvento;
			$evento->fill($metadata);
			$evento->tipo_evento = $tipoEvento;
			$evento->descripcion = $descripcion;
			$evento->save();
		}
		catch(Exception $e){
			Log::error($e->getMessage());
		}
	}

	protected function logActividad($descripcion, $parametros) {
		if(count($parametros->all())) {
			$tipoEvento = 'CONSULTAR';
			$descripcion .= ' con los siguientes parámetros %s';
			$descripcion = sprintf($descripcion, json_encode($parametros->all()));
		}
		else {
			$tipoEvento = 'INGRESAR';
		}
		$this->log($descripcion, $tipoEvento);
	}

	protected function getMetaDataLog() {
		$metadata = array(
			"usuario_id" => null,
			"usuario" => null,
			"entidad_id" => null,
			"direccion" => null,
			"user_agent" => null,
			"verbo" => null,
			"ruta" => null,
			"modelo" => null
		);
		try{
			$metadata["usuario_id"] = Auth::id();
			$metadata["usuario"] = optional(Auth::user())->usuario;
			$metadata["entidad_id"] = Auth::getSession()->has('entidad') ? Auth::getSession()->get('entidad')->id : null;
			$metadata["direccion"] = Request::ip();
			$metadata["user_agent"] = Request::header('User-Agent');
			$metadata["verbo"] = Request::method();
			$metadata["ruta"] = optional(Request::route())->uri;
			$metadata["modelo"] = get_class();
		}
		catch(Exception $e){
			Log::error($e->getMessage());
		}
		return $metadata;
	}
}

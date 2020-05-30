<?php

namespace App\Traits;

use App\Models\Sistema\LogEvento;
use Auth;
use Request;
use Log;

/**
 * Métodos para Log de actividades en los Models
 */
trait ICoreModelTrait
{

	public static function boot() {
		parent::boot();

		static::created(function($objeto){
			$objeto->LogCreate($objeto);
		});

		static::updating(function($objeto) {
			$objeto->LogUpdate($objeto);
		});

		static::deleted(function($objeto) {
			$objeto->LogDelete($objeto);
		});
	}

	protected function LogCreate($objeto) {
		try{
			$metadata = $objeto->getMetaDataLog();
			$descripcion = "Se ha creado el objeto con id: " . $objeto->id;
			$cambios = $this->getCambios();

			$evento = new LogEvento;
			$evento->fill($metadata);
			$evento->tipo_evento = LogEvento::CREAR;
			$evento->descripcion = $descripcion;
			$evento->modelo_despues = $cambios["despues"];
			$evento->save();
		}
		catch(Exception $e){
			Log::error($e->getMessage());
		}
	}

	protected function LogUpdate($objeto) {
		try{
			$metadata = $objeto->getMetaDataLog();
			$descripcion = "Se ha actualizado el objeto con id: " . $objeto->id;
			$cambios = $this->getCambios();

			$evento = new LogEvento;
			$evento->fill($metadata);
			$evento->tipo_evento = LogEvento::ACTUALIZAR;
			$evento->descripcion = $descripcion;
			$evento->modelo_antes = $cambios["antes"];
			$evento->modelo_despues = $cambios["despues"];
			$evento->save();
		}
		catch(Exception $e){
			Log::error($e->getMessage());
		}
	}

	protected function LogDelete($objeto) {
		try{
			$metadata = $objeto->getMetaDataLog();
			$descripcion = "Se ha eliminado el objeto con id: " . $objeto->id;
			$cambios = $this->getCambios();

			$evento = new LogEvento;
			$evento->fill($metadata);
			$evento->tipo_evento = LogEvento::ELIMINAR;
			$evento->descripcion = $descripcion;
			$evento->modelo_antes = $cambios["antes"];
			$evento->modelo_despues = $cambios["despues"];
			$evento->save();
		}
		catch(Exception $e){
			Log::error($e->getMessage());
		}
	}

	protected function getCambios() {
		try {
			$cambios = array("antes" => null, "despues" => null);
			$despues = $this->getDirty();
			$fresh = $this->fresh();
			if($fresh) {
				$cambios["antes"] = json_encode(array_intersect_key($this->fresh()->toArray(), $despues));
			}
			$cambios["despues"] = json_encode($despues);
		}
		catch(Exception $e) {
		}
		return $cambios;
	}

}

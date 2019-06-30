<?php

namespace App\Listeners\General;

use App\Events\General\ProcesoCerrado;
use App\Models\General\ControlPeriodoCierre;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class IniciarNuevoProceso
{
	use FonadminTrait;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  object  $event
	 * @return void
	 */
	public function handle(ProcesoCerrado $event) {
		$proceso = $event->proceso;
		$entidad = $this->getEntidad();
		if(is_null($proceso)) {
			//No se ha pasado un proceso id, posiblemente porque
			//no existen al crear una entidad
			$control = ControlPeriodoCierre::entidadId()
				->orderBy("id", "desc")
				->first();
			if(empty($control)) {
				//Se crea el primer proceso de la entidad
				$proceso = new ControlPeriodoCierre;

				$proceso->entidad_id = $entidad->id;
				$proceso->mes = $entidad->fecha_inicio_contabilidad->month;
				$proceso->anio = $entidad->fecha_inicio_contabilidad->year;
				$proceso->fecha_apertura = Carbon::now();
				$proceso->save();
				return;
			}
			else {
				$proceso = $control->id;
			}
		}

		$control = ControlPeriodoCierre::entidadId()
			->where("id", ">", $proceso)
			->first();

		if(!empty($control)) return;

		$control = ControlPeriodoCierre::find($proceso);

		$siguienteFecha = Carbon::createFromDate($control->anio, $control->mes, 1)->endOfMonth()->startOfDay();
		$siguienteFecha->addDay();

		$proceso = new ControlPeriodoCierre;

		$proceso->entidad_id = $entidad->id;
		$proceso->mes = $siguienteFecha->month;
		$proceso->anio = $siguienteFecha->year;
		$proceso->fecha_apertura = Carbon::now();
		$proceso->save();
	}
}

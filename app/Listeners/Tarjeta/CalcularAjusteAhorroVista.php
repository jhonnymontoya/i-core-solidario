<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\CalcularAjusteAhorrosVista as EventoCalcularAjusteAhorrosVista;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Socios\Socio;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Tarjeta\Tarjetahabiente;
use DB;
use Log;

class CalcularAjusteAhorroVista implements ShouldQueue
{
	private $id;
	private $esCredito;
	private $socio;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {
	}

	/**
	 * Handle the event.
	 *
	 * @param  object  $event
	 * @return void
	 */
	public function handle(EventoCalcularAjusteAhorrosVista $event) {
		$this->id = $event->id;
		$this->esCredito = $event->esCredito;
		$this->obtenerSocio();
		if(is_null($this->socio))return;
		$tarjetahabiente = $this->tieneTarjetaVista();
		if(!$tarjetahabiente)return;

		$tercero = $this->socio->tercero;
		$disponibleVista = $tercero->cupoDisponibleVista('31/12/3000');

		$tarjetaHabiente = Tarjetahabiente::with([
				'producto',
				'tarjeta',
				'tercero',
				'cuentaAhorro',
				'solicitudCredito',
				'tercero.sexo',
				'tercero.tipoIdentificacion',
				'tercero.contactos',
				'tercero.contactos.ciudad.departamento.pais',
				'tercero.ciudadNacimiento.departamento.pais',
			])->whereId($tarjetahabiente->id)->first();
		$tarjetaHabiente->disponible_vista = $disponibleVista;
		$convenio = $tarjetaHabiente->producto->convenio;
		$respuesta = DB::connection('sqlsrvRedCoopcentral')
		->select(
			"EXEC sp_crear_transaccion ?, ?, ?",
			[$convenio, 'ACTUALIZARDISPONIBLEVISTA', $tarjetaHabiente->toJson()]
		);
		if (!$respuesta || $respuesta[0]->ERROR == '1') {
			Log::error(
				'Error actualizando el disponible de vista: ' .
					$this->socio->id . '; ' . $respuesta[0]->DETALLE
			);
			return false;
		}
	}

	private function obtenerSocio() {
		if($this->esCredito) {
			$credito = SolicitudCredito::find($this->id);
			if(is_null($credito))return null;
			$tercero = $credito->tercero;
			$this->socio = optional($tercero)->socio;
		}
		else{
			$this->socio = Socio::find($this->id);
		}
	}

	private function tieneTarjetaVista() {
		$tercero = $this->socio->tercero;
		$th = $tercero->tarjetahabientes;
		foreach ($th as $item) {
			$producto = $item->producto;
			if($producto->vista)return $item;
		}
		return false;
	}
}

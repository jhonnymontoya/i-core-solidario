<?php

namespace App\Events\Tarjeta;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TarjetaHabienteCreado
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $tarjetaHabienteId;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($tarjetaHabienteId) {
		$this->tarjetaHabienteId = $tarjetaHabienteId;
	}
}

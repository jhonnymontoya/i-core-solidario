<?php

namespace App\Events\Tarjeta;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CalcularAjusteAhorrosVista
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $id;
	public $esCredito;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($id, $esCredito) {
		$this->id = $id;
		$this->esCredito = $esCredito;
	}
}

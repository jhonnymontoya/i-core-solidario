<?php

namespace App\Events\General;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ProcesoCerrado
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $proceso;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct($proceso = null) {
		$this->proceso = $proceso;
	}
}

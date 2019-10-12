<?php

namespace App\Events\Socios;

use App\Models\Socios\Socio;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Auth;

class SocioAfiliado
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $socio;
	public $password;
	public $usuario;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Socio $socio, $password) {
		$this->socio = $socio;
		$this->password = $password;
		$this->usuario = Auth::user();
	}
}

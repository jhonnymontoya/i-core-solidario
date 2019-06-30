<?php

namespace App\Events\Tarjeta;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class TarjetaHabienteCupoModificado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $tarjetaHabienteId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($tarjetaHabienteId)
    {
        $this->tarjetaHabienteId = $tarjetaHabienteId;
    }
}

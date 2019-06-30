<?php

namespace App\Events\Tarjeta;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

/**
 * Se dispara cuando se crea un ajuste al saldo de
 * la obligación de crédito
*/
class SolicitudCreditoAjusteCreado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitudCreditoId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($solicitudCreditoId)
    {
        $this->solicitudCreditoId = $solicitudCreditoId;
    }
}

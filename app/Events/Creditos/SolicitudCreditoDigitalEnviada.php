<?php

namespace App\Events\Creditos;

use App\Models\Socios\Socio;
use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

/**
Se dispara cuando desde la consulta web o app movil envian una
solicitud de crédito
*/
class SolicitudCreditoDigitalEnviada
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitudCreditoId;
    public $socioId;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($solicitudCreditoId, $socioId)
    {
        $this->solicitudCreditoId = $solicitudCreditoId;
        $this->socioId = $socioId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    /*public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }*/
}

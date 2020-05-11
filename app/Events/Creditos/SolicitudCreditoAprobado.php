<?php

namespace App\Events\Creditos;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use App\Models\Creditos\SolicitudCredito;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class SolicitudCreditoAprobado
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $solicitudCredito;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SolicitudCredito $solicitudCredito)
    {
        $this->solicitudCredito = $solicitudCredito;
    }

}

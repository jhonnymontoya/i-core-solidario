<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\TarjetaHabienteCupoModificado;
use App\Models\Tarjeta\Tarjetahabiente;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class ModificarCuentaCorrienteEnRed implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  TarjetaHabienteCupoModificado  $event
     * @return void
     */
    public function handle(TarjetaHabienteCupoModificado $event)
    {
        try {
            $tarjetaHabiente = Tarjetahabiente::with([
                'producto',
                'solicitudCredito',
                'tercero',
                'tercero.tipoIdentificacion',
            ])->whereId($event->tarjetaHabienteId)->first();

            if (is_null($tarjetaHabiente)) {
                //No se pudo encontrar el tarjeta habiente
                Log::error(
                    'Error modificando cuenta corriente, tarjeta habiente' .
                    'porque este no se encontro id: ' .
                    $event->tarjetaHabienteId
                );
                return false;
            }

            //Cupo disponible
            $cupoUtilizado = $tarjetaHabiente
                ->solicitudCredito
                ->saldoObligacion("31/12/3000");
            $cupoDisponible = $tarjetaHabiente->cupo - $cupoUtilizado;
            $tarjetaHabiente->cupoDisponible = $cupoDisponible;

            $convenio = $tarjetaHabiente->producto->convenio;
            $respuesta = DB::connection('sqlsrvRedCoopcentral')
            ->select(
                "EXEC sp_crear_transaccion ?, ?, ?",
                [
                    $convenio,
                    'MODIFICARCUENTACORRIENTE',
                    $tarjetaHabiente->toJson()
                ]
            );
            if (!$respuesta || $respuesta[0]->ERROR == '1') {
                Log::error(
                    'Error modificando la cuenta corriente del tarjeta ' .
                    'habiente id: ' .
                    $event->tarjetaHabienteId .
                    '; ' . $respuesta[0]->DETALLE
                );
                return false;
            }
        } catch (Exception $e) {
            Log::error(
                'Error modificando cuenta corriente tarjeta habiente id: ' .
                $event->tarjetaHabienteId . '; ' . $e->getMessage()
            );
        }
    }
}

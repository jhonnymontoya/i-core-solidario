<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\TarjetaHabienteCreado;
use App\Models\Tarjeta\Tarjetahabiente;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class CrearTarjetaHabienteEnRed implements ShouldQueue
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
     * @param  TarjetaHabienteCreado  $event
     * @return void
     */
    public function handle(TarjetaHabienteCreado $event)
    {
        try {
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
            ])->whereId($event->tarjetaHabienteId)->first();

            $tarjetaHabiente->disponible_vista = $tarjetaHabiente->tercero->cupoDisponibleVista('31/12/3000');

            if (is_null($tarjetaHabiente)) {
                //No se pudo encontrar el tarjeta habiente
                Log::error(
                    'Error activando el tarjeta habiente porque este ' .
                    'no se encontro id: ' . $event->tarjetaHabienteId
                );
                return false;
            }
            $convenio = $tarjetaHabiente->producto->convenio;
            $respuesta = DB::connection('sqlsrvRedCoopcentral')
            ->select(
                "EXEC sp_crear_transaccion ?, ?, ?",
                [$convenio, 'CREARTARJETAHABIENTE', $tarjetaHabiente->toJson()]
            );
            if (!$respuesta || $respuesta[0]->ERROR == '1') {
                Log::error(
                    'Error activando el tarjeta habiente id: ' .
                        $event->tarjetaHabienteId .
                        '; ' . $respuesta[0]->DETALLE
                );
                return false;
            }
        } catch (Exception $e) {
            Log::error(
                'Error activando el tarjeta habiente id: ' .
                $event->tarjetaHabienteId . '; ' . $e->getMessage()
            );
        }
    }
}

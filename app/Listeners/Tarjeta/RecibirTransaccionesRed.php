<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\ProcesarTransaccionesProvenientesRed;
use App\Events\Tarjeta\RecibirTransacciones;
use App\Models\Tarjeta\LogMovimientoTransaccionRecibido;
use DB;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;

class RecibirTransaccionesRed implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(RecibirTransacciones $event)
    {
        try {
            DB::beginTransaction();
            //Se recuperan los datos de transacciones llegadas
            $transacciones = DB::connection('sqlsrvEcgtssyn')
                    ->table('trninp')
                    ->get();

            //Si hay transacciones, estas se eliminan para no ser
            //reprocesadas
            if ($transacciones->count()) {
                DB::connection('sqlsrvEcgtssyn')->table('trninp')->delete();
            }

            //Se almacenan las transacciones
            foreach ($transacciones as $transaccion) {
                //Se convierte a collect para cuando se convierte a JSON
                $transaccion = collect($transaccion);
                $log = LogMovimientoTransaccionRecibido::create([
                    "data" => $transaccion->toJson(),
                ]);
            }

            //Aqui se dispara el evento de proceso de transacción
            event(new ProcesarTransaccionesProvenientesRed());

            DB::commit();
        } catch(Exception $e) {
            DB::rollBack();
            Log::error(
                'Error al recibir las transacciones en red: ' . $e->getMessage()
            );
        }
    }
}

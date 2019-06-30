<?php

namespace App\Listeners\Tarjeta;

use App\Events\Tarjeta\SolicitudCreditoAjusteCreado;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Tarjeta\LogMovimientoTransaccionEnviado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Log;
use DB;

class ActualizarCupoTarjetaEnRed implements ShouldQueue
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
     * @param  SolicitudCreditoAjusteCreado  $event
     * @return void
     */
    public function handle(SolicitudCreditoAjusteCreado $event)
    {
        try {
            $solicitudCredito = SolicitudCredito::with([
                    'tercero',
                    'tercero.tipoIdentificacion',
                    'tarjetahabientes',
                    'tarjetahabientes.producto'
                ])
                ->whereId($event->solicitudCreditoId)
                ->first();

            if (is_null($solicitudCredito)) {
                //No se pudo encontrar la obligación financiera
                Log::error(
                    'Error actualizando el cupo del crédito id ' .
                    $event->solicitudCreditoId
                );
                return false;
            }
            $movimientos = $solicitudCredito
                ->movimientosCapitalCredito()
                ->where("valor_movimiento", "<>", "0")
                ->whereOrigen("FONADMIN")
                ->whereDoesntHave('logMovimientosTransaccionesEnviados')
                ->get();

            foreach ($movimientos as $movimiento) {
                $seq = LogMovimientoTransaccionEnviado::obtenerSecuencia();
                $data = $this->obtenerData(
                    $solicitudCredito,
                    $movimiento,
                    $seq
                );
                $respuesta = DB::connection('sqlsrvEcgtssyn')
                    ->table('trnout')
                    ->insert($data);
                $log = LogMovimientoTransaccionEnviado::create([
                    "entidad_id" => $solicitudCredito->entidad->id,
                    "solicitud_credito_id" => $solicitudCredito->id,
                    "movimiento_capital_credito_id" => $movimiento->id,
                    "secuencia" => $seq,
                    "data" => json_encode($data),
                ]);
            }
        } catch (Exception $e) {
            Log::error(
                'Error actualizando el cupo del crédito id: ' .
                $event->solicitudCreditoId . '; ' . $e->getMessage()
            );
        }
    }

    public function obtenerData(
        $solicitudCredito,
        $movimientoCapialCredito,
        $secuencia
    )
    {
        $tercero = $solicitudCredito->tercero;
        $tarjetaHabiente = $solicitudCredito->tarjetahabientes->first();
        $producto = $tarjetaHabiente->producto;
        $valor = intval($movimientoCapialCredito->valor_movimiento);
        $data = array(
            'INDX' => $secuencia,
            'NTRY' => 0,
            'S001' => LogMovimientoTransaccionEnviado::obtenerFecha(),
            'S002' => LogMovimientoTransaccionEnviado::obtenerHora(),
            'S003' => $producto->convenio_entidad,
            'S004' => '0000',
            'S005' => '0000',
            'S007' => '0220',
            'S008' => $valor < 0 ? '22' : '02',
            'S009' => $secuencia,
            'S010' => '00',
            'S011' => $tercero->numero_identificacion,
            'S012' => $this->obtenerTipoIdentificacion(
                        $tercero->tipoIdentificacion->codigo
                    ),
            'S015' => '000000',
            'S018' => '04',
            'S01F' => '',
            'S01T' => '91',
            'S020' => $tarjetaHabiente->numero_cuenta_corriente,
            'S021' => '',
            'S025' => $producto->convenio_entidad,
            'S026' => '',
            'S027' => $producto->convenio_entidad,
            'S030' => '',
            'S031' => LogMovimientoTransaccionEnviado::obtenerFecha(),
            'S032' => abs($valor) . "00",
            'S033' => '0',
            'S034' => '0',
            'S035' => '0',
            'S036' => '0',
            'S037' => '0',
            'S03A' => '50',
            'S03B' => '',
            'S03X' => 'CAS',
            'S03K' => '',
            'GMF3' => '0',
            'S050' => '0',
        );
        return $data;
    }

    public function obtenerTipoIdentificacion($codigoIdentificacion)
    {
        $tipoIdentificacion = "0";
        switch ($codigoIdentificacion) {
            case 'CE':
                $tipoIdentificacion = "1";
                break;
            case 'TI':
                $tipoIdentificacion = "2";
                break;
            case 'NIT':
                $tipoIdentificacion = "9";
                break;            
            default:
                $tipoIdentificacion = "0";
                break;
        }
        return $tipoIdentificacion;
    }
}

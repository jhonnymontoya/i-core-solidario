<?php

namespace App\Api;

use Carbon\Carbon;
use App\Helpers\ConversionHelper;
use Illuminate\Support\Facades\DB;

class Ahorros
{

    public static function getAhorros($socio)
    {
        $fecha = Carbon::now()->startOfday();
        $sql = "select ahorros.fn_saldo_total_ahorros(?, ?) AS saldo, ahorros.fn_saldo_total_ahorros(?, ?) AS saldo_anterior;";
        $res = DB::select($sql, [$socio->id, $fecha, $socio->id, $fecha->copy()->addMonth(-1)]);
        $saldoAnterior = $res[0]->saldo_anterior;
        $saldo = $res[0]->saldo;
        $variacion = 0;
        try{
            $variacion = intval(($saldo * 100) / $saldoAnterior) - 100;
        }
        catch(\ErrorException $e) {}

        $res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$socio->id, $fecha]);
        $ahorros = collect($res);

        $cuotas = $ahorros->sum("cuota");
        $intereses = $ahorros->sum("intereses");

        $data = [
            "totalCuota" => number_format($cuotas, 0),
            "totalIntereses" => number_format($intereses, 0),
            "totalAhorros" => number_format($saldo, 0),
            "porcentajeIncremento" => $variacion,
            "ahorrosGenerales" => Ahorros::getAhorrosGenerales($ahorros),
            "ahorrosProgramados" => Ahorros::getAhorrosProgramados($ahorros),
            "SDATs" => Ahorros::getSDATs($socio, $fecha)
        ];
        return $data;
    }

    private static function getAhorrosGenerales($ahorros)
    {
        $ahorrosOblVol = $ahorros->where("tipo_ahorro", "<>", "PROGRAMADO");
        $data = [];
        foreach($ahorrosOblVol as $ahorro) {
            $cuotaMes = ConversionHelper::conversionValorPeriodicidad(
                $ahorro->cuota,
                $ahorro->periodicidad,
                'MENSUAL'
            );
            $dato = [
                "id" => $ahorro->modalidad_ahorro_id,
                "modalidad" => $ahorro->nombre,
                "cuota" => number_format($ahorro->cuota, 0),
                "periodicidad" => $ahorro->periodicidad,
                "cuotaMes" => number_format($cuotaMes, 0),
                "saldo" => number_format($ahorro->saldo, 0),
                "intereses" => number_format($ahorro->intereses, 2),
                "tasaEA" => number_format($ahorro->tasa, 2)
            ];
            array_push($data, $dato);
        }
        return $data;
    }

    private static function getAhorrosProgramados($ahorros)
    {
        $ahorrosPor = $ahorros->where("tipo_ahorro", "PROGRAMADO");
        $data = [];
        foreach($ahorrosPor as $ahorro) {
            $cuotaMes = ConversionHelper::conversionValorPeriodicidad(
                $ahorro->cuota,
                $ahorro->periodicidad,
                'MENSUAL'
            );
            try {
                $fecha = Carbon::createFromFormat('Y/m/d', $ahorro->vencimiento)
                    ->startOfDay();
            }
            catch(\InvalidArgumentException $e) {
                $fecha = Carbon::createFromFormat('Y/m/d', "1970/01/01")
                    ->startOfDay();
            }
            $dato = [
                "id" => $ahorro->modalidad_ahorro_id,
                "modalidad" => $ahorro->nombre,
                "cuota" => number_format($ahorro->cuota, 0),
                "periodicidad" => $ahorro->periodicidad,
                "cuotaMes" => number_format($cuotaMes, 0),
                "saldo" => number_format($ahorro->saldo, 0),
                "intereses" => number_format($ahorro->intereses, 2),
                "vencimiento" => $fecha->format("Y-m-d"),
                "tasaEA" => number_format($ahorro->tasa, 2)
            ];
            array_push($data, $dato);
        }
        return $data;
    }

    private static function getSDATs($socio, $fecha)
    {
        $data = [];
        foreach($socio->SDATs as $sdat) {
            if($sdat->estaActivo() == false) {
                continue;
            }

            $movimientos = $sdat
                ->movimientosSdat()
                ->where("fecha_movimiento", '<=', $fecha)
                ->get();
            $movimientos = $movimientos->sum("valor");
            if($movimientos == 0) {
                continue;
            }

            $rendimientos = $sdat
                ->rendimientosSdat()
                ->where("fecha_movimiento", '<=', $fecha)
                ->get();
            $rendimientos = $rendimientos->sum("valor");

            $dato = [
                "numeroDeposito" => $sdat->id,
                "tipo" => $sdat->tipoSDAT->codigo,
                "valor" => number_format($sdat->valor),
                "fechaConstitucion" => $sdat->fecha_constitucion->format("Y-m-d"),
                "plazoDias" => $sdat->plazo,
                "fechaVencimiento" => $sdat->fecha_vencimiento->format("Y-m-d"),
                "tasaEA" => number_format($sdat->tasa, 2),
                "saldo" => number_format($movimientos, 0),
                "interesesReconocidos" => number_format($rendimientos, 0),
                "estado" => $sdat->estado,
            ];
            array_push($data, $dato);
        }
        return $data;
    }

}

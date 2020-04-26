<?php

namespace App\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Creditos
{

    public static function getCreditos($socio)
    {
        $tercero = $socio->tercero;
        $fecha = Carbon::now()->startOfday();

        $sql = "exec creditos.sp_saldo_total_creditos ?, ?";
        $res = DB::select($sql, [$socio->id, $fecha]);
        $porcentaje = intval($res[0]->porcentajePago);

        $creditos = $tercero
            ->solicitudesCreditos()
            ->with('modalidadCredito')
            ->where('fecha_desembolso', '<=', $fecha)
            ->estado('DESEMBOLSADO')
            ->get();

        $creditos->transform(function($item, $key) use($fecha) {
            $item->saldoCapital = $item->saldoObligacion($fecha);
            $item->saldoIntereses = $item->saldoInteresObligacion($fecha);
            return $item;
        });
        $creditos = $creditos->where("saldoCapital", ">", 0);

        $cuotas = $creditos->sum("valor_cuota");
        $intereses = $creditos->sum("saldoIntereses");
        $saldosCapitales = $creditos->sum("saldoCapital");

        $data = [
            "totalCuota" => number_format($cuotas, 0),
            "totalSaldoCapital" => number_format($saldosCapitales, 0),
            "totalIntereses" => number_format($intereses, 0),
            "total" => number_format($saldosCapitales + $intereses, 0),
            "porcentajeAbonado" => $porcentaje,
            "creditos" => Creditos::getObligaciones($creditos),
            "codeudas" => Creditos::getCodeudas($tercero, $fecha)
        ];
        return $data;
    }

    private static function getObligaciones($creditos)
    {
        $data = [];
        foreach($creditos as $credito) {
            $total = $credito->saldoCapital + $credito->saldoIntereses;
            $dato = [
                "id" => $credito->id,
                "numeroObligacion" => $credito->numero_obligacion,
                "modalidad" => $credito->modalidadCredito->nombre,
                "fechaDesembolso" => $credito->fecha_desembolso->format("Y-m-d"),
                "valorInicial" => number_format($credito->valor_credito, 0),
                "tasa" => number_format($credito->tasa, 3),
                "cuota" => number_format($credito->valor_cuota, 0),
                "saldoCapital" => number_format($credito->saldoCapital, 0),
                "saldoIntereses" => number_format($credito->saldoIntereses, 0),
                "saldoTotal" => number_format($total, 0)
            ];
            array_push($data, $dato);
        }
        return $data;
    }

    private static function getCodeudas($tercero, $fecha)
    {
        $codeudas = $tercero
            ->codeudas()
            ->with('solicitudCredito')
            ->whereHas('solicitudCredito', function($q){
            return $q->whereEstadoSolicitud('DESEMBOLSADO');
        })->get();

        $data = [];
        foreach ($codeudas as $codeuda) {
            $sc = $codeuda->solicitudCredito;
            $saldo = $sc->saldoObligacion($fecha);
            if($saldo <= 0) {
                continue;
            }
            $ter = $sc->tercero;
            $identificacion = "%s %s";
            $identificacion = sprintf(
                $identificacion,
                $ter->tipoIdentificacion->codigo,
                number_format($ter->numero_identificacion, 0)
            );

            $dato = [
                "identificacion" => $identificacion,
                "deudor" => $ter->nombre_corto,
                "numeroObligacion" => $sc->numero_obligacion,
                "fechaDesembolso" => $sc->fecha_desembolso->format("Y-m-d"),
                "valorInicial" => number_format($sc->valor_credito, 0),
                "tasa" => number_format($sc->tasa, 3),
                "saldoCapital" => number_format($saldo, 0),
                "calificacion" => $sc->calificacion_obligacion
            ];
            array_push($data, $dato);
        }
        return $data;
    }

}

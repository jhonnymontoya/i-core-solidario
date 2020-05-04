<?php

namespace App\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Creditos
{

    const POSITIVO = 'POSITIVO';
    const NEGATIVO = 'NEGATIVO';

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

    public static function getDetalleCredito($tercero, $credito)
    {
        $fecha = Carbon::now()->startOfday();
        $amortizaciones = $credito->amortizaciones;

        $data = [
            "numeroObligacion" => $credito->numero_obligacion,
            "modalidad" => $credito->modalidadCredito->nombre,
            "fechaDesembolso" => $credito->fecha_desembolso->format("Y-m-d"),
            "valorInicial" => number_format($credito->valor_credito, 0),
            "tasa" => number_format($credito->tasa, 3),
            "cuota" => number_format($credito->valor_cuota, 0),
            "saldoCapital" => number_format(
                $credito->saldoObligacion($fecha),
                0
            ),
            "saldoIntereses" => number_format(
                $credito->saldoInteresObligacion($fecha),
                0
            ),
            "saldoSeguro" => number_format(
                $credito->saldoSeguroObligacion($fecha),
                0
            ),
            "capitalVencido" => number_format(
                $credito->capitalVencido($fecha),
                0
            ),
            "diasVencidos" => number_format(
                $credito->diasVencidos($fecha),
                0
            ),
            "plazo" => $credito->plazo,
            "altura" => number_format(
                $credito->alturaObligacion($fecha),
                0
            ),
            "periodicidad" => mb_convert_case(
                $credito->periodicidad,
                MB_CASE_UPPER,
                "UTF-8"
            ),
            "formaPago" => mb_convert_case(
                $credito->forma_pago,
                MB_CASE_UPPER,
                "UTF-8"
            ),
            "fechaInicioPago" => $credito->fecha_primer_pago->format("Y-m-d"),
            "fechaFinPago" => $credito->fecha_primer_pago->format("Y-m-d"),
            "fechaUltimoMovimiento" => Creditos::getFechaUltimoPagoObligacion(
                $amortizaciones
            )->format("Y-m-d"),
            "tipoCuota" => mb_convert_case(
                $credito->tipo_amortizacion,
                MB_CASE_UPPER,
                "UTF-8"
            ),
            "calificacion" => $credito->calificacion_obligacion,
            "movimientosCreditos" => Creditos::getCreditoMovimientos(
                $credito,
                $fecha
            ),
            "amortizacion" => Creditos::getCreditoAmortizacion(
                $credito,
                $amortizaciones
            ),
            "codeudores" => Creditos::getCreditoCodeudores($credito)
        ];
        return $data;
    }

    private static function getFechaUltimoPagoObligacion($amortizaciones)
    {
        $fecha = '';
        if($amortizaciones->count()) {
            $fecha = $amortizaciones[$amortizaciones->count() - 1]->fecha_cuota;
        }
        else {
            $fecha = Carbon::createFromFormat('Y-m-d', '2050-12-31')
                ->startOfDay();
        }
        return $fecha;
    }

    private static function getCreditoMovimientos($credito, $fecha)
    {
        $res = DB::select(
            'EXEC creditos.sp_movimientos_por_obligacion ?, ?',
            [$credito->id, $fecha]
        );
        $data = [];
        foreach ($res as $movimiento) {
            $fecha = Carbon::createFromFormat(
                'Y-m-d H:i:s.000',
                $movimiento->fecha_movimiento
            )->startOfDay();
            $capital = intval($movimiento->capital);
            $signoCapital = Creditos::POSITIVO;
            if($capital >= 0) {
                $signoCapital = Creditos::POSITIVO;
            }
            else {
                $signoCapital = Creditos::NEGATIVO;
            }
            $capital = abs($capital);

            $intereses = intval($movimiento->intereses);
            $signoIntereses = Creditos::POSITIVO;
            if($intereses >= 0) {
                $signoIntereses = Creditos::POSITIVO;
            }
            else {
                $signoIntereses = Creditos::NEGATIVO;
            }
            $intereses = abs($intereses);

            $total = intval($movimiento->total);
            $signoTotal = Creditos::POSITIVO;
            if($total >= 0) {
                $signoTotal = Creditos::POSITIVO;
            }
            else {
                $signoTotal = Creditos::NEGATIVO;
            }
            $total = abs($total);

            $dato = [
                "fecha" => $fecha->format("Y-m-d"),
                "concepto" => $movimiento->concepto,
                "detalle" => $movimiento->detalle,
                "capital" => number_format($capital, 0),
                "signoCapital" => $signoCapital,
                "intereses" => number_format($intereses, 0),
                "signoIntereses" => $signoIntereses,
                "total" => number_format($total, 0),
                "signoTotal" => $signoTotal,
            ];
            array_push($data, $dato);
        }
        return $data;
    }

    private static function getCreditoAmortizacion($credito, $amortizaciones)
    {
        $tasaEA = ($credito->tasa / 100) + 1;
        $tasaEA = pow($tasaEA, 12) - 1;
        $data = [
            "tasaSeguroCartera" => number_format(
                optional($credito->seguroCartera)->tasa_mes,
                4
            ),
            "porcentajeCapitalExtraordinarias" => number_format(
                $credito->porcentajeCapitalEnExtraordinarias(),
                2
            ),
            "tasaEfectivaAnual" => number_format($tasaEA * 100, 2),
            "amortizaciones" => Creditos::getDetalleCreditoAmortizacion(
                $amortizaciones
            )
        ];
        return $data;
    }

    private static function getDetalleCreditoAmortizacion($amortizaciones)
    {
        $data = [];
        foreach ($amortizaciones as $amortizacion) {
            $dato = [
                "cuota" => $amortizacion->numero_cuota,
                "naturalezaCuota" => mb_convert_case(
                    $amortizacion->naturaleza_cuota,
                    MB_CASE_UPPER,
                    "UTF-8"
                ),
                "formaPago" => mb_convert_case(
                    $amortizacion->forma_pago,
                    MB_CASE_UPPER,
                    "UTF-8"
                ),
                "fechaPago" => $amortizacion->fecha_cuota->format("Y-m-d"),
                "capital" => number_format($amortizacion->abono_capital, 0),
                "intereses" => number_format($amortizacion->abono_intereses, 0),
                "seguroCartera" => number_format(
                    $amortizacion->abono_seguro_cartera,
                    0
                ),
                "totalCuota" => number_format($amortizacion->total_cuota, 0),
                "nuevoSaldo" => number_format(
                    $amortizacion->nuevo_saldo_capital,
                    0
                )
            ];
            array_push($data, $dato);
        }
        return $data;
    }

    private static function getCreditoCodeudores($credito)
    {
        $data = [];
        $codeudores = $credito->codeudores()
            ->with(['tercero', 'tercero.socio', 'tercero.tipoIdentificacion'])
            ->get();
        foreach ($codeudores as $codeudor) {
            $tercero = $codeudor->tercero;
            $socio = $tercero->socio;
            $dato = [
                "tipoIdentificacion" => $tercero->tipoIdentificacion->codigo,
                "identificacion" => $tercero->numero_identificacion,
                "nombre" => $tercero->nombre_completo,
                "nombreCorto" => $tercero->nombre_corto,
                "estado" => $socio->estado ?? "No asociado"
            ];
            array_push($data, $dato);
        }
        return $data;
    }

}

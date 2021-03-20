<?php

namespace App\Api;

use Carbon\Carbon;
use App\Helpers\ConversionHelper;
use App\Helpers\FinancieroHelper;
use App\Models\Creditos\Modalidad;
use Illuminate\Support\Facades\DB;
use App\Models\Creditos\SolicitudCredito;

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
        $amortizaciones = $credito->amortizaciones()
            ->orderBy("fecha_cuota")
            ->orderBy("naturaleza_cuota", "desc")
            ->get();
        $movimientos = Creditos::getCreditoMovimientos($credito, $fecha);

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
            "fechaInicioPago" => null,
            "fechaFinPago" => Creditos::getFechaUltimoPagoObligacion(
                $amortizaciones
            )->format("Y-m-d"),
            "fechaUltimoMovimiento" => null,
            "tipoCuota" => mb_convert_case(
                $credito->tipo_amortizacion,
                MB_CASE_UPPER,
                "UTF-8"
            ),
            "calificacion" => $credito->calificacion_obligacion,
            "movimientosCreditos" => $movimientos,
            "amortizacion" => Creditos::getCreditoAmortizacion(
                $credito,
                $amortizaciones
            ),
            "codeudores" => Creditos::getCreditoCodeudores($credito)
        ];
        if(count($movimientos) > 0) {
            $fechaInicio = $movimientos[count($movimientos) - 1]["fecha"];
            $fechaFin = $movimientos[0]["fecha"];
            $data["fechaInicioPago"] = $fechaInicio;
            $data["fechaUltimoMovimiento"] = $fechaFin;
        }
        else {
            $data["fechaInicioPago"] = "0000-00-00";
            $data["fechaUltimoMovimiento"] = "0000-00-00";
        }

        if(is_null($credito->fecha_primer_pago) == false) {
            $data["fechaInicioPago"] = $credito->fecha_primer_pago
                ->format("Y-m-d");
        }

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

    public static function crearSolicitudCredito($socio, $modalidadCredito, $valorSolicitado, $plazo, $observaciones){
        $modalidadesCredito = Modalidad::find($modalidadCredito);
        $tercero = $socio->tercero;
        $fechaSolicitud = Carbon::now()->startOfDay();

        $seguroCartera = null;
        if($modalidadesCredito->segurosCartera->count() > 0) {
            $seguroCartera = $modalidadesCredito->segurosCartera[0];
        }

        $solicitud = new SolicitudCredito;
        $solicitud->entidad_id = $tercero->entidad_id;
        $solicitud->tercero_id = $tercero->id;
        $solicitud->modalidad_credito_id = $modalidadesCredito->id;
        $solicitud->seguro_cartera_id = optional($seguroCartera)->id;

        $solicitud->valor_solicitud = $valorSolicitado;
        $solicitud->valor_credito = $valorSolicitado;
        $solicitud->fecha_solicitud = $fechaSolicitud;
        $solicitud->tipo_pago_intereses = $modalidadesCredito->pago_interes;
        $solicitud->tipo_amortizacion = $modalidadesCredito->tipo_cuota;
        $solicitud->tipo_tasa = $modalidadesCredito->tipo_tasa;
        $solicitud->tasa = $modalidadesCredito->obtenerValorTasa(
            $valorSolicitado,
            0,
            $fechaSolicitud,
            $socio->fecha_ingreso,
            $socio->fecha_antiguedad
        );
        $solicitud->aplica_mora = $modalidadesCredito->aplica_mora;

        $solicitud->quien_inicio_usuario = $tercero->numero_identificacion;
        $solicitud->quien_inicio = $tercero->nombre_corto;
        $solicitud->canal = 'DIGITAL';
        $solicitud->periodicidad = $socio->pagaduria->periodicidad_pago;
        $solicitud->plazo = $plazo;
        $solicitud->observaciones = $observaciones;
        $solicitud->save();

        //Se agregan los documentos
        $solicitud
            ->documentos()
            ->sync($solicitud->modalidadCredito->documentacionModalidad->pluck('id'));

        return $solicitud;
    }

    public static function getModalidadesDeCredito($entidadId)
    {
        $modalidadesCredito = Modalidad::entidadId($entidadId)
            ->activa()
            ->usoSocio()
            ->get();

        $data = collect();
        foreach ($modalidadesCredito as $modalidadCredito) {
            $modalidad = (object)[
                "id" => $modalidadCredito->id,
                "nombre" => $modalidadCredito->nombre,
                "periodicidadesDePago" => Creditos::getArrayPeriodicidades($modalidadCredito)
            ];
            $data->push($modalidad);
        }
        return $data;
    }

    private static function getArrayPeriodicidades($modalidadDeCredito)
    {
        $periodicidades = [];

        if($modalidadDeCredito->acepta_pago_anual)
        {
            array_push($periodicidades, "ANUAL");
        }

        if($modalidadDeCredito->acepta_pago_semestral)
        {
            array_push($periodicidades, "SEMESTRAL");
        }

        if($modalidadDeCredito->acepta_pago_cuatrimestral)
        {
            array_push($periodicidades, "CUATRIMESTRAL");
        }

        if($modalidadDeCredito->acepta_pago_trimestral)
        {
            array_push($periodicidades, "TRIMESTRAL");
        }

        if($modalidadDeCredito->acepta_pago_bimensual)
        {
            array_push($periodicidades, "BIMESTRAL");
        }

        if($modalidadDeCredito->acepta_pago_mensual)
        {
            array_push($periodicidades, "MENSUAL");
        }

        if($modalidadDeCredito->acepta_pago_quincenal)
        {
            array_push($periodicidades, "QUINCENAL");
        }

        if($modalidadDeCredito->acepta_pago_catorcenal)
        {
            array_push($periodicidades, "CATORCENAL");
        }

        if($modalidadDeCredito->acepta_pago_decadal)
        {
            array_push($periodicidades, "DECADAL");
        }

        if($modalidadDeCredito->acepta_pago_semanal)
        {
            array_push($periodicidades, "SEMANAL");
        }

        return $periodicidades;
    }

    public static function simularCredito($socio, $modalidadCreditoId, $periodicidad, $valorCredito, $plazo)
    {
        $fechaCredito = Carbon::now()->startOfDay();
        $modalidad = Modalidad::find($modalidadCreditoId);
        $fechaPrimerPago = $socio->pagaduria
            ->calendarioRecaudos()
            ->whereEstado("programado")
            ->first();

        $fechaPrimerPago = $fechaPrimerPago->fecha_recaudo;

        $amortizacion = FinancieroHelper::obtenerAmortizacion(
            $modalidad,
            $valorCredito,
            $fechaCredito,
            $fechaPrimerPago,
            $plazo,
            $periodicidad
        );
        if(!$amortizacion) {
            throw new Exception("Plazo invalido", 1);
        }
        $plazoTmp = ConversionHelper::conversionValorPeriodicidad(
            $plazo,
            "MENSUAL",
            $periodicidad
        );
        $tasa = null;
        if($modalidad->es_tasa_condicionada) {
            $condicion = $modalidad->condicionesModalidad()
                ->whereTipoCondicion("TASA")
                ->first();
            if(!$condicion) {
                throw new Exception("No existe la condicioón", 1);
            }
            if(!$condicion->contenidoEnCondicion($plazoTmp)) {
                throw new Exception("El plazo no puede estar en dicha condición", 1);

            }
            $tasa = floatval($condicion->valorCondicionado($plazoTmp));
        }
        else {
            $tasa = $modalidad->tasa;
        }

        $dato[] = $amortizacion[0];
        $dato[] = $amortizacion[count($amortizacion) - 1];

        $data = array(
            "fechaSimulacion" => $fechaCredito->format("Y-m-d"),
            "modalidadDeCredito" => $modalidad->nombre,
            "periodicidad" => $periodicidad,
            "valorCredito" => "$" . number_format($valorCredito, 0),
            "plazo" => $plazo,
            "tasa" => number_format($tasa, 2) . "% M.V.",
            "numeroCuotas" => count($amortizacion),
            "fechaPrimerPago" => $fechaPrimerPago->format("Y-m-d"),
            "fechaUltimoPago" => $dato[1]["fechaCuota"]->format("Y-m-d"),
            "valorCuota" => "$" . number_format($dato[0]["total"], 0)
        );

        return $data;

    }

}

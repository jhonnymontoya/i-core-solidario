<?php

namespace App\Api;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class Recaudos
{

    public static function getRecaudos($socio)
    {
        $tercero = $socio->tercero;
        $fecha = Carbon::now()->startOfday();

        $data = [];
        $recaudos = $tercero
            ->recaudosNomina()
            ->whereHas('controlProceso', function($q){
                $q->whereEstado("APLICADO");
            })
            ->with(['controlProceso', 'conceptoRecaudo', 'controlProceso.calendarioRecaudo'])
            ->select(
                'control_proceso_id',
                'concepto_recaudo_id',
                DB::raw('SUM(capital_generado) + SUM(intereses_generado) + SUM(seguro_generado) as total_generado'),
                DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) + SUM(seguro_aplicado) as total_aplicado'),
                DB::raw('SUM(capital_ajustado) + SUM(intereses_ajustado) + SUM(seguro_ajustado) as total_ajustado')
            )
            ->groupBy('control_proceso_id', 'concepto_recaudo_id')
            ->orderBy('control_proceso_id', 'desc')
            ->orderBy('concepto_recaudo_id', 'asc')
            ->get();

        $control = 0;
        $cantidadRecaudos = 6;
        foreach($recaudos as $recaudo) {
            $fechaRecaudo = $recaudo
                ->controlProceso
                ->calendarioRecaudo
                ->fecha_recaudo
                ->format("Y-m-d");

            if($control == $recaudo->control_proceso_id) {
                continue;
            }

            if($cantidadRecaudos-- == 0) {
                break;
            }

            $control = $recaudo->control_proceso_id;

            $generado = $recaudos
                ->where("control_proceso_id", $control)
                ->sum("total_generado");

            $aplicado = $recaudos
                ->where("control_proceso_id", $control)
                ->sum("total_aplicado");

            $ajustado = $recaudos
                ->where("control_proceso_id", $control)
                ->sum("total_ajustado");

            $dato = [
                "fechaRecaudo" => $fechaRecaudo,
                "totalGenerado" => number_format($generado, 0),
                "totalAplicado" => number_format($aplicado, 0),
                "totalAjustado" => number_format($ajustado, 0),
                "conceptos" => Recaudos::getConceptos($recaudos, $control)
            ];
            array_push($data, $dato);
        }

        return $data;
    }

    private static function getConceptos($recaudos, $controlProcesoId)
    {
        $recaudosCP = $recaudos->where("control_proceso_id", $controlProcesoId);
        $data = [];
        foreach($recaudosCP as $recaudo) {
            $concepto = $recaudo->conceptoRecaudo;
            $dato = [
                "nombre" => $concepto->nombre,
                "codigo" => $concepto->codigo,
                "generado" => number_format($recaudo->total_generado, 0),
                "aplicado" => number_format($recaudo->total_aplicado, 0),
                "ajustado" => number_format($recaudo->total_ajustado, 0)
            ];
            array_push($data, $dato);
        }
        return $data;
    }
}

<?php

namespace App\Listeners\ControlVigilancia;

use Carbon\Carbon;
use App\Events\ICoreCronJob;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class DespachaAlertasSarlaft
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
    public function handle(ICoreCronJob $event)
    {
        $entidades = Entidad::activa()
            ->with(["oficialesCumplimiento", "alertas"])
            ->has("oficialesCumplimiento")
            ->get();

        foreach($entidades as $entidad)
        {
            $oficialCumlimiento = $entidad->oficialesCumplimiento->first();
            $alertas = $entidad->alertas;

            foreach($alertas as $alerta){
                $this->despacharAlerta($oficialCumlimiento, $alerta);
            }
        }
    }

    private function despacharAlerta($oficialCumlimiento, $alerta)
    {
        $periodicidades = $this->getConfiguracionPeriodicidades($alerta);

        //Se hace case de las alertas para despachar
        switch ($alerta->nombre) {
            case 'LOG DE LISTAS':
                $this->logDeListas($oficialCumlimiento, $alerta, $periodicidades);
                break;

            case 'TRANSACCIONES EN EFECTIVO':
                $this->transaccionesEnEfectivo($oficialCumlimiento, $alerta, $periodicidades);
                break;
        }
    }

    private function getPeriodicidades()
    {
        return [
            'DIARIO'    => false,
            'SEMANAL'   => false,
            'MENSUAL'   => false,
            'ANUAL'     => false
        ];
    }

    private function getConfiguracionPeriodicidades($alerta)
    {
        $periodicidades = $this->getPeriodicidades();
        $now = Carbon::now();

        if($alerta->diario)
        {
            if($now->greaterThanOrEqualTo($alerta->fecha_proximo_diario)){
                $periodicidades["DIARIO"] = true;
            }
        }

        if($alerta->semanal)
        {
            if($now->greaterThanOrEqualTo($alerta->fecha_proximo_semanal)){
                $periodicidades["SEMANAL"] = true;
            }
        }

        if($alerta->mensual)
        {
            if($now->greaterThanOrEqualTo($alerta->fecha_proximo_mensual)){
                $periodicidades["MENSUAL"] = true;
            }
        }

        if($alerta->anual)
        {
            if($now->greaterThanOrEqualTo($alerta->fecha_proximo_anual)){
                $periodicidades["ANUAL"] = true;
            }
        }

        return $periodicidades;
    }

    private function actualizarProximo($alerta, $periodicidad)
    {
        $now = Carbon::now();

        switch ($periodicidad) {
            case 'DIARIO':
                $now->endOfDay();
                $alerta->fecha_proximo_diario = $now;
                break;

            case 'SEMANAL':
                $now->endOfWeek();
                $alerta->fecha_proximo_semanal = $now;
                break;

            case 'MENSUAL':
                $now->endOfMonth();
                $alerta->fecha_proximo_mensual = $now;
                break;

            case 'ANUAL':
                $now->endOfYear();
                $alerta->fecha_proximo_anual = $now;
                break;
        }

        $alerta->save();
    }


    //////////////////////////////////////////////////////////////////
    // FUNCIONES DE DESPACHAR
    //////////////////////////////////////////////////////////////////


    private function logDeListas($oficialCumlimiento, $alerta, $periodicidades)
    {
        if($periodicidades['DIARIO'])
        {
            $this->actualizarProximo($alerta, 'DIARIO');
        }

        if($periodicidades['SEMANAL'])
        {
            $this->actualizarProximo($alerta, 'SEMANAL');
        }

        if($periodicidades['MENSUAL'])
        {
            $this->actualizarProximo($alerta, 'MENSUAL');
        }

        if($periodicidades['ANUAL'])
        {
            $this->actualizarProximo($alerta, 'ANUAL');
        }
    }

    private function transaccionesEnEfectivo($oficialCumlimiento, $alerta, $periodicidades)
    {
        if($periodicidades['DIARIO'])
        {
            $this->actualizarProximo($alerta, 'DIARIO');
        }

        if($periodicidades['SEMANAL'])
        {
            $this->actualizarProximo($alerta, 'SEMANAL');
        }

        if($periodicidades['MENSUAL'])
        {
            $this->actualizarProximo($alerta, 'MENSUAL');
        }

        if($periodicidades['ANUAL'])
        {
            $this->actualizarProximo($alerta, 'ANUAL');
        }
    }
}

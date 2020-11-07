<?php

namespace App\Listeners\ControlVigilancia;

use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Events\ICoreCronJob;
use App\Models\General\Entidad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Mail\ControlVigilancia\AlertaChekeoListasControl;
use App\Mail\ControlVigilancia\AlertaTransaccionesEnEfectivo;

class DespachaAlertasSarlaft
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
        $fechaFin = Carbon::now()->startOfDay();
        $fechaInicio = $fechaFin->clone();

        foreach ($periodicidades as $periodicidad => $value) {
            if($value == false)
            {
                continue;
            }

            if($periodicidad == 'DIARIO')
            {
                $fechaInicio->subDay();
                $this->actualizarProximo($alerta, 'DIARIO');
            }

            if($periodicidad == 'SEMANAL')
            {
                $fechaInicio->subWeek();
                $this->actualizarProximo($alerta, 'SEMANAL');
            }

            if($periodicidad == 'MENSUAL')
            {
                $fechaInicio->subMonth();
                $this->actualizarProximo($alerta, 'MENSUAL');
            }

            if($periodicidad == 'ANUAL')
            {
                $fechaInicio->subYear();
                $this->actualizarProximo($alerta, 'ANUAL');
            }

            $query = "EXEC controlVigilancia.sp_chequeo_listas_control_por_rango_tiempo ?, ?, ?";
            $datos = DB::select($query, [$alerta->entidad_id, $fechaInicio, $fechaFin]);

            $archivo = "app%stemp%s%s";
            $archivo = sprintf(
                $archivo,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                Str::uuid()
            );
            $archivo = storage_path($archivo);

            $fp = fopen($archivo, 'w');
            if($datos)
            {
                fputcsv($fp, array_keys((array)$datos[0]));
                foreach($datos as $data) {
                    fputcsv($fp, (array)$data);
                }
            }
            fclose($fp);

            $correo = new AlertaChekeoListasControl(
                $oficialCumlimiento,
                $periodicidad,
                $archivo
            );
            if(empty($oficialCumlimiento->email_copia) == false)
            {
                Mail::to($oficialCumlimiento->email)
                    ->cc($oficialCumlimiento->email_copia)
                    ->send($correo);
            }
            else
            {
                Mail::to($oficialCumlimiento->email)->send($correo);
            }
        }
    }

    private function transaccionesEnEfectivo($oficialCumlimiento, $alerta, $periodicidades)
    {
        $fechaFin = Carbon::now()->startOfDay();
        $fechaInicio = $fechaFin->clone();

        foreach ($periodicidades as $periodicidad => $value) {
            if($value == false)
            {
                continue;
            }

            if($periodicidad == 'DIARIO')
            {
                $fechaInicio->subDay();
                $this->actualizarProximo($alerta, 'DIARIO');
            }

            if($periodicidad == 'SEMANAL')
            {
                $fechaInicio->subWeek();
                $this->actualizarProximo($alerta, 'SEMANAL');
            }

            if($periodicidad == 'MENSUAL')
            {
                $fechaInicio->subMonth();
                $this->actualizarProximo($alerta, 'MENSUAL');
            }

            if($periodicidad == 'ANUAL')
            {
                $fechaInicio->subYear();
                $this->actualizarProximo($alerta, 'ANUAL');
            }

            $query = "EXEC controlVigilancia.sp_transacciones_efectivo ?, ?, ?";
            $datos = DB::select($query, [$alerta->entidad_id, $fechaInicio, $fechaFin]);

            $archivo = "app%stemp%s%s";
            $archivo = sprintf(
                $archivo,
                DIRECTORY_SEPARATOR,
                DIRECTORY_SEPARATOR,
                Str::uuid()
            );
            $archivo = storage_path($archivo);

            $fp = fopen($archivo, 'w');
            if($datos)
            {
                fputcsv($fp, array_keys((array)$datos[0]));
                foreach($datos as $data) {
                    fputcsv($fp, (array)$data);
                }
            }
            fclose($fp);

            $correo = new AlertaTransaccionesEnEfectivo(
                $oficialCumlimiento,
                $periodicidad,
                $archivo
            );
            if(empty($oficialCumlimiento->email_copia) == false)
            {
                Mail::to($oficialCumlimiento->email)
                    ->cc($oficialCumlimiento->email_copia)
                    ->send($correo);
            }
            else
            {
                Mail::to($oficialCumlimiento->email)->send($correo);
            }
        }
    }
}

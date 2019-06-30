<?php

namespace App\Http\Controllers\ControlVigilancial;

use App\Http\Controllers\Controller;
use App\Http\Requests\ControlVigilancia\ArhivosSES\DescargarReporteRequest;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Route;
use Illuminate\Support\Str;
use Log;
use DB;

class ArchivosSESController extends Controller
{

    use FonadminTrait;

    public function __construct()
    {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request)
    {
        $this->logActividad("Ingresó a Archivos SES", $request);
        $req = $request->validate([
            'fecha_reporte' => 'bail|nullable|date_format:"Y/m"'
        ]);

        $fecha = Carbon::now()->startOfDay();
        $carteraCerrada = false;

        if (!empty($req["fecha_reporte"])) {
            $fecha = Carbon::createFromFormat('Y/m', $req["fecha_reporte"])
                ->startOfDay();

            if(!$this->moduloCerrado(7, $fecha)) {
                $mensaje = "Módulo de cartera sin cerrar para el periodo de reporte";
                return redirect('archivosSES')
                    ->withErrors(['fecha_reporte' => $mensaje])
                    ->withInput();
            }
            else {
                $carteraCerrada = true;
            }
        }
        $reportes = array(
            'ASOCIADOS' => 'Asociados',
            'CATALOGOCUENTAS' => 'Catálogo de cuentas',
            //'DIRECTIVOS' => 'Directivos',
            'APORTES' => 'Aportes',
            'CAPTACIONES' => 'Captaciones',
            'CARTERACREDITOS' => 'Cartera de créditos'
        );
        return view('controlVigilancia.archivosSES.index')
            ->withFecha($fecha)
            ->withCarteraCerrada($carteraCerrada)
            ->withReportes($reportes);
    }

    public function descargar(DescargarReporteRequest $request)
    {
        $fecha = Carbon::createFromFormat('Y/m', $request->fecha_reporte)
            ->endOfMonth()
            ->startOfDay();
        if(!$this->moduloCerrado(7, $fecha)) {
            $mensaje = "Módulo de cartera sin cerrar para el periodo de reporte";
            return redirect()
                ->back()
                ->withErrors(['fecha_reporte' => $mensaje])
                ->withInput();
        }

        $nombre = "%s_%s.csv";
        $nombre = sprintf(
            $nombre,
            $request->reporte,
            str_replace("/", "", $request->fecha_reporte)
        );
        $file = null;
        switch ($request->reporte) {
            case 'ASOCIADOS':
                $file = $this->generarAsociados($fecha);
                break;
            case 'CATALOGOCUENTAS':
                $file = $this->generarCatalogoCuentas($fecha);
                break;
            case 'DIRECTIVOS':
                $inf = "Descargando Directivos";
                break;
            case 'APORTES':
                $file = $this->generarAportes($fecha);
                break;
            case 'CAPTACIONES':
                $file = $this->generarCaptaciones($fecha);
                break;
            case 'CARTERACREDITOS':
                $file = $this->generarCartera($fecha);
                break;
            
            default:
                break;
        }
        if ($file) {
            return response()
                ->download($file, $nombre, [])
                ->deleteFileAfterSend(true);
        }
        else {
            $mensaje = "Error al generar el reporte: %s en entidad %s";
            $mensaje = sprintf(
                $mensaje,
                json_encode($request->all()),
                $this->getEntidad()->id
            );
            Log::error($mensaje);
            abort(500, "Error al generar el reporte");
        }
    }

    ///////////////////////////////

    private function generarAsociados($fecha)
    {
        $sp = "EXEC controlVigilancia.sp_generacion_asociados_ses ?, ?";
        $data = DB::select($sp, [$this->getEntidad()->id, $fecha]);
        return $this->escribir($data);
    }

    public function generarCatalogoCuentas($fecha)
    {
        $sp = "EXEC contabilidad.sp_balance_prueba ?, ?, ?, ?";
        $data = DB::select(
            $sp,
            [
                $this->getEntidad()->id,
                $fecha->year,
                $fecha->month,
                4
            ]
        );
        $resultado = 0;
        foreach ($data as &$item) {
            if ($item->cuenta == 4) {
                $resultado = $item->saldo;
            }
            if ($item->cuenta == 5 || $item->cuenta == 6 || $item->cuenta == 6) {
                $resultado -= $item->saldo;
            }
            unset($item->naturaleza);
            unset($item->nivel);
            unset($item->saldo_anterior);
            unset($item->debitos);
            unset($item->creditos);
            $item->cuenta = str_pad($item->cuenta, 6, "0", STR_PAD_RIGHT);
        }
        $resPut1 = false;
        $resPut2 = false;
        $resPut3 = false;
        foreach ($data as &$item) {
            if ($item->cuenta == "300000") {
                $item->saldo += $resultado;
            }
            if ($item->cuenta == "350000") {
                $item->saldo += $resultado;
                $resPut1 = true;
            }
            if ($resultado >= 0) {
                if ($item->cuenta == "350500") {
                    $item->saldo += $resultado;
                    $resPut2 = true;
                }
            }
            else {
                if ($item->cuenta == "351000") {
                    $item->saldo += $resultado;
                    $resPut2 = true;
                }
            }
            if ($item->cuenta == "500000") {
                $item->saldo += $resultado;
            }
            if ($item->cuenta == "530000") {
                $item->saldo += $resultado;
                $resPut3 = true;
            }
        }
        if (!$resPut1) {
            $cuenta = array(
                "cuenta" => "350000",
                "nombre" => "RESULTADO DEL EJERCICIO",
                "saldo" => $resultado
            );
            $data[] = (object) $cuenta;
        }
        if (!$resPut2) {
            if ($resultado >= 0) {
                $cuenta = array(
                    "cuenta" => "350500",
                    "nombre" => "EXEDENTES",
                    "saldo" => $resultado
                );
            }
            else {
                $cuenta = array(
                    "cuenta" => "351000",
                    "nombre" => "PERDIDAS",
                    "saldo" => $resultado
                );
            }
            $data[] = (object) $cuenta;
        }
        if (!$resPut3) {
            $cuenta = array(
                "cuenta" => "530000",
                "nombre" => "EXCEDENTES Y PERDIDAS DEL EJERCICIO",
                "saldo" => $resultado
            );
            $data[] = (object) $cuenta;
        }
        usort($data, function($i, $j){
            return $i->cuenta < $j->cuenta ? -1 : 1;
        });
        return $this->escribir($data);
    }

    private function generarAportes($fecha)
    {
        $sp = "EXEC controlVigilancia.sp_generacion_aportes_ses ?, ?";
        $data = DB::select($sp, [$this->getEntidad()->id, $fecha]);
        return $this->escribir($data);
    }

    private function generarCaptaciones($fecha)
    {
        $sp = "EXEC controlVigilancia.sp_generacion_captaciones_ses ?, ?";
        $data = DB::select($sp, [$this->getEntidad()->id, $fecha]);
        return $this->escribir($data);
    }

    private function generarCartera($fecha)
    {
        $sp = "EXEC controlVigilancia.sp_generacion_cartera_ses ?, ?";
        $data = DB::select($sp, [$this->getEntidad()->id, $fecha]);
        return $this->escribir($data);
    }

    private function escribir($data)
    {
        $archivo = tempnam(storage_path(), (string) Str::uuid());
        $f = fopen($archivo, "w");
        foreach($data as $fila){
            fputcsv($f, (array) $fila, ';');
        }
        fclose($f);
        return $archivo;
    }

    ///////////////////////////////

    public static function routes() {
        Route::get(
            'archivosSES',
            'ControlVigilancial\ArchivosSESController@index'
        );
        Route::get(
            'archivosSES/descargar',
            'ControlVigilancial\ArchivosSESController@descargar'
        );
    }
}

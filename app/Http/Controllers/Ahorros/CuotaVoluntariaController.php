<?php

namespace App\Http\Controllers\Ahorros;

use Route;
use App\Traits\ICoreTrait;
use App\Models\Socios\Socio;
use Illuminate\Http\Request;
use App\Models\General\Tercero;
use App\Http\Controllers\Controller;
use App\Models\Ahorros\CuotaVoluntaria;
use App\Models\Ahorros\ModalidadAhorro;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Ahorros\CuotaVoluntaria\CreateCuotaVoluntariaRequest;

class CuotaVoluntariaController extends Controller
{
    use ICoreTrait;

    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request) {
        $this->logActividad("Ingreso a cuotas voluntarias", $request);
        $socio = Socio::with('cuotasVoluntarias')
            ->estado('ACTIVO')
            ->whereId($request->socio)
            ->first();

        return view('ahorros.cuotaVoluntaria.index')->withSocio($socio);
    }

    public function create(Socio $obj) {
        $msg = "Ingresó a crear un ahorro voluntario para socio '%s'";
        $this->log(sprintf($msg, $obj->id), 'INGRESAR');

        $tiposCuotasVoluntarias = ModalidadAhorro::entidadId($this->getEntidad()->id)
            ->voluntario()
            ->activa(true)
            ->get();

        $beneficiarios = $obj->beneficiarios()
            ->with(["parentesco", "tercero"])
            ->get();

        $listaBeneficiarios = [];
        foreach($beneficiarios as $beneficiario){
            $nombre = "%s %s (%s)";
            $nombre = sprintf(
                $nombre,
                $beneficiario->tercero->primer_nombre,
                $beneficiario->tercero->primer_apellido,
                $beneficiario->parentesco->nombre
            );
            $listaBeneficiarios[$beneficiario->tercero->id] = $nombre;
        }

        $tiposCuotas = array();
        $modalidadesBeneficiarios = [];
        foreach ($tiposCuotasVoluntarias as $value){
            $tiposCuotas[$value->id] = $value->codigo . " - " . $value->nombre;

            if($value->para_beneficiario){
                $modalidadesBeneficiarios[] = $value->id;
            }
        }

        $periodicidades = array(
                'DIARIO' => 'Diario',
                'SEMANAL' => 'Semanal',
                'DECADAL' => 'Decadal',
                'CATORCENAL' => 'Catorcenal',
                'QUINCENAL' => 'Quincenal',
                'MENSUAL' => 'Mensual',
                'BIMESTRAL' => 'Bimestral',
                'TRIMESTRAL' => 'Trimestral',
                'CUATRIMESTRAL' => 'Cuatrimestral',
                'SEMESTRAL' => 'Semestral',
                'ANUAL' => 'Anual'
            );

        $listaProgramaciones = array();
        $programaciones = $obj->pagaduria
            ->calendarioRecaudos()
            ->whereEstado('PROGRAMADO')
            ->get();

        foreach($programaciones as $programacion) {
            $listaProgramaciones[$programacion->fecha_recaudo->format('d/m/Y')] = $programacion->fecha_recaudo;
        }

        return view('ahorros.cuotaVoluntaria.create')
            ->withSocio($obj)
            ->withTiposCuotasVoluntarias($tiposCuotas)
            ->withPeriodicidades($periodicidades)
            ->withProgramaciones($listaProgramaciones)
            ->withBeneficiarios($listaBeneficiarios)
            ->withModalidadesBeneficiarios(join(",", $modalidadesBeneficiarios));
    }

    public function store(CreateCuotaVoluntariaRequest $request, Socio $obj) {
        $msg = "Creó un ahorro voluntario para el socio '%s' con los siguientes parámetros %s";
        $this->log(sprintf($msg, $obj->id,  json_encode($request->all())), 'CREAR');
        $cuota = new CuotaVoluntaria;

        $cuota->fill($request->all());
        $cuota->socio_id = $obj->id;

        $modalidad = ModalidadAhorro::find($request->modalidad_ahorro_id);

        if($modalidad->para_beneficiario){
            $tercero = Tercero::find($request->tercero_id);
            $nombre = "%s %s";
            $nombre = sprintf(
                $nombre,
                $tercero->primer_nombre,
                $tercero->primer_apellido
            );
            $cuota->beneficiario = $nombre;
        }

        if($modalidad->tipo_ahorro == 'PROGRAMADO') {
            $fechaFinal = $modalidad->getFechaFinalizacion($cuota->periodo_inicial);
            if($cuota->periodo_inicial->greaterThan($fechaFinal)) {
                Session::flash('error', 'La fecha final de la modalidad de ahorro es menor a la fecha de periodo inicial.');
                return redirect()->back()->withInput();
            }
            if(empty($request->periodo_final)) {
                $cuota->periodo_final = $fechaFinal;
            }
            elseif($cuota->periodo_final->greaterThan($fechaFinal)) {
                $cuota->periodo_final = $fechaFinal;
            }
        }
        $cuota->save();

        Session::flash('message', 'Se ha agregado la cuota para \'' . $cuota->modalidadAhorro->nombre . '\'');
        return redirect('cuotaVoluntaria?socio=' . $obj->id);
    }

    public function confirmDelete(CuotaVoluntaria $obj) {
        $msg = "Ingresó a eliminar la cuota voluntaria '%s'";
        $this->log(sprintf($msg, $obj->id), 'INGRESAR');
        return view('ahorros.cuotaVoluntaria.delete')->withCuota($obj);
    }

    public function delete(CuotaVoluntaria $obj) {
        $msg = "Eliminó la cuota voluntaria '%s'";
        $this->log(sprintf($msg, $obj->id), "ELIMINAR");
        $obj->delete();

        Session::flash('message', 'Se ha eliminado la cuota para \'' . $obj->modalidadAhorro->nombre . '\'');
        return redirect('cuotaVoluntaria?socio=' . $obj->socio->id);
    }

    public static function routes() {
        Route::get('cuotaVoluntaria', 'Ahorros\CuotaVoluntariaController@index');
        Route::get('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@create')
            ->name('cuotaVoluntariaCreate');
        Route::post('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@store');
        Route::get('cuotaVoluntaria/{obj}/delete', 'Ahorros\CuotaVoluntariaController@confirmDelete')
            ->name('cuotaVoluntariaDelete');
        Route::delete('cuotaVoluntaria/{obj}', 'Ahorros\CuotaVoluntariaController@delete');
    }
}

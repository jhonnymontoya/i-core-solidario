<?php

namespace App\Http\Controllers\Ahorros;

use Route;
use App\Traits\ICoreTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Ahorros\ModalidadAhorro;
use Illuminate\Support\Facades\Session;
use App\Http\Requests\Ahorros\TipoCuotaAhorros\EditTipoCuotaAhorrosRequest;
use App\Http\Requests\Ahorros\TipoCuotaAhorros\CreateTipoCuotaAhorrosRequest;

class TipoCuotaAhorrosController extends Controller
{
    use ICoreTrait;

    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request) {
        $this->logActividad("Ingreso a tipos de cuotas de ahorros", $request);
        $tiposCuotasAhorros = ModalidadAhorro::entidadId($this->getEntidad()->id)
            ->voluntario()
            ->activa($request->estado)
            ->tipoAhorro($request->tipo_ahorro)
            ->search($request->name)
            ->orderBy('esta_activa', 'desc')
            ->orderBy('nombre', 'asc')
            ->paginate();

        return view('ahorros.tipoCuotaAhorros.index')
            ->withTiposCuotasAhorros($tiposCuotasAhorros);
    }

    public function create() {
        $this->log("Ingresó a crear un tipo de ahorro", 'INGRESAR');
        return view('ahorros.tipoCuotaAhorros.create');
    }

    public function store(CreateTipoCuotaAhorrosRequest $request) {
        $msg = "Creó un tipo de ahorro con los siguientes parámetros %s";
        $this->log(sprintf($msg, json_encode($request->all())), 'CREAR');
        $tipoAhorro = new ModalidadAhorro;

        $tipoAhorro->fill($request->all());
        $tipoAhorro->entidad_id = $this->getEntidad()->id;

        if($tipoAhorro->tipo_ahorro == 'VOLUNTARIO') {
            $tipoAhorro->tipo_vencimiento = null;
            $tipoAhorro->plazo = null;
            $tipoAhorro->fecha_vencimiento_colectivo = null;
            $tipoAhorro->tasa_penalidad = null;
            $tipoAhorro->penalidad_por_retiro = 0;
        }

        $tipoAhorro->save();

        Session::flash('message', 'Se ha creado el tipo de ahorro \'' . $tipoAhorro->nombre . '\'');
        return redirect('tipoCuotaAhorros');
    }

    public function edit(ModalidadAhorro $obj) {
        $msg = "Ingresó a editar el tipo de ahorro '%s'";
        $this->log(sprintf($msg, $obj->id), 'INGRESAR');
        $this->objEntidad($obj, 'No autorizado a editar la modalidad de ahorro');
        return view('ahorros.tipoCuotaAhorros.edit')->withCuota($obj);
    }

    public function update(EditTipoCuotaAhorrosRequest $request, ModalidadAhorro $obj) {
        $this->objEntidad($obj, 'No autorizado a editar la modalidad de ahorro');
        $msg = "Actualizó el tipo de ahorro '%s' con los siguientes parámetros %s";
        $msg = sprintf($msg, $obj->id, json_encode($request->all()));
        $this->log($msg, 'ACTUALIZAR');
        $obj->nombre                        = $request->nombre;
        $obj->tasa                          = $request->tasa;
        $obj->capitalizacion_simultanea     = $request->capitalizacion_simultanea;
        $obj->esta_activa                   = $request->esta_activa;
        $obj->intereses_cuif_id             = $request->intereses_cuif_id;
        $obj->paga_intereses_retirados      = $request->paga_intereses_retirados;
        if($obj->tipo_ahorro != 'VOLUNTARIO') {
            $obj->plazo = $request->plazo;;
            $obj->fecha_vencimiento_colectivo = $request->fecha_vencimiento_colectivo;
            $obj->tasa_penalidad = $request->tasa_penalidad;
            $obj->penalidad_por_retiro = $request->penalidad_por_retiro;
        }

        $obj->save();

        Session::flash('message', 'Se ha actualizado el tipo de ahorro \'' . $obj->nombre . '\'');
        return redirect('tipoCuotaAhorros');
    }

    public static function routes() {
        Route::get('tipoCuotaAhorros', 'Ahorros\TipoCuotaAhorrosController@index');
        Route::get('tipoCuotaAhorros/create', 'Ahorros\TipoCuotaAhorrosController@create');
        Route::post('tipoCuotaAhorros', 'Ahorros\TipoCuotaAhorrosController@store');
        Route::get('tipoCuotaAhorros/{obj}/edit', 'Ahorros\TipoCuotaAhorrosController@edit')
            ->name('tipoCuotaAhorroEdit');
        Route::put('tipoCuotaAhorros/{obj}', 'Ahorros\TipoCuotaAhorrosController@update');
    }
}

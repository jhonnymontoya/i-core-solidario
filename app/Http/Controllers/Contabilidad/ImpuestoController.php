<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Http\Requests\Contabilidad\Impuesto\CreateConceptoRequest;
use App\Http\Requests\Contabilidad\Impuesto\CreateImpuestoRequest;
use App\Http\Requests\Contabilidad\Impuesto\EditImpuestoRequest;
use App\Models\Contabilidad\ConceptoImpuesto;
use App\Models\Contabilidad\Impuesto;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;

class ImpuestoController extends Controller
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
        $this->logActividad("Ingresó a Impuestos", $request);
        $request = $request->validate([
            "name" => "bail|nullable|string|max:250",
            "tipo" => "bail|nullable|string|in:NACIONAL,DISTRITAL,REGIONAL",
            "estado" => "bail|nullable|boolean"
        ]);
        $request = (object) $request;
        $impuestos = Impuesto::with('conceptosImpuestos')
            ->entidadId()
            ->search(optional($request)->name)
            ->tipo(optional($request)->tipo)
            ->activo(optional($request)->estado)
            ->paginate();

        return view('contabilidad.impuesto.index')
            ->withTiposImpuestos($this->getTiposImpuestos())
            ->withImpuestos($impuestos);
    }

    public function create()
    {
        $this->log("Ingresó a crear un nuevo impuesto");
        return view('contabilidad.impuesto.create')
            ->withTiposImpuestos($this->getTiposImpuestos());
    }

    public function store(CreateImpuestoRequest $request)
    {
        $this->log(
            "Creó impuesto con los siguientes parámetros " .
                json_encode($request->all()),
            "CREAR"
        );
        $request->request->add([
            "entidad_id" => $this->getEntidad()->id
        ]);
        $impuesto = Impuesto::create($request->all());
        $mensaje = "Se ha creado el impuesto '%s'";
        Session::flash("message", sprintf($mensaje, $impuesto->nombre));
        return redirect()->route('impuesto.edit', $impuesto->id);
    }

    public function edit(Impuesto $obj)
    {
        $this->objEntidad($obj);
        $mensaje = "Ingresó a editar el impuesto '%s' (%s)";
        $mensaje = sprintf($mensaje, $obj->nombre, $obj->id);

        $conceptos = $obj->conceptosImpuestos()
            ->with('cuentaDestino')
            ->orderBy("esta_activo", "desc")
            ->orderBy("nombre")
            ->get();

        return view('contabilidad.impuesto.edit')
            ->withTiposImpuestos($this->getTiposImpuestos())
            ->withConceptos($conceptos)
            ->withImpuesto($obj);
    }

    public function update(Impuesto $obj, EditImpuestoRequest $request)
    {
        $this->objEntidad($obj);
        $mensaje = "Actualizó el impuesto '%s' (%s) con los siguientes " .
            "parámetros %s";
        $mensaje = sprintf(
            $mensaje,
            $obj->nombre,
            $obj->id, json_encode($request->all())
        );
        $this->log($mensaje, "ACTUALIZAR");
        $obj->nombre = $request->nombre;
        $obj->tipo = $request->tipo;
        $obj->esta_activo = $request->esta_activo;
        $obj->save();
        $mensaje = "Se ha creado el impuesto '%s'";
        Session::flash("message", sprintf($mensaje, $obj->nombre));
        return redirect('impuesto');
    }

    public function agregarConcepto(
        Impuesto $obj,
        CreateConceptoRequest $request
    ) {
        $this->objEntidad($obj);
        $mensaje = "Creó concepto de impuesto para el impuesto %s (%s) con " .
            "los siguientes parámetros %s";
        $mensaje = sprintf(
            $mensaje,
            $obj->nombre,
            $obj->id, json_encode($request->all())
        );
        $this->log($mensaje, "CREAR");

        $concepto = ConceptoImpuesto::create([
            'impuesto_id' => $obj->id,
            'destino_cuif_id' => $request->cuenta,
            'nombre' => $request->nombreConcepto,
            'tasa' => $request->tasa,
            'esta_activo' => 1,
        ]);

        $item = array(
            "id" => $concepto->id,
            "nombre" => $concepto->nombre,
            "cuenta" => $concepto->cuentaDestino->full,
            "tasa" => number_format($concepto->tasa, 2) . "%",
            "estado" => (bool) $concepto->esta_activo
        );
        return response()->json($item);
    }

    public function alternarEstado(Impuesto $obj, ConceptoImpuesto $concepto)
    {
        $this->objEntidad($obj);
        if ($concepto->impuesto_id != $obj->id) {
            $mensaje = "Concepto no pertenece al impuesto.";
            return response()->json(["error" => $mensaje], 401);
        }
        $mensaje = "%s el concepto '%s' (%s) del impuesto '%s' (%s)";
        $mensaje = sprintf(
            $mensaje,
            !$concepto->esta_activo ? "Activó" : "Inactivó",
            $concepto->nombre,
            $concepto->id,
            $obj->nombre,
            $obj->id
        );
        $this->log($mensaje, "ACTUALIZAR");
        $concepto->esta_activo = !$concepto->esta_activo;
        $concepto->save();
        return response()->json(["estado" => $concepto->esta_activo]);
    }

    private function getTiposImpuestos()
    {
        return [
            "NACIONAL" => "Nacional",
            "DISTRITAL" => "Distrital",
            "REGIONAL" => "Regional",
        ];
    }

    public static function routes() {
        Route::get(
            'impuesto',
            'Contabilidad\ImpuestoController@index'
        );
        Route::get(
            'impuesto/create',
            'Contabilidad\ImpuestoController@create'
        );
        Route::post(
            'impuesto',
            'Contabilidad\ImpuestoController@store'
        );
        Route::get(
            'impuesto/{obj}/edit',
            'Contabilidad\ImpuestoController@edit'
        )->name('impuesto.edit');
        Route::put(
            'impuesto/{obj}',
            'Contabilidad\ImpuestoController@update'
        );
        Route::put(
            'impuesto/{obj}/agregarConcepto',
            'Contabilidad\ImpuestoController@agregarConcepto'
        )->name('impuesto.agregarConcepto');
        Route::put(
            'impuesto/{obj}/{concepto}',
            'Contabilidad\ImpuestoController@alternarEstado'
        );
    }
}

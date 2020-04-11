<?php

namespace App\Http\Controllers\Contabilidad;

use App\Http\Controllers\Controller;
use App\Models\Contabilidad\Impuesto;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Route;

class InformacionTributariaController extends Controller
{
    use FonadminTrait;

    public function __construct() {
        $this->middleware('auth:admin');
        $this->middleware('verEnt');
        $this->middleware('verMenu');
    }

    public function index(Request $request) {
        $this->logActividad("Ingresó a información tributaria", $request);
        $request = $request->validate([
            "name" => "bail|nullable|string|max:250",
            "tipo" => "bail|nullable|string|in:NACIONAL,DISTRITAL,REGIONAL"
        ]);
        $request = (object) $request;
        $impuestos = Impuesto::with('conceptosImpuestos')
            ->entidadId()
            ->search(optional($request)->name)
            ->tipo(optional($request)->tipo)
            ->activo()
            ->paginate();

        return view('contabilidad.informacionTributaria.index')
            ->withTiposImpuestos($this->getTiposImpuestos())
            ->withImpuestos($impuestos);
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
        Route::get('informacionTributaria', 'Contabilidad\InformacionTributariaController@index');
    }
}

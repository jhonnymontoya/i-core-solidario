<?php
namespace App\Http\Controllers\Tarjeta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tarjeta\Tarjetas\CreateTarjetasRequest;
use App\Models\Tarjeta\Tarjeta;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Route;
use Validator;
use Illuminate\Support\Facades\Session;

class TarjetaController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->logActividad("Ingresó a tarjetas", $request);
		$entidad = $this->getEntidad();
		$request = $request->validate([
			'name' => 'bail|nullable|string|max:50',
			'estado' => 'bail|nullable|string|in:DISPONIBLE,ASIGNADA'
		]);
		$tarjetas = Tarjeta::with(
			'tarjetahabientes',
			'tarjetahabientes.tercero',
			'tarjetahabientes.tercero.tipoIdentificacion'
		)->entidadId()->orderBy('numero');
		
		if (isset($request["name"])) {
			$tarjetas = $tarjetas->search($request["name"]);
		}
		if (isset($request["estado"])) {
			switch ($request["estado"]) {
				case 'DISPONIBLE':
					$tarjetas->doesntHave("tarjetahabientes");
					break;
				case 'ASIGNADA':
					$tarjetas->whereHas("tarjetahabientes");
					break;
			}
		}
		$tarjetas = $tarjetas->paginate();
		return view("tarjeta.tarjetas.index")->withTarjetas($tarjetas);
	}

	public function create() {
		$this->log("Ingresó a crear tarjetas");
		$fecha = Carbon::now()->addYears(5)->format('Y/m');
		return view("tarjeta.tarjetas.create")->withFecha($fecha);
	}

	public function store(CreateTarjetasRequest $request) {
		$this->log("Creó tarjetas con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$entidad = $this->getEntidad();

		$tarjetas = array();
		$numeroInicial = intval($request->numeroInicial);
		$numeroFinal = intval($request->numeroFinal);
		list($anio, $mes) = explode("/", $request->fechaVencimiento);
		while ($numeroInicial <= $numeroFinal) {
			if (Tarjeta::validarNumeroTarjeta((string)$numeroInicial)) {
				$tarjeta = new Tarjeta;
				$tarjeta->numero = (string)$numeroInicial;
				$tarjeta->vencimiento_mes = $mes;
				$tarjeta->vencimiento_anio = $anio;
				$tarjetas[] = $tarjeta;
			}
			$numeroInicial++;
		}
		$entidad->tarjetas()->saveMany($tarjetas);
		Session::flash('message', 'Se han registrado \'' . count($tarjetas) . '\' tarjetas.');
		return redirect("tarjetas");
	}

	public function getTarjetas(Request $request) {
		$validator = Validator::make($request->all(),
			['q' => 'bail|nullable|string|min:2|max:19',
			'id' => 'bail|nullable|integer|min:1',],
			[], ['q' => 'consulta']
		);
		if ($validator->fails()) {
			return response()->json($validator->errors(), 422, [], JSON_UNESCAPED_UNICODE);
		}
		$tarjetas = Tarjeta::entidadId()->doesntHave('tarjetahabientes')->orden();

		if (!empty($request->id)) {
			$tarjetas->whereId($request->id);
		}
		if (!empty($request->q)) {
			$q = str_replace("-", "", $request->q);
			$tarjetas->search($q);
		}
		$tarjetas = $tarjetas->take(20)->get();

		$resultado = array('total_count' => $tarjetas->count(), 'incomplete_results' => false);
		$resultado['items'] = array();

		foreach ($tarjetas as $tarjeta) {
			$item = array(
				'id' => $tarjeta->id,
				'numero' => $tarjeta->numero,
				'text' => $tarjeta->numeroFormateado,
				'anioVencimiento' => $tarjeta->vencimiento_anio,
				'mesVencimiento' => $tarjeta->vencimiento_mes,
				'vencimiento' => $tarjeta->vencimiento_mes . '/' .
					$tarjeta->vencimiento_anio
			);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	/**
	 * Registra las rutas del módulo
	 */
	public static function routes() {
		Route::get('tarjetas', 'Tarjeta\TarjetaController@index');
		Route::get('tarjetas/create', 'Tarjeta\TarjetaController@create');
		Route::post('tarjetas', 'Tarjeta\TarjetaController@store');
		Route::get('tarjetas/getTarjetas', 'Tarjeta\TarjetaController@getTarjetas');
	}
}

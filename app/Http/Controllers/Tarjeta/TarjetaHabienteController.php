<?php
namespace App\Http\Controllers\Tarjeta;

use App\Events\Tarjeta\TarjetaHabienteCreado;
use App\Events\Tarjeta\TarjetaHabienteCupoModificado;
use App\Helpers\ConversionHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tarjeta\TarjetaHabiente\CreateTarjetaHabienteRequest;
use App\Http\Requests\Tarjeta\TarjetaHabiente\EditCupoTarjetaHabienteRequest;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Tercero;
use App\Models\Tarjeta\Producto;
use App\Models\Tarjeta\Tarjetahabiente;
use App\Traits\ICoreTrait;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Route;
use Session;

class TarjetaHabienteController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	/**
	 * Punto de entrada al controller
	 * @return type
	 */
	public function index(Request $request) {
		$this->logActividad("Ingresó a tarjetahabiente", $request);
		$req = $request->validate(['name' => 'bail|nullable|string|max:50']);
		$terceros = Tercero::with('tarjetahabientes', 'tarjetahabientes.tarjeta')
			->has('tarjetahabientes');
		if(isset($req["name"])) {
			$terceros = $terceros->search($req["name"]);
			$terceros = $terceros->orWhereHas('tarjetahabientes.tarjeta', function($query) use($req){
				$busqueda = "%" . $req['name'] . "%";
				$query->where("numero", "like", $busqueda);
			});
		}
		$terceros = $terceros->orderBy('nombre')->paginate();
		return view('tarjeta.tarjetahabiente.index')->withTerceros($terceros);
	}

	/**
	 * Muestra el formulario de crear el tarjetahabiente
	 * @return type
	 */
	public function create() {
		$this->log("Ingresó a crear nuevo tarjetahabiente");
		$listaProductos = Producto::entidadId()->activo()->get();
		$productos = array();
		foreach ($listaProductos as $producto) {
			$productos[$producto->id] = $producto->codigo . " - " . $producto->nombre;
		}
		return view('tarjeta.tarjetahabiente.create')->withProductos($productos);
	}

	/**
	 * Guarda el tarjetahabiente
	 * @return type
	 */
	public function store(CreateTarjetaHabienteRequest $request) {
		$this->log("Creó tarjetahabiente con los siguientes parámetros " .  json_encode($request->all()), "CREAR");
		$entidad = $this->getEntidad();

		$producto = Producto::find($request->producto_id);
		$tercero = Tercero::find($request->tercero_id);

		try {
			DB::beginTransaction();
			$tarjetahabiente = new Tarjetahabiente;

			$tarjetahabiente->entidad_id = $entidad->id;
			$tarjetahabiente->producto_id = $request->producto_id;
			$tarjetahabiente->tarjeta_id = $request->tarjeta_id;
			$tarjetahabiente->tercero_id = $request->tercero_id;
			$tarjetahabiente->fecha_asignacion = date('d/m/Y');
			$tarjetahabiente->estado = 'ASIGNADA';

			//Asigna el número de cuenta corriente basado
			//en el número de la tarjeta si el producto tiene
			// modalidad de crédito
			if($producto->credito) {
				$tarjetahabiente->asignarNumeroCuentaCorriente();

				$solicitudCredito = $this->crearObligacionFinanciera($tarjetahabiente, $request);
				$tarjetahabiente->solicitud_credito_id = $solicitudCredito->id;
				$tarjetahabiente->cupo = $request->cupo;
			}

			if($producto->ahorro) {
				$tarjetahabiente->cuenta_ahorro_id = $request->cuenta_ahorro_id;
			}

			if($producto->vista) {
				$tarjetahabiente->numero_cuenta_vista = $tercero->numero_identificacion;
			}
			$tarjetahabiente->save();
			if($entidad->usa_tarjeta) {
				event(new TarjetaHabienteCreado($tarjetahabiente->id));
			}
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Error creando el tarjetahabiente: ' . $e->getMessage());
			abort(500, 'Error creando el tarjetahabiente');
		}
		Session::flash('message', 'Se ha creado el tarjetahabiente');
		return redirect()->route('tarjetaHabiente.edit', [$tarjetahabiente->tercero_id, $tarjetahabiente->id]);
	}

	/**
	 * Obtiene el plazo según la periodicidad del tercero->socio
	 * si no es socio, es mensual
	 * @param type $terceroId
	 * @param type $plazo
	 * @return type
	 */
	private function obtenerPlazo($terceroId, $plazo) {
		$tercero = Tercero::find($terceroId);
		if(!$tercero) {
			return $plazo;
		}
		if(!$tercero->socio) {
			return $plazo;
		}
		$periodicidad = $tercero->socio->pagaduria->periodicidad_pago;
		$plazoPeriodicidad = ConversionHelper::conversionValorPeriodicidad($plazo, "MENSUAL", $periodicidad);
		$plazoPeriodicidad = intval($plazoPeriodicidad);
		return $plazoPeriodicidad;
	}

	/**
	 * Obtiene la periodicidad tercero->socio
	 * si no es socio, es mensual
	 * @param type $terceroId
	 * @param type $plazo
	 * @return type
	 */
	private function obtenerPeriodicidad($terceroId) {
		$tercero = Tercero::find($terceroId);
		if (!$tercero) return "MENSUAL";
		if (!$tercero->socio) return "MENSUAL";
		return $tercero->socio->pagaduria->periodicidad_pago;
	}

	/**
	 * Obtiene la forma de pago
	 * si no es socio, es mensual
	 * @param type $terceroId
	 * @param type $plazo
	 * @return type
	 */
	private function obtenerFormaPago($terceroId) {
		$tercero = Tercero::find($terceroId);
		if (!$tercero) return "CAJA";
		if (!$tercero->socio) return "CAJA";
		return 'NOMINA';
	}

	/**
	 * Crea y devuelve una obligación financiera
	 * @return type
	 */
	public function crearObligacionFinanciera($tarjetahabiente, $request) {
		$usuario = $this->getUser();
		$solicitudCredito = null;
		try {
			$entidad = $this->getEntidad();
			$modalidadCredito = $tarjetahabiente->producto->modalidadCredito;
			$seguroCartera = $modalidadCredito->segurosCartera->first();
			$numeroObligacion = DB::select('select creditos.fn_asignacion_numero_obligacion(?, ?) AS numeroObligacion', [$entidad->id, 0]);
			if (empty($numeroObligacion)) {
				throw new Exception("Error asignando numero de obligación", 1);
			}
			$numeroObligacion = $numeroObligacion[0]->numeroObligacion;
			$solicitudCredito = new solicitudCredito([
				'entidad_id' => $entidad->id,
				'tercero_id' => $request->tercero_id,
				'modalidad_credito_id' => $modalidadCredito->id,
				'seguro_cartera_id' => optional($seguroCartera)->id,
				'valor_credito' => $request->cupo,
				'numero_obligacion' => $numeroObligacion,
				'fecha_solicitud' => date('d/m/Y'),
				'quien_inicio_usuario' => optional($usuario)->usuario,
				'quien_inicio' => optional($usuario)->nombre_corto,
				'quien_radico_usuario' => optional($usuario)->usuario,
				'quien_radico' => optional($usuario)->nombre_corto,
				'fecha_aprobacion' => date('d/m/Y'),
				'quien_aprobo_usuario' => optional($usuario)->usuario,
				'quien_aprobo' => optional($usuario)->nombre_corto,
				'fecha_desembolso' => date('d/m/Y'),
				'quien_desembolso_usuario' => optional($usuario)->usuario,
				'quien_desembolso' => optional($usuario)->nombre_corto,
				'valor_cuota' => 0,
				'plazo' => $this->obtenerPlazo($request->tercero_id, $modalidadCredito->plazo),
				'periodicidad' => $this->obtenerPeriodicidad($request->tercero_id),
				'tipo_pago_intereses' => $modalidadCredito->pago_interes,
				'tipo_amortizacion' => $modalidadCredito->tipo_cuota,
				'tipo_tasa' => $modalidadCredito->tipo_cuota,
				'tasa' => $modalidadCredito->tasa,
				'aplica_mora' => $modalidadCredito->aplica_mora,
				'tasa_mora' => $modalidadCredito->tasa_mora,
				'tipo_garantia' => 'PERSONAL',
				'forma_pago' => $this->obtenerFormaPago($request->tercero_id),
				'calificacion_obligacion' => 'A',
				'estado_solicitud' => 'DESEMBOLSADO',
				'observaciones' => "Crédito rotativo vinculado a tarjeta afinidad",
				'canal' => 'OFICINA'
			]);
			$solicitudCredito->save();
		} catch(Exception $e) {
			Log::error($e->getMessage());
			throw new Exception("Error creando obligación financiera", 1);
		}
		return $solicitudCredito;
	}

	public function show(Tercero $obj) {
		$this->objEntidad($obj);
		$mensaje = "Ingresó a ver los productos -tarjetas- del tercero %s (%s)";
		$this->log(sprintf($mensaje, $obj->id, $obj->nombre));

		$socio = $obj->socio;
		$pagaduria = optional($socio)->pagaduria;
		$tarjetaHabientes = $obj->tarjetahabientes()->with('producto', 'tarjeta')->get();//dd($tarjetaHabientes[0]->producto);
		return view('tarjeta.tarjetahabiente.view')
			->withTercero($obj)
			->withSocio($socio)
			->withPagaduria($pagaduria)
			->withTarjetaHabientes($tarjetaHabientes);
	}

	public function edit(Tercero $obj, Tarjetahabiente $tarjetahabiente) {
		$this->objEntidad($obj);
		$this->objEntidad($tarjetahabiente);
		if ($tarjetahabiente->tercero_id != $obj->id) {
			abort(400, "El producto no pertenece al tercero seleccionado");
		}
		$mensaje = "Ingresó a editar la tarjeta '%s' del tercero %s (%s)";
		$this->log(sprintf($mensaje, $tarjetahabiente->tarjeta->numeroFormateado, $obj->id, $obj->nombre));
		return view('tarjeta.tarjetahabiente.edit')
			->withTercero($obj)
			->withTarjetahabiente($tarjetahabiente);
	}

	public function actualizarCupo(Tarjetahabiente $obj, EditCupoTarjetaHabienteRequest $request) {
		$this->objEntidad($obj);
		$mensaje = "Actualizó el tarjetahabiente %s con los siguientes parámetros %s";
		$mensaje = sprintf($mensaje, $obj->id, json_encode($request->all()));
		$this->log($mensaje, "ACTUALIZAR");

		//Se valida que el producto de la tarjeta esté configurado
		//como mixto o crédito
		if ($obj->producto->modalidad == 'CUENTAAHORROS') {
			Session::flash('error', 'La tarjeta no acepta este tipo de modificación');
			return redirect()->route('tarjetaHabiente.edit', [$obj->tercero->id, $obj->id]);
		}

		//Si se ha cambiado la modalidad del producto, se debe validar que el
		//la solicitud de crédito si exista
		$solicitudDeCredito = $obj->solicitudCredito;
		if (is_null($solicitudDeCredito)) {
			Session::flash('error', 'La tarjeta no acepta este tipo de modificación');
			return redirect()->route('tarjetaHabiente.edit', [$obj->tercero->id, $obj->id]);
		}

		try {
			DB::beginTransaction();
			$solicitudDeCredito->valor_credito = $request->cupo;
			$solicitudDeCredito->save();
			$obj->cupo = $request->cupo;
			$obj->save();
			if($this->getEntidad()->usa_tarjeta) {
				event(new TarjetaHabienteCupoModificado($obj->id));
			}
			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Error al actualizar el cupo de crédito : ' . $e->getMessage());
			Session::flash('error', 'Error al actualizar el cupo de crédito');
			return redirect()->route('tarjetaHabiente.edit', [$obj->tercero->id, $obj->id]);
		}
		Session::flash('message', 'Se ha actualizado el cupo de crédito');
		return redirect()->route('tarjetaHabiente.show', [$obj->tercero->id]);
	}

	/**
	 * Registra las rutas del módulo
	 */
	public static function routes() {
		Route::get('tarjetaHabiente', 'Tarjeta\TarjetaHabienteController@index');
		Route::get('tarjetaHabiente/create', 'Tarjeta\TarjetaHabienteController@create');
		Route::post('tarjetaHabiente', 'Tarjeta\TarjetaHabienteController@store');
		Route::get('tarjetaHabiente/{obj}/show', 'Tarjeta\TarjetaHabienteController@show')->name('tarjetaHabiente.show');
		Route::get('tarjetaHabiente/{obj}/{tarjetahabiente}/edit', 'Tarjeta\TarjetaHabienteController@edit')->name('tarjetaHabiente.edit');

		Route::put('tarjetaHabiente/{obj}/actualizarCupo', 'Tarjeta\TarjetaHabienteController@actualizarCupo')->name('tarjetaHabiente.update.cupo');
	}
}

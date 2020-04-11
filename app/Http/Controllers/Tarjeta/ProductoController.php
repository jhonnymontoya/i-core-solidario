<?php
namespace App\Http\Controllers\Tarjeta;

use App\Http\Controllers\Controller;
use App\Http\Requests\Tarjeta\Producto\CreateProductoRequest;
use App\Http\Requests\Tarjeta\Producto\EditProductoRequest;
use App\Models\Creditos\Modalidad;
use App\Models\Tarjeta\Producto;
use App\Traits\FonadminTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Route;
use Validator;

class ProductoController extends Controller
{
	use FonadminTrait;

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
		$request->validate(['name' => 'bail|nullable|string|min:1|max:50']);
		$this->logActividad("Ingresó a producto tarjeta afinidad", $request);
		$productos = Producto::entidadId()->search($request->name)->paginate();
		return view('tarjeta.producto.index')->withProductos($productos);
	}

	/**
	 * Muestra el formulario de crear productos
	 * @return type
	 */
	public function create() {
		$this->log("Ingresó a producto tarjeta afinidad");
		$periodicidades = array(
			'MENSUAL' => 'Mensual',
			'BIMESTRAL' => 'Bimestral',
			'TRIMESTRAL' => 'Trimestral',
			'CUATRIMESTRAL' => 'Cuatrimestral',
			'SEMESTRAL' => 'Semestral',
			'ANUAL' => 'Anual'
		);
		$modalidadesCredito = Modalidad::entidadId()->usoParaTarjeta(true)->orderBy('nombre')->select('id', 'codigo', 'nombre')->get();
		$modalidades = array();
		foreach ($modalidadesCredito as $modalidad) {
			$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;
		}
		return view('tarjeta.producto.create')->withPeriodicidades($periodicidades)->withModalidades($modalidades);
	}

	/**
	 * Almacena el nuevo producto
	 * @param CreateProductoRequest $request objeto con los parámetros para
	 * crear el nuevo producto 
	 * @return Response
	 */
	public function store(CreateProductoRequest $request) {
		$this->log("Creó producto tarjeta afinidad con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		$producto = new Producto;

		$producto->fill($request->all());
		$producto->entidad_id = $this->getEntidad()->id;

		$producto->save();

		Session::flash('message', "Se ha creado el producto '" . $producto->codigo . "'");
		return redirect('tarjetaProducto');
	}

	/**
	 * Muestra el formulario de edición de producto
	 * @param Producto $obj 
	 * @return view
	 */
	public function edit(Producto $obj){
		$this->log("Ingresó a editar el producto tarjeta afinidad $obj->id");
		$this->objEntidad($obj);
		$periodicidades = array(
			'MENSUAL' => 'Mensual',
			'BIMESTRAL' => 'Bimestral',
			'TRIMESTRAL' => 'Trimestral',
			'CUATRIMESTRAL' => 'Cuatrimestral',
			'SEMESTRAL' => 'Semestral',
			'ANUAL' => 'Anual'
		);
		$modalidadesCredito = Modalidad::entidadId()->usoParaTarjeta(true)->orderBy('nombre')->select('id', 'codigo', 'nombre')->get();
		$modalidades = array();
		foreach ($modalidadesCredito as $modalidad) {
			$modalidades[$modalidad->id] = $modalidad->codigo . ' - ' . $modalidad->nombre;
		}
		return view('tarjeta.producto.edit')
			->withProducto($obj)
			->withPeriodicidades($periodicidades)
			->withModalidades($modalidades);
	}

	public function update(Producto $obj, EditProductoRequest $request) {
		$this->log(
			"Actualizó producto tarjeta afinidad $obj->id con los siguientes " .
				"parámetros " . json_encode($request->all()),
			"ACTUALIZAR"
		);
		$this->objEntidad($obj);
		$obj->fill($request->all());

		$obj->credito = is_null($request->credito) ? false : true;
		$obj->ahorro = is_null($request->ahorro) ? false : true;
		$obj->vista = is_null($request->vista) ? false : true;

		$obj->entidad_id = $this->getEntidad()->id;

		$obj->save();

		Session::flash(
			'message', 'Se ha actualizado el producto \'' . $obj->codigo . '\''
		);
		return redirect('tarjetaProducto');
	}

	public function getProductoConParametros(Request $request) {
		$validator = Validator::make($request->all(),
			[
				'q' => 'bail|nullable|string|min:2|max:100',
				'id' => 'bail|nullable|integer|min:1',
			],
			[],
			[
				'q' => 'consulta'
			]
		);

		if ($validator->fails()) {
			return response() ->json($validator->errors(), 422, [], JSON_UNESCAPED_UNICODE);
		}

		$productos = Producto::entidadId()->activo()->with('modalidadCredito');

		//Con id
		if(!empty($request->id))$productos->whereId($request->id);

		//Búsqueda
		if(!empty($request->q))$productos->search($request->q);

		$productos = $productos->take(20)->get();
		$resultado = array(
			'total_count' => $productos->count(),
			'incomplete_results' => false
		);
		$resultado['items'] = array();

		foreach ($productos as $producto) {
			$modalidad = array(
				'nombre' => $producto->nombre_completo,
				'plazo' => $producto->modalidadCredito->plazo,
				'tasa' => $producto->modalidadCredito->tasa
			);
			$item = array(
				'id' => $producto->id,
				'codigo' => $producto->codigo,
				'nombre' => $producto->nombre,
				'text' => $producto->codigo . ' - ' . $producto->nombre,
				'modalidad' => $producto->modalidad,
				'modalidadCredito' => $modalidad
			);
			array_push($resultado['items'], $item);
		}
		return response()->json($resultado);
	}

	/**
	 * Registra las rutas del módulo
	 */
	public static function routes() {
		Route::get('tarjetaProducto', 'Tarjeta\ProductoController@index');
		Route::get('tarjetaProducto/create', 'Tarjeta\ProductoController@create');
		Route::post('tarjetaProducto', 'Tarjeta\ProductoController@store');
		Route::get('tarjetaProducto/{obj}/edit', 'Tarjeta\ProductoController@edit')->name('tarjetaProductoEdit');
		Route::put('tarjetaProducto/{obj}', 'Tarjeta\ProductoController@update');

		Route::get('tarjetaProducto/getProductoConParametros', 'Tarjeta\ProductoController@getProductoConParametros');
	}
}

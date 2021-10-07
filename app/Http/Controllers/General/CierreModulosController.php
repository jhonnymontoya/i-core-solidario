<?php

namespace App\Http\Controllers\General;

use App\CierreCartera\Ahorros as CierreAhorro;
use App\CierreCartera\Cartera as CierreCartera;
use App\CierreCartera\Contabilidad as CierreContabilidad;
use App\CierreCartera\Socios as CierreSocio;
use App\Events\General\ProcesoCerrado;
use App\Http\Controllers\Controller;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\General\ControlCierreModulo;
use App\Models\General\ControlPeriodoCierre;
use App\Models\Recaudos\Pagaduria;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Route;

class CierreModulosController extends Controller
{
	use ICoreTrait;

	/**
	 * Constructor con middlewares
	 * @return type
	 */
	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	/**
	 * Index general para mostrar los procesos
	 * @param Request $request 
	 * @return type
	 */
	public function index(Request $request) {
		$this->log("Ingresó a control cierre de periodos");
		$controlesPeriodos = ControlPeriodoCierre::entidadId()
			->orderBy("anio", "desc")
			->orderBy("mes", "desc")
			->paginate();
		return view('general.cierreModulos.index')->withPeriodos($controlesPeriodos);
	}

	/**
	 * Muestra el detalle de un periodo
	 * @param ControlPeriodoCierre $obj 
	 * @return type
	 */
	public function detalle(ControlPeriodoCierre $obj) {
		//Se valida que el ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);

		$this->log(sprintf("Ingresó al detalle del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));
		return view('general.cierreModulos.detalle')->withPeriodo($obj);
	}

	/**
	 * Obiene la fecha con la cual comparar los procesos
	 * @param type $obj 
	 * @return type
	 */
	private function obtenerFechaComparacion($obj) {
		return Carbon::createFromDate($obj->anio, $obj->mes, 1)->endOfMonth()->startOfDay();
	}

	/*SOCIOS*/

	/**
	 * Muestra el detalle general para el cierre de socios
	 * @param ControlPeriodoCierre $obj 
	 * @return type
	 */
	public function detalleSocios(ControlPeriodoCierre $obj) {
		//Se valida que el ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);

		$this->log(sprintf("Ingresó al cierre de socios del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));

		//Se valida que el módulo de socios no se encuentre cerrado para el proceso actual
		if($obj->moduloCerrado(10)) {
			Session::flash('error', 'Módulo cerrado para el periodo seleccionado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}

		//Se obtiene la fecha para comparación del proceso
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		$cierre = new CierreSocio($this->getEntidad(), $fechaComparacion);

		//Se obtienen los socios en estado proceso con alertas tipo A,B,C para la fecha de comparación
		$sociosProceso = $cierre->obtenerSociosEnProceso();

		//Se obtienen los socios en estado retiro con alertas tipo A,B,C para la fecha de comparación
		$sociosRetiro = $cierre->obtenerSociosEnRetiro();

		//Se obtienen los socios en estado activo pero sin cuotas obligatorias con alertas tipo A,B,C
		//para la fecha de comparación
		$sociosCuotasObligatorias = $cierre->obtenerSociosSinCuotasObligatorias();

		//Se obtienen los socios con contratos vencidos
		$sociosContratoVencido = $cierre->obtenerSociosConContratosVencido();

		return view('general.cierreModulos.detalleSocios')
				->withPeriodo($obj)
				->withProceso($sociosProceso)
				->withRetiro($sociosRetiro)
				->withCuota($sociosCuotasObligatorias)
				->withContrato($sociosContratoVencido);
	}

	/**
	 * Procesa el cierre de socios para el periodo actual
	 * @param ControlPeriodoCierre $obj 
	 * @return type
	 */
	public function sociosProcesar(ControlPeriodoCierre $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Cerró socios del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio), "CREAR");

		if($obj->moduloCerrado(10)) {
			Session::flash('error', 'Módulo cerrado para el periodo seleccionado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		$sePuedeCerrar = $obj->moduloSePuedeCerrar(10);
		if(!$sePuedeCerrar) {
			Session::flash('error', 'No se puede concluir el proceso ya que el módulo anterior no ha sido cerrado');
			return redirect()->route('cierreModulosDetalleSocios', $obj->id);
		}

		$fechaComparacion = $this->obtenerFechaComparacion($obj);
		//Se valida que el modulo se pueda cerrar de acuerdo a las reglas
		/*$cierre = new CierreSocio($this->getEntidad(), $fechaComparacion);

		if(!$cierre->validoParaCierre()) {
			Session::flash('error', 'No se puede concluir el proceso ya que existen alertas tipo A sin resolver');
			return redirect()->route('cierreModulosDetalleSocios', $obj->id);
		}*/

		$cierreModulo = new ControlCierreModulo;
		$cierreModulo->entidad_id = $this->getEntidad()->id;
		$cierreModulo->control_periodo_cierre_id = $obj->id;
		$cierreModulo->modulo_id = 10;
		$cierreModulo->usuario_id = $this->getUser()->id;
		$cierreModulo->fecha_cierre = $fechaComparacion;

		$cierreModulo->save();
		event(new ProcesoCerrado($obj->id));
		Session::flash('message', 'Ha sido procesado con éxito el cierre del módulo Socios');
		return redirect()->route('cierreModulosDetalle', $obj->id);
	}
	/*FIN SOCIOS*/


	/*AHORROS*/

	/**
	 * Detalle de cierre de socios
	 * @return type
	 */
	public function detalleAhorros(ControlPeriodoCierre $obj) {
		//Se valida que el objeto ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó al cierre de ahorros del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));

		//Se obtiene la fecha de comparación
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		$cierre = new CierreAhorro($this->getEntidad(), $fechaComparacion);

		//Se obtienen los ahorros negativos
		$ahorrosNegativos = $cierre->obtenerAhorrosNegativos();

		//Se obtinen los socios liquidados con ahorros en saldo
		$obtenerSociosLiquidadosConAhorros = $cierre->obtenerSociosLiquidadosConAhorros();

		//Se obtienen diferencias entre movimientos de contabilidad y ahorros
		$diferenciasAhorrosContabilidad = $cierre->obtenerDiferenciaAhorrosContabilidad();

		//Se obtienen los socios sin ahorros
		$sociosSinAhorros = $cierre->obtenerSociosSinAhorros();

		//Se obtienen los socios sin movimientos de ahorros en más de 30 días
		$sociosSinMovimientosEnTiempo = $cierre->obtenerSociosSinMovimientosAhorrosEnTiempo();

		//Se obtienen socios sin aportes
		$sociosSinAportes = $cierre->obtenerSociosSinAportes();

		//Obtiene array de socios con aportes en un límite del 10% por entidad
		$sociosAportesLimite = $cierre->obtenerSociosConAportesLimite();

		return view('general.cierreModulos.detalleAhorros')
				->withPeriodo($obj)
				->withAhorrosNegativos($ahorrosNegativos)
				->withLiquidadosConAhorros($obtenerSociosLiquidadosConAhorros)
				->withDiferenciasAhorrosContabilidad($diferenciasAhorrosContabilidad)
				->withSinAhorros($sociosSinAhorros)
				->withSinMovimientosEnTiempo($sociosSinMovimientosEnTiempo)
				->withSinAportes($sociosSinAportes)
				->withAportesLimites($sociosAportesLimite);
	}

	/**
	 * Procesa el cierre de ahorros para el periodo actual
	 * @param ControlPeriodoCierre $obj 
	 * @return type
	 */
	public function ahorrosProcesar(ControlPeriodoCierre $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Cerró ahorros del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio), "CREAR");

		if($obj->moduloCerrado(6)) {
			Session::flash('error', 'Módulo cerrado para el periodo seleccionado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		$sePuedeCerrar = $obj->moduloSePuedeCerrar(6);
		if(!$sePuedeCerrar) {
			Session::flash('error', 'No se puede concluir el proceso ya que el módulo anterior no ha sido cerrado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}

		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		//Se valida que el modulo se pueda cerrar de acuerdo a las reglas
		/*$cierre = new CierreAhorro($this->getEntidad(), $this->obtenerFechaComparacion($obj));

		if(!$cierre->validoParaCierre()) {
			Session::flash('error', 'No se puede concluir el proceso ya que existen alertas tipo A sin resolver');
			return redirect()->route('cierreModulosDetalleAhorros', $obj->id);
		}*/

		$res = DB::select("exec ahorros.sp_liquidacion_rendimientos ?", [$obj->id]);
		if(empty($res)) {
			Session::flash('error', 'Error al liquidar los rendimientos');
			return redirect()->route('cierreModulosDetalleAhorros', $obj->id);
		}

		$res = $res[0];

		if($res->ERROR == 1) {
			Session::flash('error', $res->MENSAJE);
			return redirect()->route('cierreModulosDetalleAhorros', $obj->id);
		}

		$cierreModulo = new ControlCierreModulo;
		$cierreModulo->entidad_id = $this->getEntidad()->id;
		$cierreModulo->control_periodo_cierre_id = $obj->id;
		$cierreModulo->modulo_id = 6;
		$cierreModulo->usuario_id = $this->getUser()->id;
		$cierreModulo->fecha_cierre = $fechaComparacion;

		$cierreModulo->save();
		event(new ProcesoCerrado($obj->id));

		Session::flash(
			'message',
			'Ha sido procesado con éxito el cierre del módulo Ahorros'
		);

		if (empty($res->CODIGOCOMPROBANTE) == false) {
            Session::flash('codigoComprobante', $res->CODIGOCOMPROBANTE);

            Session::flash('numeroComprobante', $res->NUMEROCOMPROBANTE);
        }

		return redirect()->route('cierreModulosDetalle', $obj->id);
	}

	/*FIN AHORROS*/

	/*CARTERA*/

	/**
	 * Detalle de cierre de cartera
	 * @return type
	 */
	public function detalleCartera(ControlPeriodoCierre $obj) {
		//Se valida que el objeto ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);

		$this->log(sprintf("Ingresó al cierre de cartera del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));

		//Se obtiene la fecha de comparación
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		$cierre = new CierreCartera($this->getEntidad(), $fechaComparacion);

		//Se obtienen los créditos con días de vencimiento entre de 30 y 60 (B, C)
		$carteraDiasVencidos = $cierre->obtenerCarteraDiasVencido();

		//Se obtiene los creditos sin definir (C)
		$creditosSinDefinir = $cierre->obtenerCreditosSinDefinir();

		//Se obtiene los créditos con saldo negativo (A)
		$creditosSaldoNegativo = $cierre->obtenerCarteraSaldoNegativo();

		//Se obtiene los créditos sin amortización (A)
		$creditosSinAmortizacion = $cierre->obtenerCarteraSinAmortizacion();

		//Se obtiene los creditos con saldo y estado diferente a desembolsado (A)
		$creditosSaldoEstadoDiferenteDesembolso = $cierre->obtenerCarteraConSaldoEstadoDiferenteDesembolsado();

		//Se obtiene la diferencia entre cartera y contabilidad (A)
		$diferenciaCarteraContabilidad = $cierre->obtenerDiferenciaCarteraContabilidad();

		return view('general.cierreModulos.detalleCartera')
				->withPeriodo($obj)
				->withCarteraDiasVencidos($carteraDiasVencidos)
				->withCreditosSinDefinir($creditosSinDefinir)
				->withCreditosSaldoNegativo($creditosSaldoNegativo)
				->withCreditosSinAmortizacion($creditosSinAmortizacion)
				->withCreditosSaldoEstadoDiferenteDesembolso($creditosSaldoEstadoDiferenteDesembolso)
				->withDiferenciaCarteraContabilidad($diferenciaCarteraContabilidad);
	}

	public function carteraPrecierre(ControlPeriodoCierre $obj) {
		//Se valida que el objeto ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó al pre cierre de cartera del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));

		//Log
		$this->log("Ingresó a precierre de cartera del periodo " . $obj->id);

		//Se obtiene la entidad
		$entidad = $this->getEntidad();

		//Se obtiene la fecha de comparación
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		$pagadurias = Pagaduria::entidadId()->pluck('nombre', 'id');
		$preCierre = DB::select("EXEC creditos.sp_precierre_cartera ?, ?", [$entidad->id, $fechaComparacion]);

		return view('general.cierreModulos.detallePrecierreCartera')
				->withPeriodo($obj)
				->withPreCierre($preCierre)
				->withPagadurias($pagadurias);
	}

	/**
	 * Procesa el cierre de créditos para el periodo actual
	 * @param ControlPeriodoCierre $obj 
	 * @return type
	 */
	public function carteraProcesar(ControlPeriodoCierre $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Cerró cartera del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio), "CREAR");

		if($obj->moduloCerrado(7)) {
			Session::flash('error', 'Módulo cerrado para el periodo seleccionado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		$sePuedeCerrar = $obj->moduloSePuedeCerrar(7);
		if(!$sePuedeCerrar) {
			Session::flash('error', 'No se puede concluir el proceso ya que el módulo anterior no ha sido cerrado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}

		//Se obtiene la fecha de comparación
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		//Se valida que el modulo se pueda cerrar de acuerdo a las reglas
		/*$cierre = new CierreCartera($this->getEntidad(), $this->obtenerFechaComparacion($obj));

		if(!$cierre->validoParaCierre()) {
			Session::flash('error', 'No se puede concluir el proceso ya que existen alertas tipo A sin resolver');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}*/

		$res = DB::select("exec creditos.sp_procesar_cierre_cartera ?, ?, ?", [$this->getEntidad()->id, $fechaComparacion, $this->getUser()->id]);
		if(empty($res)) {
			Session::flash('error', 'Error al procesar el cierre de cartera');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		if($res[0]->ERROR == 1) {
			Session::flash('error', $res[0]->MENSAJE);
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		Session::flash('message', $res[0]->MENSAJE);
		event(new ProcesoCerrado($obj->id));
		return redirect()->route('cierreModulosDetalle', $obj->id);
	}

	/*FIN CARTERA*/

	/*INICIO CONTABILIDAD*/

	/**
	 * Muestra el detalle del cierre de contabilidad antes de cerrar
	 * @param ControlPeriodoCierre $obj
	 * @return type
	 */
	public function detalleContabilidad(ControlPeriodoCierre $obj) {
		//Se valida que el objeto ControlPeriodoCierre pertenezca a la entidad actual
		$this->objEntidad($obj);

		$this->log(sprintf("Ingresó al cierre de contabilidad del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio));

		//Se obtiene la fecha de comparación
		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		$cierre = new CierreContabilidad($this->getEntidad(), $fechaComparacion);

		//Se obtienen los comprobantes descuadrados (A)
		$comprobantesDescuadrados = $cierre->obtenerComprobantesDescuadrados();

		//Se obtienen comprobantes con impuestos en estado borrador (B)
		$comprobantesBorradorConImpuesto = $cierre->obtenerComprobantesBorradorConImpuesto();

		//Se obtienen comprobantes en borrador (C)
		$comprobantesBorrador = $cierre->obtenerComprobantesBorrador();

		return view('general.cierreModulos.detalleContabilidad')
			->withPeriodo($obj)
			->withComprobantesDescuadrados($comprobantesDescuadrados)
			->withComprobantesBorradorConImpuesto($comprobantesBorradorConImpuesto)
			->withComprobantesBorrador($comprobantesBorrador);
	}

	/**
	 * Cierra el proceso de contabilidad
	 * @param ControlPeriodoCierre $obj
	 * @return type
	 */
	public function contabilidadProcesar(ControlPeriodoCierre $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Cerró contabilidad del periodo %s %s-%s", $obj->id, $obj->mes, $obj->anio), "CREAR");

		if($obj->moduloCerrado(2)) {
			Session::flash('error', 'Módulo cerrado para el periodo seleccionado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}
		$sePuedeCerrar = $obj->moduloSePuedeCerrar(2);
		if(!$sePuedeCerrar) {
			Session::flash('error', 'No se puede concluir el proceso ya que el módulo anterior no ha sido cerrado');
			return redirect()->route('cierreModulosDetalle', $obj->id);
		}

		$fechaComparacion = $this->obtenerFechaComparacion($obj);

		//Se valida que el modulo se pueda cerrar de acuerdo a las reglas
		/*$cierre = new CierreContabilidad($this->getEntidad(), $this->obtenerFechaComparacion($obj));

		if(!$cierre->validoParaCierre()) {
			Session::flash('error', 'No se puede concluir el proceso ya que existen alertas tipo A sin resolver');
			return redirect()->route('cierreModulosDetalleAhorros', $obj->id);
		}*/

		//Se eliminan comprobantes en borrador
		$fecha = Carbon::createFromDate($obj->anio, $obj->mes, 1)->endOfMonth()->endOfDay();
		$movimientos = MovimientoTemporal::entidadId()->where("fecha_movimiento", "<=", $fecha)->get();
		foreach($movimientos as $movimiento)$movimiento->delete();

		$cierreModulo = new ControlCierreModulo;
		$cierreModulo->entidad_id = $this->getEntidad()->id;
		$cierreModulo->control_periodo_cierre_id = $obj->id;
		$cierreModulo->modulo_id = 2;
		$cierreModulo->usuario_id = $this->getUser()->id;
		$cierreModulo->fecha_cierre = $fechaComparacion;

		$cierreModulo->save();
		event(new ProcesoCerrado($obj->id));
		Session::flash('message', 'Ha sido procesado con éxito el cierre del módulo Contabilidad');
		return redirect()->route('cierreModulosDetalle', $obj->id);
	}
	/*FIN CONTABILIDAD*/

	public static function routes() {
		Route::get('cierreModulos', 'General\CierreModulosController@index');
		Route::get('cierreModulos/{obj}', 'General\CierreModulosController@detalle')->name('cierreModulosDetalle');
		
		Route::get('cierreModulos/{obj}/socios', 'General\CierreModulosController@detalleSocios')->name('cierreModulosDetalleSocios');
		//Route::get('cierreModulos/{obj}/socios/{tipoAlerta}/proceso', 'General\CierreModulosController@detalleSociosProceso')->name('cierreModulosDetalleSociosProceso');
		Route::put('cierreModulos/{obj}/socios/procesar', 'General\CierreModulosController@sociosProcesar')->name('cierreModulosSociosProcesar');

		Route::get('cierreModulos/{obj}/ahorros', 'General\CierreModulosController@detalleAhorros')->name('cierreModulosDetalleAhorros');
		Route::put('cierreModulos/{obj}/ahorros/procesar', 'General\CierreModulosController@ahorrosProcesar')->name('cierreModulosAhorrosProcesar');

		Route::get('cierreModulos/{obj}/cartera', 'General\CierreModulosController@detalleCartera')->name('cierreModulosDetalleCartera');
		Route::get('cierreModulos/{obj}/cartera/precierre', 'General\CierreModulosController@carteraPrecierre')->name('cierreModulos.cartera.precierre');
		Route::put('cierreModulos/{obj}/cartera/procesar', 'General\CierreModulosController@carteraProcesar')->name('cierreModulosCarteraProcesar');

		Route::get('cierreModulos/{obj}/contabilidad', 'General\CierreModulosController@detalleContabilidad')->name('cierreModulosDetalleContabilidad');
		Route::put('cierreModulos/{obj}/contabilidad/procesar', 'General\CierreModulosController@contabilidadProcesar')->name('cierreModulosContabilidadProcesar');
	}
}

<?php

namespace App\Http\Controllers\Ahorros;

use App\Helpers\FinancieroHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Ahorros\SDAT\ConstituirSDATRequest;
use App\Http\Requests\Ahorros\SDAT\CreateSDATRequest;
use App\Http\Requests\Ahorros\SDAT\SaldarSDATRequest;
use App\Models\Ahorros\MovimientoSDAT;
use App\Models\Ahorros\RendimientoSDAT;
use App\Models\Ahorros\SDAT;
use App\Models\Ahorros\TipoSDAT;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\DetalleMovimientoTemporal;
use App\Models\Contabilidad\Impuesto;
use App\Models\Contabilidad\Movimiento;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\ParametroInstitucional;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Exception;
use Illuminate\Http\Request;
use Log;
use Route;
use Session;

class SDATController extends Controller
{
	use ICoreTrait;

	public function __construct() {
		$this->middleware('auth:admin');
		$this->middleware('verEnt');
		$this->middleware('verMenu');
	}

	public function index(Request $request) {
		$this->log("Ingresó a SDAT");
		$SDATs = SDAT::entidadId()
			->search($request->name)
			->estado($request->estado)
			->orderBy('estado', 'asc')
			->orderBy('fecha_constitucion', 'desc')
			->paginate();
		$estados = [
			'SOLICITUD' => 'Solicitud',
			'CONSTITUIDO' => 'Constituido',
			'RENOVADO' => 'Renovado',
			'PRORROGADO' => 'Prorrogado',
			'SALDADO' => 'Saldado',
			'ANULADO' => 'Anulado'
		];
		return view('ahorros.SDAT.index')
			->withSdats($SDATs)
			->withEstados($estados);
	}

	public function create() {
		$this->log("Ingresó a la creación de SDAT");
		$tiposSDAT = TipoSDAT::entidadId()
			->orderBy('nombre')
			->pluck('nombre', 'id');
		return view('ahorros.SDAT.create')->withTiposSDAT($tiposSDAT);
	}

	public function store(CreateSDATRequest $request) {
		$radicar = empty($request->radicar) ? false : true;
		if($this->moduloCerrado(2, $request->fecha)) {
			Session::flash("error", "Módulo de contabilidad cerrado para la fecha de radicación");
			return redirect("SDAT");
		}
		if($this->moduloCerrado(6, $request->fecha)) {
			Session::flash("error", "Módulo de ahorros cerrado para la fecha de radicación");
			return redirect("SDAT");
		}
		if($radicar) {
			$this->log("Radicó SDAT con los siguientes parámetros " . json_encode($request->all()), "CREAR");
		}

		$tipoSDAT = TipoSDAT::find($request->tipo_sdat);
		$condicion = null;
		$baseRetefuente = ParametroInstitucional::entidadId()->codigo('AH001')->first();
		if(empty($baseRetefuente)) {
			Session::flash("error", "Parámetro institucional AH001 no configurado.");
			return redirect('SDAT/create')->withInput();
		}
		$porcentajeRetefuente = ParametroInstitucional::entidadId()->codigo('AH002')->first();
		if(empty($porcentajeRetefuente)) {
			Session::flash("error", "Parámetro institucional AH002 no configurado.");
			return redirect('SDAT/create')->withInput();
		}
		try {
			$condicion = $tipoSDAT->obtenerCondicionPlazoMonto($request->plazo, $request->valor);
		}
		catch(Exception $e){
			/*
				1 = Plazo no configurado
				2 = Monto no configurado
			*/
			$error = [];
			if($e->getCode() == 1) {
				$error = ["plazo" => $e->getMessage()];
			}
			elseif($e->getCode() == 2) {
				$error = ["valor" => $e->getMessage()];
			}
			return redirect('SDAT/create')
				->withInput()
				->withErrors($error);
		}

		$data = collect();

		$tasa = FinancieroHelper::efectivaToNominal($condicion->tasa / 100, 'ANUAL', 'MENSUAL') * 100;
		$data->put("tasaEA", $condicion->tasa);
		$data->put("tasa", $tasa);

		$fechaConstitucion = Carbon::createFromFormat("d/m/Y", $request->fecha)->startOfDay();
		$data->put("fechaVencimiento", $fechaConstitucion->copy()->addDays($request->plazo));

		$interesEstimado = ((($tasa / 100) / 30) * $request->plazo) * $request->valor;
		$data->put("interesEstimado", $interesEstimado);

		$interesEstimadoDiario = $interesEstimado / $request->plazo;

		$retefuenteEstimado = 0;
		if($interesEstimadoDiario > $baseRetefuente->valor) {
			$retefuenteEstimado = $interesEstimado * ($porcentajeRetefuente->valor / 100);
		}
		$data->put("retefuenteEstimado", $retefuenteEstimado);

		$total = ($request->valor + $interesEstimado) - $retefuenteEstimado;
		$data->put("total", $total);

		if($radicar) {
			$sdat = new SDAT;

			$sdat->entidad_id = $this->getEntidad()->id;
			$sdat->tipo_sdat_id = $request->tipo_sdat;
			$sdat->socio_id = $request->socio;
			$sdat->valor = $request->valor;
			$sdat->fecha_constitucion = $request->fecha;
			$sdat->fecha_vencimiento = $data["fechaVencimiento"];
			$sdat->plazo = $request->plazo;
			$sdat->tasa = $condicion->tasa;
			$sdat->intereses_estimados = $interesEstimado;
			$sdat->retefuente_estimada = $retefuenteEstimado;
			$sdat->estado = 'SOLICITUD';

			$sdat->save();

			Session::flash("message", "Se ha creado el SDAT");
			return redirect('SDAT');
		}
		Session::flash("dataTitulo", $data);
		return redirect('SDAT/create')->withInput();
	}

	public function getConstituir(SDAT $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó a constituir el SDAT '%s'", $obj->id));
		return view('ahorros.SDAT.constituir')->withSdat($obj);
	}

	public function putConstituir(SDAT $obj, ConstituirSDATRequest $request) {
		$this->objEntidad($obj);
		if($this->moduloCerrado(2, $request->fecha)) {
			Session::flash("error", "Módulo de contabilidad cerrado para la fecha de radicación");
			return redirect("SDAT");
		}
		if($this->moduloCerrado(6, $request->fecha)) {
			Session::flash("error", "Módulo de ahorros cerrado para la fecha de radicación");
			return redirect("SDAT");
		}
		$this->log(sprintf("Ingresó a constituir el SDAT '%s'", $obj->id));

		$tipoComprobante = TipoComprobante::entidadId()
			->uso('PROCESO')
			->whereCodigo("SDAT")
			->first();
		if(!$tipoComprobante) {
			Session::flash("error", "No se encontró el tipo de comprobante contable 'SDAT'");
			return redirect("SDAT");
		}
		$tercero = $obj->socio->tercero;
		$tipoSDAT = $obj->tipoSdat;
		try {
			DB::beginTransaction();
			$movimiento = new MovimientoTemporal;
			$movimiento->entidad_id = $this->getEntidad()->id;
			$movimiento->fecha_movimiento = $obj->fecha_constitucion;
			$movimiento->tipo_comprobante_id = $tipoComprobante->id;
			$desc = "Constitución de SDAT Número '%s' para %s por valor de $%s";
			$desc = sprintf($desc, $obj->id, $tercero->nombre_completo, number_format($obj->valor));
			$movimiento->descripcion = $desc;
			$movimiento->origen = 'PROCESO';

			$movimiento->save();

			$detalles = [];
			$detalle = new DetalleMovimientoTemporal;
			$detalle->entidad_id = $this->getEntidad()->id;
			$detalle->codigo_comprobante = $tipoComprobante->codigo;
			$detalle->setTercero($tercero);
			$detalle->setCuif($tipoSDAT->capitalCuif);
			$detalle->debito = 0;
			$detalle->credito = $obj->valor;
			$detalle->serie = 1;
			$detalle->fecha_movimiento = $obj->fecha_constitucion;
			$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
			$detalle->referencia = $codigo;
			$detalles[] = $detalle;

			$detalle = new DetalleMovimientoTemporal;
			$detalle->entidad_id = $this->getEntidad()->id;
			$detalle->codigo_comprobante = $tipoComprobante->codigo;
			$detalle->setTercero($tercero);
			$detalle->setCuif(Cuif::find($request->cuenta));
			$detalle->debito = $obj->valor;;
			$detalle->credito = 0;
			$detalle->serie = 2;
			$detalle->fecha_movimiento = $obj->fecha_constitucion;
			$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
			$detalle->referencia = null;
			$detalles[] = $detalle;

			$movimiento->detalleMovimientos()->saveMany($detalles);
			$respuesta = DB::select('exec ahorros.sp_contabilizar_constitucion_sdat ?, ?', [$obj->id, $movimiento->id]);

			$respuesta = $respuesta[0];

			if($respuesta->ERROR == 1) {
				DB::rollBack();
				Session::flash('error', $respuesta[0]->MENSAJE);
				return redirect("SDAT");
			}

			DB::commit();
		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Mensaje de error: ' . $e->getMessage());
			Session::flash('error', $respuesta->MENSAJE);
			return redirect("SDAT");
		}
		Session::flash(
			"message",
			"Se ha constituido el SDAT número '$obj->id' con éxito."
		);

		if (empty($respuesta->CODIGOCOMPROBANTE) == false) {
            Session::flash(
                'codigoComprobante',
                $respuesta->CODIGOCOMPROBANTE
            );

            Session::flash(
                'numeroComprobante',
                $respuesta->NUMEROCOMPROBANTE
            );
        }
		return redirect("SDAT");
	}

	public function getSaldar(SDAT $obj) {
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó a saldar el SDAT '%s'", $obj->id));
		return view('ahorros.SDAT.preSaldar')->withSdat($obj);
	}

	public function putPreSaldar(SDAT $obj, SaldarSDATRequest $request) {
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó al pre saldo del SDAT '%s'", $obj->id));

		$fechaDevolucion = Carbon::createFromFormat("d/m/Y", $request->fechaDevolucion)
			->startOfDay();

		$cuenta = Cuif::find($request->cuenta);

		$sql = "exec ahorros.sp_calculo_devolucion_SDAT ?, ?";
		$res = DB::select($sql, [$obj->id, $fechaDevolucion]);

		$res = $res ? $res[0] : null;

		if($fechaDevolucion->lessThan($obj->fecha_vencimiento)) {
			Session::flash("error", "La fecha de devolución es menor a la fecha de vencimiento.");
		}

		return view('ahorros.SDAT.saldar')
			->withSdat($obj)
			->withFechaDevolucion($fechaDevolucion)
			->withCuenta($cuenta)
			->withDatosDevolucion($res);
	}

	public function putSaldar(SDAT $obj, SaldarSDATRequest $request) {
		$this->objEntidad($obj);
		$this->log(sprintf("Ingresó al pre saldo del SDAT '%s'", $obj->id));

		$fechaDevolucion = Carbon::createFromFormat("d/m/Y", $request->fechaDevolucion)
			->startOfDay();

		$cuenta = Cuif::find($request->cuenta);

		$tipoComprobante = TipoComprobante::entidadId()
			->uso('PROCESO')
			->whereCodigo("SDAT")
			->first();

		if(!$tipoComprobante) {
			Session::flash("error", "No se encontró el tipo de comprobante contable 'SDAT'");
			return redirect("SDAT");
		}

		$cuentaRetefuente = ParametroInstitucional::entidadId($this->getEntidad()->id)
				->codigo('AH003')
				->first();
		if(!$cuentaRetefuente) {
			Session::flash("error", "No se encontró el parámetro institucional 'AH003'");
			return redirect("SDAT");
		}
		$cuentaRetefuente = Cuif::entidadId()->whereCodigo((int)$cuentaRetefuente->valor)->first();

		$porcentajeRetefuente = ParametroInstitucional::entidadId($this->getEntidad()->id)
				->codigo('AH002')
				->first();
		if(!$porcentajeRetefuente) {
			Session::flash("error", "No se encontró el parámetro institucional 'AH002'");
			return redirect("SDAT");
		}

		$tercero = $obj->socio->tercero;
		$tipoSDAT = $obj->tipoSdat;

		$sql = "exec ahorros.sp_calculo_devolucion_SDAT ?, ?";
		$res = DB::select($sql, [$obj->id, $fechaDevolucion]);

		$res = $res ? $res[0] : null;
		try {
			DB::beginTransaction();
			$movimiento = new MovimientoTemporal;
			$movimiento->entidad_id = $this->getEntidad()->id;
			$movimiento->fecha_movimiento = $fechaDevolucion;
			$movimiento->tipo_comprobante_id = $tipoComprobante->id;
			$desc = "Devolución de deposito SDAT Número '%s' para %s";
			$desc = sprintf($desc, $obj->id, $tercero->nombre_completo);
			$movimiento->descripcion = $desc;
			$movimiento->origen = 'PROCESO';

			$movimiento->save();
			$serie = 1;

			$detalles = [];
			$detalle = new DetalleMovimientoTemporal;
			$detalle->entidad_id = $this->getEntidad()->id;
			$detalle->codigo_comprobante = $tipoComprobante->codigo;
			$detalle->setTercero($tercero);
			$detalle->setCuif($tipoSDAT->capitalCuif);
			$detalle->debito = $res->saldo;
			$detalle->credito = 0;
			$detalle->serie = $serie;
			$detalle->fecha_movimiento = $fechaDevolucion;
			$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
			$detalle->referencia = $codigo;
			$detalles[] = $detalle;
			$serie++;

			if($res->interes_causado != 0) {
				$detalle = new DetalleMovimientoTemporal;
				$detalle->entidad_id = $this->getEntidad()->id;
				$detalle->codigo_comprobante = $tipoComprobante->codigo;
				$detalle->setTercero($tercero);
				$detalle->setCuif($tipoSDAT->interesesPorPagarCuif);
				$detalle->debito = $res->interes_causado;
				$detalle->credito = 0;
				$detalle->serie = $serie;
				$detalle->fecha_movimiento = $fechaDevolucion;
				$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
				$detalle->referencia = $codigo;
				$detalles[] = $detalle;
				$serie++;
			}

			if($res->interes_pendiente != 0) {
				$detalle = new DetalleMovimientoTemporal;
				$detalle->entidad_id = $this->getEntidad()->id;
				$detalle->codigo_comprobante = $tipoComprobante->codigo;
				$detalle->setTercero($tercero);
				$detalle->setCuif($tipoSDAT->interesesCuif);
				$detalle->debito = $res->interes_pendiente;
				$detalle->credito = 0;
				$detalle->serie = $serie;
				$detalle->fecha_movimiento = $fechaDevolucion;
				$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
				$detalle->referencia = $codigo;
				$detalles[] = $detalle;
				$serie++;
			}

			if($res->retefuente_pendiente != 0) {
				$detalle = new DetalleMovimientoTemporal;
				$detalle->entidad_id = $this->getEntidad()->id;
				$detalle->codigo_comprobante = $tipoComprobante->codigo;
				$detalle->setTercero($tercero);
				$detalle->setCuif($cuentaRetefuente);
				$detalle->debito = 0;
				$detalle->credito = $res->retefuente_pendiente;
				$detalle->serie = $serie;
				$detalle->fecha_movimiento = $fechaDevolucion;
				$codigo = sprintf("%s-%s", $tipoSDAT->codigo, $obj->id);
				$detalle->referencia = $codigo;
				$detalles[] = $detalle;
				$serie++;
			}

			//CONTRAPARTIDA
			$detalle = new DetalleMovimientoTemporal;
			$detalle->entidad_id = $this->getEntidad()->id;
			$detalle->codigo_comprobante = $tipoComprobante->codigo;
			$detalle->setTercero($tercero);
			$detalle->setCuif($cuenta);
			$detalle->debito = 0;
			$detalle->credito = $res->total_devolucion;
			$detalle->serie = $serie;
			$detalle->fecha_movimiento = $fechaDevolucion;
			$detalles[] = $detalle;

			$movimiento->detalleMovimientos()->saveMany($detalles);
			$respuesta = DB::select('exec ahorros.sp_contabilizar_devolucion_sdat ?, ?', [$obj->id, $movimiento->id]);
			$respuesta = $respuesta[0];

			if($respuesta->ERROR == 1) {
				DB::rollBack();
				Session::flash('error', $respuesta->MENSAJE);
				return redirect("SDAT");
			}

			$idContabilizado = (int)$respuesta->MENSAJE;
			$mc = Movimiento::find($idContabilizado);

			$movimientoSDAT = new MovimientoSDAT;
			$movimientoSDAT->sdat_id = $obj->id;
			$movimientoSDAT->movimiento_id = $idContabilizado;
			$movimientoSDAT->fecha_movimiento = $fechaDevolucion;
			$movimientoSDAT->valor = -$res->saldo;
			$movimientoSDAT->save();

			if($res->interes_pendiente != 0) {
				$rendimiento = new RendimientoSDAT;
				$rendimiento->entidad_id = $this->getEntidad()->id;
				$rendimiento->socio_id = $obj->socio->id;
				$rendimiento->sdat_id = $obj->id;
				$rendimiento->movimiento_id = $idContabilizado;
				$rendimiento->valor = $res->interes_pendiente;
				$rendimiento->fecha_movimiento = $fechaDevolucion;
				$rendimiento->save();
			}

			if($res->interes_total != 0) {
				$rendimiento = new RendimientoSDAT;
				$rendimiento->entidad_id = $this->getEntidad()->id;
				$rendimiento->socio_id = $obj->socio->id;
				$rendimiento->sdat_id = $obj->id;
				$rendimiento->movimiento_id = $idContabilizado;
				$rendimiento->valor = -$res->interes_total;
				$rendimiento->fecha_movimiento = $fechaDevolucion;
				$rendimiento->save();
			}

			if($res->retefuente_pendiente != 0) {
				$impuesto = Impuesto::entidadId()->whereNombre('RETEFUENTE')->first();
				$concepto = $impuesto->conceptosImpuestos()->whereNombre('RENDIMIENTOS FINANCIEROS')->first();

				$movimiento = new MovimientoImpuesto;
				$movimiento->entidad_id = $this->getEntidad()->id;
				$movimiento->movimiento_id = $idContabilizado;
				$movimiento->setTercero($tercero);
				$movimiento->fecha_movimiento = $fechaDevolucion;
				$movimiento->impuesto_id = $impuesto->id;
				$movimiento->concepto_impuesto_id = $concepto->id;
				$movimiento->setCuif($cuentaRetefuente);
				$movimiento->base = $res->interes_pendiente;
				$movimiento->tasa = $porcentajeRetefuente->valor;
				$movimiento->iva = 0;
				$movimiento->save();
			}
			DB::commit();
			$msg = "Se ha saldado con exito el SDAT '%s' de %s con el documento '%s'";
			$msg = sprintf(
				$msg,
				$obj->id,
				$tercero->nombre_corto,
				$mc->tipoComprobante->codigo . '-' . $mc->numero_comprobante
			);
			Session::flash('message', $msg);

			if (empty($respuesta->CODIGOCOMPROBANTE) == false) {
                Session::flash(
                    'codigoComprobante',
                    $respuesta->CODIGOCOMPROBANTE
                );

                Session::flash(
                    'numeroComprobante',
                    $respuesta->NUMEROCOMPROBANTE
                );
            }

		} catch(Exception $e) {
			DB::rollBack();
			Log::error('Mensaje de error: ' . $e->getMessage());
			$msg = "Error al saldar el SDAT";
			Session::flash('error', $msg);
		}
		return redirect('SDAT');
	}

	public static function routes() {
		Route::get('SDAT', 'Ahorros\SDATController@index');
		Route::get('SDAT/create', 'Ahorros\SDATController@create');
		Route::post('SDAT', 'Ahorros\SDATController@store');
		Route::get('SDAT/{obj}/edit', 'Ahorros\SDATController@edit')->name('SDAT.edit');
		Route::put('SDAT/{obj}', 'Ahorros\SDATController@update');

		Route::get('SDAT/{obj}/constituir', 'Ahorros\SDATController@getConstituir')->name('SDAT.constituir');
		Route::put('SDAT/{obj}/constituir', 'Ahorros\SDATController@putConstituir')->name('SDAT.put.constituir');

		Route::get('SDAT/{obj}/preSaldar', 'Ahorros\SDATController@getSaldar')->name('SDAT.saldar');
		Route::put('SDAT/{obj}/preSaldar', 'Ahorros\SDATController@putPreSaldar')->name('SDAT.put.preSaldar');
		Route::put('SDAT/{obj}/saldar', 'Ahorros\SDATController@putSaldar')->name('SDAT.put.saldar');
	}
}

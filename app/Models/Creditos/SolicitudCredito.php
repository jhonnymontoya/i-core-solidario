<?php

namespace App\Models\Creditos;

use App\Models\Creditos\ParametroContable;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Recaudos\RecaudoNomina;
use App\Models\Tarjeta\LogMovimientoTransaccionEnviado;
use App\Models\Tarjeta\Tarjetahabiente;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SolicitudCredito extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.solicitudes_creditos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'tercero_id',
		'modalidad_credito_id',
		'seguro_cartera_id',
		'valor_solicitud',
		'valor_credito',
		'numero_obligacion',
		'fecha_solicitud',
		'fecha_aprobacion',
		'fecha_desembolso',
		'fecha_cancelacion',
		'fecha_primer_pago',
		'fecha_primer_pago_intereses',
		'valor_cuota',
		'plazo',
		'periodicidad', //Posibles valores: ANUAL, SEMESTRAL, CUATRIMESTRAL, TRIMESTRAL, BIMESTRAL, MENSUAL, QUINCENAL, CATORCENAL, DECADAL, SEMANAL
		'tipo_pago_intereses', //Posibles valores: VENCIDOS, ANTICIPADOS
		'tipo_amortizacion', //Posibles valores: CAPITAL, FIJA
		'tipo_tasa', //Posibles valores: FIJA, VARIABLE, SINTASA
		'tasa',
		'aplica_mora',
		'tasa_mora',
		'tipo_garantia', //Posibles valores: REAL, PERSONAL
		'forma_pago', //Posibles valores: CAJA, PRIMA, NOMINA
		'calificacion_obligacion', //Posibles valores: A, B, C, D, E, K
		'estado_solicitud', //Posibles valores: SALDADO, DESEMBOLSADO, APROBADO, ANULADO, RECHAZADO, RADICADO, BORRADOR
		'observaciones',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_solicitud',
		'fecha_aprobacion',
		'fecha_desembolso',
		'fecha_cancelacion',
		'fecha_primer_pago',
		'fecha_primer_pago_intereses',
		'created_at',
		'updated_at',
		'deleted_at'
	];

	/**
	 * Atributos que deben ser convertidos a tipos nativos.
	 *
	 * @var array
	 */
	protected $casts = [
		'aplica_mora'		=> 'boolean',
		'valor_cuota'		=> 'float'
	];

	/**
	 * Getters personalizados
	 */

	public function getValorCreditoAttribute() {
		return round($this->attributes['valor_credito']);
	}

	/**
	 * Setters Personalizados
	 */

	public function setFechaSolicitudAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_solicitud'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_solicitud'] = null;
		}
	}

	public function setFechaAprobacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_aprobacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_aprobacion'] = null;
		}
	}

	public function setFechaDesembolsoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_desembolso'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_desembolso'] = null;
		}
	}

	public function setFechaCancelacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_cancelacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_cancelacion'] = null;
		}
	}

	public function setFechaPrimerPagoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_primer_pago'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_primer_pago'] = null;
		}
	}

	public function setFechaPrimerPagoInteresesAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_primer_pago_intereses'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_primer_pago_intereses'] = null;
		}
	}

	/**
	 * Scopes
	 */

	public function scopeEstado($query, $value) {
		if(!empty($value)) {
			return $query->whereEstadoSolicitud($value);
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->whereHas('tercero', function($q) use($value) {
				$q->search($value);
			})->orWhere("numero_obligacion", "like", "%$value%")
			->orWhere("id", "like", "%$value%");
		}
	}

	/**
	 * Funciones
	 */

	public function tieneInconsistencias() {
		if(!$this->amortizaciones->count())return true;

		foreach($this->cumplimientoCondiciones as $condicion) {
			if(!$condicion->cumple)return true;
		}
		return false;
	}

	public function porcentajeCapitalEnExtraordinarias() {
		$totalCapitalExtraordinario = $this->amortizaciones()
			->where('naturaleza_cuota', 'EXTRAORDINARIA')
			->sum('abono_capital');
		$totalCapitalExtraordinario = is_null($totalCapitalExtraordinario) ? 0 : $totalCapitalExtraordinario;
		if($this->valor_credito == 0) {
			return 0;
		}
		$porcentaje = ($totalCapitalExtraordinario * 100) / $this->valor_credito;
		return $porcentaje;
	}

	public function saldoObligacion($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_saldo_obligacion(?, ?, ?) AS saldo_obligacion', [$this->attributes['tercero_id'], $this->attributes['id'], $fechaConsulta]);
		$saldoObligacion = count($res) ? intval($res[0]->saldo_obligacion) : 0;
		return $saldoObligacion;
	}

	public function saldoInteresObligacion($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('exec creditos.sp_saldo_intereses_obligacion ?, ?, ?', [$this->attributes['tercero_id'], $this->attributes['id'], $fechaConsulta]);
		$saldoInteresObligacion = count($res) ? intval($res[0]->saldo_interes_obligacion) : 0;
		return $saldoInteresObligacion;
	}

	public function saldoSeguroObligacion($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('exec creditos.sp_saldo_seguro_obligacion ?, ?', [$this->attributes['id'], $fechaConsulta]);
		$saldoSeguroObligacion = count($res) ? intval($res[0]->saldo_seguro_obligacion) : 0;
		return $saldoSeguroObligacion;
	}

	/**
	 * Devuelve el capital vencido de una obligación a una fecha de consulta
	 * @param type|null $fechaConsulta
	 * @return type
	 */
	public function capitalVencido($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_capital_vencido(?, ?) as capital_vencido', [$this->attributes['id'], $fechaConsulta]);
		$capitalVencido = count($res) ? intval($res[0]->capital_vencido) : 0;
		return $capitalVencido;
	}

	/**
	 * Devuelve los días vencidos de una obligación a una fecha de consulta
	 * @param type|null $fechaConsulta
	 * @return type
	 */
	public function diasVencidos($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_dias_vencidos(?, ?) as dias_vencidos', [$this->attributes['id'], $fechaConsulta]);
		$diasVencidos = count($res) ? intval($res[0]->dias_vencidos) : 0;
		return $diasVencidos;
	}

	/**
	 * Retorna de la amortización, la cuota en la que se encuentra el credito
	 * @param type|null $fechaConsulta
	 * @return type
	 */
	public function alturaObligacion($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_altura_obligacion(?, ?) AS altura_obligacion', [$this->attributes['id'], $fechaConsulta]);
		$altura = count($res) ? intval($res[0]->altura_obligacion) : 0;
		return $altura;
	}

	public function getCodeudores() {
		$tiposCodeudores = $this->modalidadCredito->tiposGarantias->pluck('nombre', 'id');
		$garantias = collect();
		foreach ($tiposCodeudores as $key => $valor) {
			$cantidad = $this->modalidadCredito->tiposGarantias()->whereNombre($valor)->count();
			$garantia = ['nombre' => $valor, 'cantidad' => 0, 'total' => $cantidad, 'cumplido' => false];
			if($this->codeudores->count()) {
				$garantia['cantidad'] = $this->codeudores()->whereTipoGarantiaId($key)->count();
			}
			$garantia['cumplido'] = $garantia['cantidad'] >= $garantia['total'] ? true : false;
			$garantias->push($garantia);
		}
		$garantia = ['nombre' => 'NO REQUERIDO', 'cantidad' => 0, 'total' => 0, 'cumplido' => true];
		$garantia['cantidad'] = $this->codeudores()->whereNull('tipo_garantia_id')->count();
		$garantias->push($garantia);
		$codeudores = collect();
		foreach ($this->codeudores as $codeudor) {
			$nombreGarantia = optional($codeudor->tipoGarantia)->nombre;
			$item = collect();
			$item->put('id', $codeudor->id);
			$item->put('nombreGarantia', empty($nombreGarantia) ? 'NO REQUERIDO' : $nombreGarantia);
			$item->put('numeroIdentificacion', $codeudor->tercero->numero_identificacion);
			$item->put('nombre', $codeudor->tercero->nombre);
			if(!empty($codeudor->condicion)) {
				$item->put('tipoCondicion', $codeudor->condicion);
				switch ($codeudor->condicion) {
					case 'Permanente':
						$item->put('valorCondicion', true);
						break;
					case 'Con descubierto':
						if($codeudor->valor_descubierto <= 0)$item['tipoCondicion'] = 'No requerido';
						$item->put('valorCondicion', true);
						break;
					case 'Por monto':
						if($codeudor->valor_parametro_monto >= $this->valor_credito)$item['tipoCondicion'] = 'No requerido';
						$item->put('valorCondicion', true);
						break;
					case 'Por valor descubierto':
						if($codeudor->valor_descubierto <= $codeudor->valor_parametro_descubierto)$item['tipoCondicion'] = 'No requerido';
						$item->put('valorCondicion', true);
						break;
					default:
						$item->put('valorCondicion', false);
						break;
				}
			}
			else {
				$item->put('tipoCondicion', 'No requerido');
				$item->put('valorCondicion', true);
			}
			$calificacion = true;

			$item->put('admiteCodeudorExterno', $codeudor->admite_codeudor_externo);
			$item->put('valorAdmiteCodeudorExterno', $codeudor->admite_codeudor_externo == false ? ($codeudor->es_codeudor_externo ? false : true) : true);
			$calificacion = $calificacion ? ($item['admiteCodeudorExterno'] ? $item['valorAdmiteCodeudorExterno'] : true) : false;

			$item->put('validaCupoCodeudor', $codeudor->valida_cupo_codeudor);
			$item->put('valorValidaCupoCodeudor', $codeudor->valida_cupo_codeudor ? ($codeudor->cupo_codeudor < $this->valor_credito ? false : true) : true);
			$item->put('cupoCodeudor', $codeudor->cupo_codeudor);
			$calificacion = $calificacion ? ($item['validaCupoCodeudor'] ? $item['valorValidaCupoCodeudor'] : true) : false;

			$item->put('tieneLimiteObligacionesCodeudor', $codeudor->tiene_limite_obligaciones_codeudor);
			$item->put('valorTieneLimiteObligacionesCodeudor', $codeudor->tiene_limite_obligaciones_codeudor ? ($codeudor->parametro_limite_obligaciones_codeudor >  $codeudor->numero_obligaciones_codeudor ? true : false) : true);
			$item->put('limiteObligacionesCodeudor', $codeudor->numero_obligaciones_codeudor);
			$calificacion = $calificacion ? ($item['tieneLimiteObligacionesCodeudor'] ? $item['valorTieneLimiteObligacionesCodeudor'] : true) : false;

			$item->put('tieneLimiteSaldoCodeudas', $codeudor->tiene_limite_saldo_codeudas);
			$item->put('valorTieneLimiteSaldoCodeudas', $codeudor->tiene_limite_saldo_codeudas ? ($codeudor->parametro_limite_saldo_codeudas > ($codeudor->valor_saldo_codeudas + $this->valor_credito) ? true : false)  : true);
			$item->put('valorSaldoCodeudas', $codeudor->valor_saldo_codeudas);
			$calificacion = $calificacion ? ($item['tieneLimiteSaldoCodeudas'] ? $item['valorTieneLimiteSaldoCodeudas'] : true) : false;

			$item->put('validaAntiguedadCodeudor', $codeudor->valida_antiguedad_codeudor);
			$item->put('valorValidaAntiguedadCodeudor', $codeudor->valida_antiguedad_codeudor ? ($codeudor->parametro_antiguedad_codeudor <= $codeudor->valor_antiguedad_codeudor ? true : false) : true);
			$item->put('valorAntiguedadCodeudor', $codeudor->valor_antiguedad_codeudor);
			$calificacion = $calificacion ? ($item['validaAntiguedadCodeudor'] ? $item['valorValidaAntiguedadCodeudor'] : true) : false;

			$item->put('validaCalificacionCodeudor', $codeudor->valida_calificacion_codeudor);
			$item->put('valorValidaCalificacionCodeudor', $codeudor->valida_calificacion_codeudor ? ($codeudor->parametro_calificacion_minima_requerida_codeudor <= $codeudor->valor_calificacion_codeudor ? true : false) : true);
			$item->put('valorCalificacionCodeudor', $codeudor->valor_calificacion_codeudor);
			$calificacion = $calificacion ? ($item['validaCalificacionCodeudor'] ? $item['valorValidaCalificacionCodeudor'] : true) : false;

			if($item['tipoCondicion'] == 'No requerido') {
				$item->put('resultado', $item['tipoCondicion']);
			}
			else {
				$item->put('resultado', $calificacion ? 'Aceptado' : 'No aceptado');
			}
			$codeudores->push($item);
		}
		$data = collect();
		$data->put("garantias", $garantias);
		$data->put("codeudores", $codeudores);
		return $data;
	}

	public function tieneEstados($estados) {
		$estados = explode(",", $estados);
		$res = false;
		foreach ($estados as $estado) {
			if($this->estado == $estado) {
				$res = true;
				break;
			}
		}
		return $res;
	}

	public function getCodeudoresGarantias() {
		$res = array('valorParametro' => 0, 'valorSolicitud' => 0, 'cumple' => false);
		$tiposCodeudores = $this->modalidadCredito->tiposGarantias;
		foreach ($tiposCodeudores as $tipo) {
			if($tipo->es_permanente) {
				$res['valorParametro']++;
			}
			if($tipo->es_permanente_con_descubierto) {
				$socio = $this->tercero->socio;
				if(empty($socio)) {
					$res['valorParametro']++;
					continue;
				}
				$ahorros = $socio->getTotalAhorros($this->fecha_solicitud);
				$nuevoCapitalCredito = $socio->getTotalCapitalCreditos($this->fecha_solicitud) + $this->valor_credito;
				$diff = $ahorros - $nuevoCapitalCredito;
				if($diff < 0)
					$res['valorParametro']++;
			}
			if($tipo->requiere_garantia_por_monto) {
				if($this->valor_credito >= floatval($tipo->monto)) {
					$res['valorParametro']++;
				}
			}
			if($tipo->requiere_garantia_por_valor_descubierto) {
				$socio = $this->tercero->socio;
				if(empty($socio)) {
					$res['valorParametro']++;
					continue;
				}
				$ahorros = $socio->getTotalAhorros($this->fecha_solicitud);
				$nuevoCapitalCredito = $socio->getTotalCapitalCreditos($this->fecha_solicitud);
				$diff = ($ahorros - $nuevoCapitalCredito) + floatval($tipo->valor_descubierto);
				if($this->valor_credito >= $diff) {
					$res['valorParametro']++;
				}
			}
		}
		$codeudores = $this->getCodeudores();
		foreach ($codeudores['codeudores'] as $key => $value) {
			if($value['resultado'] == 'Aceptado') {
				$res['valorSolicitud']++;
			}
		}
		$res['cumple'] = $res['valorSolicitud'] >= $res['valorParametro'] ? true : false;
		return $res;
	}

	public function proximoRecaudo() {
		$recaudos = $this->recaudosNomina()->whereHas('controlProceso', function($q){
			return $q->where('estado', 'GENERADO');
		})->first();
		return $recaudos;
	}

	public function solicitudDeTarjetaHabiente()
	{
		return $this->tarjetahabientes()->count() ? true : false;
	}

	public function getParametroContable()
	{
		$pc = ParametroContable::entidadId($this->entidad_id)
			->tipoCartera('CONSUMO');

		if ($this->forma_pago == 'CAJA') {
			$pc = $pc->tipoGarantia('OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA');
		}
		else {
			$pc = $pc->tipoGarantia('OTRAS GARANTIAS (PERSONAL) CON LIBRANZA');
		}
		$pc = $pc->categoriaClasificacion($this->calificacion_obligacion)
			->first();

		if (!$pc) {
			$mensaje = "No se encontró parámetro contable para obligación %s";
			throw new Exception(sprintf($mensaje, $this->numero_obligacion));
		}
		return $pc;
	}

	/**
	 * Relaciones Uno a Uno
	 */

	/**
	 * Relaciones Uno a muchos
	 */

	public function movimientosCapitalCredito() {
		return $this->hasMany(MovimientoCapitalCredito::class, 'solicitud_credito_id', 'id');
	}

	public function amortizaciones() {
		return $this->hasMany(Amortizacion::class, 'obligacion_id', 'id');
	}

	public function cumplimientoCondiciones() {
		return $this->hasMany(CumplimientoCondicion::class, 'solicitud_id', 'id');
	}

	public function controlesInteresesCartera() {
		return $this->hasMany(ControlInteresCartera::class, 'solicitud_id', 'id');
	}

	public function controlesSegurosCartera() {
		return $this->hasMany(ControlSeguroCartera::class, 'solicitud_id', 'id');
	}

	public function recaudosNomina() {
		return $this->hasMany(RecaudoNomina::class, 'solicitud_credito_id', 'id');
	}

	public function codeudores() {
		return $this->hasMany(Codeudor::class, 'solicitud_credito_id', 'id');
	}

	public function tarjetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'solicitud_credito_id', 'id');
	}

	/**
	 * Me muestra las obligaciones que el presente crédito a consolidado (recogido)
	 * @return Collection(ObligacionConsolidacion)
	 */
	public function obligacionesConsolidadas() {
		return $this->hasMany(ObligacionConsolidacion::class, 'solicitud_credito_id', 'id');
	}

	/**
	 * Devuelve las obligaciones que pretenden recoger el crédito
	 * @return Collection(ObligacionConsolidacion)
	 */
	public function obligacionesQueConsolidan() {
		return $this->hasMany(ObligacionConsolidacion::class, 'solicitud_credito_consolidado_id', 'id');
	}

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'solicitud_credito_id', 'id');
	}

	public function deterioros() {
		return $this->hasMany(Deterioro::class, 'solicitud_credito_id', 'id');
	}

	public function logMovimientosTransaccionesEnviados() {
		return $this->hasMany(
			LogMovimientoTransaccionEnviado::class,
			'solicitud_credito_id',
			'id'
		);
	}

	public function cuotasExtraordinarias() {
		return $this->hasMany(CuotaExtraordinaria::class, 'obligacion_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function modalidadCredito() {
		return $this->belongsTo(Modalidad::class, 'modalidad_credito_id', 'id');
	}

	public function seguroCartera() {
		return $this->belongsTo(SeguroCartera::class, 'seguro_cartera_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */

	public function documentos() {
		return $this->belongsToMany(DocumentacionModalidad::class, 'creditos.documento_solicitud', 'solicitud_credito_id', 'documento_modalidad_id')
					->withPivot('cumple')
					->withTimestamps();
	}
}

<?php

namespace App\Models\Creditos;

use App\Helpers\ConversionHelper;
use App\Models\Creditos\TipoGarantia;
use App\Models\General\Entidad;
use App\Models\Recaudos\ConceptoRecaudos;
use App\Models\Tarjeta\Producto;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modalidad extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.modalidades";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'codigo',
		'nombre',
		'descripcion',
		'es_exclusivo_de_socios',
		'tipo_tasa',
		'es_tasa_condicionada',
		'tasa',
		'pago_interes',
		'factor_condicion_variable_id',
		'aplica_mora',
		'tasa_mora',
		'es_plazo_condicionado',
		'plazo',
		'es_monto_condicionado',
		'monto',
		'es_monto_cupo',
		'afecta_cupo',
		'tipo_cuota',
		'acepta_cuotas_extraordinarias',
		'maximo_porcentaje_pago_extraordinario',
		'minimo_antiguedad_entidad',
		'minimo_antiguedad_empresa',
		'limite_obligaciones',
		'intervalo_solicitudes',
		'acepta_pago_semanal',
		'acepta_pago_decadal',
		'acepta_pago_catorcenal',
		'acepta_pago_quincenal',
		'acepta_pago_mensual',
		'acepta_pago_bimensual',
		'acepta_pago_trimestral',
		'acepta_pago_cuatrimestral',
		'acepta_pago_semestral',
		'acepta_pago_anual',
		'uso_para_tarjeta',
		'esta_activa',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
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
		'es_exclusivo_de_socios'			=> 'boolean',
		'es_tasa_condicionada'				=> 'boolean',
		'aplica_mora'						=> 'boolean',
		'es_plazo_condicionado'				=> 'boolean',
		'es_monto_condicionado'				=> 'boolean',
		'afecta_cupo'						=> 'boolean',
		'acepta_cuotas_extraordinarias'		=> 'boolean',
		'acepta_pago_semanal'				=> 'boolean',
		'acepta_pago_decadal'				=> 'boolean',
		'acepta_pago_catorcenal'			=> 'boolean',
		'acepta_pago_quincenal'				=> 'boolean',
		'acepta_pago_mensual'				=> 'boolean',
		'acepta_pago_bimestral'				=> 'boolean',
		'acepta_pago_trimestral'			=> 'boolean',
		'acepta_pago_cuatrimestral'			=> 'boolean',
		'acepta_pago_semestral'				=> 'boolean',
		'acepta_pago_anual'					=> 'boolean',
		'es_monto_cupo'						=> 'boolean',
		'uso_para_tarjeta'					=> 'boolean',
		'esta_activa'						=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	/**
	 * Setters Personalizados
	 */

	public function setCodigoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
		}
		else {
			$this->attributes['codigo'] = null;
		}
	}

	public function setNombreAttribute($value) {
		if(!empty($value)) {
			$this->attributes['nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
		}
		else {
			$this->attributes['nombre'] = null;
		}
	}

	/**
	 * Scopes
	 */

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->whereEntidadId($value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('codigo', 'like', '%' . $value . '%')->orWhere('nombre', 'like', '%' . $value . '%');
		}
	}

	public function scopeUsoParaTarjeta($query, $value = false) {
		if(!is_null($value)) {
			$query->where('uso_para_tarjeta', $value);
		}
	}

	public function scopeActiva($query, $value = true) {
		return $query->whereEstaActiva($value);
	}

	/**
	 * Funciones
	 */

	public function getPeriodicidadesDePagoAdmitidas() {
		$periodicidades = array();

		if($this->attributes['acepta_pago_semanal'])$periodicidades['SEMANAL'] = "Semanal";
		if($this->attributes['acepta_pago_decadal'])$periodicidades['DECADAL'] = "Decadal";
		if($this->attributes['acepta_pago_catorcenal'])$periodicidades['CATORCENAL'] = "Catorcenal";
		if($this->attributes['acepta_pago_quincenal'])$periodicidades['QUINCENAL'] = "Quincenal";
		if($this->attributes['acepta_pago_mensual'])$periodicidades['MENSUAL'] = "Mensual";
		if($this->attributes['acepta_pago_bimestral'])$periodicidades['BIMESTRAL'] = "Bimestral";
		if($this->attributes['acepta_pago_trimestral'])$periodicidades['TRIMESTRAL'] = "Trimestral";
		if($this->attributes['acepta_pago_cuatrimestral'])$periodicidades['CUATRIMESTRAL'] = "Cuatrimestral";
		if($this->attributes['acepta_pago_semestral'])$periodicidades['SEMESTRAL'] = "Semestral";
		if($this->attributes['acepta_pago_anual'])$periodicidades['ANUAL'] = "Anual";

		return $periodicidades;
	}

	public function obtenerValorTasa(
		$valorCredito,
		$plazo,
		$fechaSolicitud,
		$fechaIngreso,
		$fechaAntiguedad,
		$periodicidad = 'MENSUAL'
	) {
		if ($periodicidad != 'MENSUAL') {
			$plazo = ConversionHelper::conversionValorPeriodicidad(
				$plazo,
				'MENSUAL',
				$periodicidad
			);
		}
		if($this->es_tasa_condicionada) {
			$condicion = $this->condicionesModalidad()->whereTipoCondicion('TASA')->first();
			if(empty($condicion))return 0;

			switch($condicion->condicionado_por) {
				case 'ANTIGUEDADEMPRESA':
					$antiguedad = empty($fechaIngreso) ? Carbon::now()->startOfDay() : $fechaIngreso;
					$diferencia = $fechaSolicitud->diffInMonths($antiguedad, true);
					return $condicion->valorCondicionado($diferencia);
					break;
				case 'ANTIGUEDADENTIDAD':
					$antiguedad = empty($fechaAntiguedad) ? Carbon::now()->startOfDay() : $fechaAntiguedad;
					$diferencia = $fechaSolicitud->diffInMonths($antiguedad, true);
					return $condicion->valorCondicionado($diferencia);
					break;
				case 'MONTO':
					return $condicion->valorCondicionado($valorCredito);
					break;
				case 'PLAZO':
					return $condicion->valorCondicionado($plazo);
					break;

				default:
					return 0;
					break;
			}
		}
		else {
			return empty($this->tasa) ? 0 : $this->tasa;
		}
	}

	/**
	 * Comprueba si la modalidad tiene tasa configurada
	 */
	public function tieneTasa()
	{
		if(empty($this->tipo_tasa) == true) {
			return false;
		}

		switch($this->tipo_tasa) {
			case 'FIJA':
				if(empty($this->pago_interes) == true) {
					return false;
				}
				if(empty($this->tasa) == false) {
					return true;
				}
				$condicion = $this->condicionesModalidad
					->where('tipo_condicion', 'TASA')->first();
				if($condicion == null) {
					return false;
				}
				return $condicion->rangosCondicionesModalidad->count() > 0 ? true : false;

			case 'VARIABLE':
				if(empty($this->pago_intereses) == true) {
					return false;
				}
				if(empty($this->factor_condicion_variable_id) == false) {
					return true;
				}
				if(empty($this->tasa) == false) {
					return true;
				}
				$condicion = $this->condicionesModalidad
					->where('tipo_condicion', 'TASA')->first();
				if($condicion == null) {
					return false;
				}
				return $condicion->rangosCondicionesModalidad->count() > 0 ? true : false;

			case 'SINTASA':
				return true;

			default:
				return false;
		}
		return false;
	}

	/**
	 * Comprueba si la modalidad tiene plazo configurado
	 */
	public function tienePlazo()
	{
		if(is_null($this->es_plazo_condicionado) == true) {
			return false;
		}

		if($this->es_plazo_condicionado) {
			$condicion = $this->condicionesModalidad
				->where('tipo_condicion', 'PLAZO')->first();
			if($condicion == null) {
				return false;
			}
			return $condicion->rangosCondicionesModalidad->count() > 0 ? true : false;
		}
		else {
			return empty($this->plazo) == true ? false : true;
		}
		return false;
	}

	/**
	 * Comprueba si la modalidad tiene amortización configurado
	 */
	public function tieneAmortizacion()
	{
		return is_null($this->tipo_cuota) == true ? false : true;
	}

	public function estaParametrizada()
	{
	    return $this->tieneTasa() && $this->tienePlazo() && $this->tieneAmortizacion();
	}

	/**
	 * Relaciones Uno a Uno
	 */

	/**
	 * Relaciones Uno a muchos
	 */

	public function condicionesModalidad() {
		return $this->hasMany(CondicionModalidad::class, 'modalidad_id', 'id');
	}

	public function documentacionModalidad() {
		return $this->hasMany(DocumentacionModalidad::class, 'modalidad_id', 'id');
	}

	public function solicitudesCreditos() {
		return $this->hasMany(SolicitudCredito::class, 'modalidad_credito_id', 'id');
	}

	public function procesosCreditosLote() {
		return $this->hasMany(ProcesoCreditosLote::class, 'modalidad_credito_id', 'id');
	}

	public function productos() {
		return $this->hasMany(Producto::class, 'modalidad_credito_id', 'id');
	}

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'modalidad_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */

	public function conceptosRecaudos() {
		return $this->belongsToMany(ConceptoRecaudos::class, 'recaudos.conceptos_modalidades_creditos', 'modalidad_credito_id', 'concepto_id')->withTimestamps();
	}

	public function tiposGarantias() {
		return $this->belongsToMany(TipoGarantia::class, 'creditos.garantia_modalidad', 'modalidad_credito_id', 'tipo_garantia_id')->withTimestamps();
	}

	public function segurosCartera() {
		return $this->belongsToMany(SeguroCartera::class, 'creditos.modalidades_por_seguro_cartera', 'modalidad_credito_id', 'seguro_cartera_id')
					->withTimestamps();
	}

	public function cobrosAdministrativos() {
		return $this->belongsToMany(CobroAdministrativo::class, 'creditos.modalidades_por_cobro_administrativo', 'modalidad_credito_id', 'cobro_administrativo_id')
					->withTimestamps();
	}
}

<?php

namespace App\Models\Creditos;

use App\Models\Contabilidad\Cuif;
use App\Models\General\Entidad;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CobroAdministrativo extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.cobros_administrativos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'destino_cuif_id',
		'codigo',
		'nombre',
		'efecto', //DEDUCCIONCREDITO, ADICIONCREDITO
		'es_condicionado',
		'condicion', //MONTO, PLAZO
		'base_cobro', //VALORCREDITO, VAORDESCUBIERTO
		'factor_calculo', //VALORFIJO, PORCENTAJEBASE
		'valor',
		'esta_activo'
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
		'esta_activo'			=> 'boolean',
		'es_condicionado'		=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setEstaActivoAttribute($value) {
		$this->attributes['esta_activo'] = empty($value) ? false : $value;
	}
	
	public function setCodigoAttribute($value) {
		$this->attributes['codigo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setNombreAttribute($value) {
		$this->attributes['nombre'] = title_case($value);
	}

	/**
	 * Scopes
	 */

	public function scopeActivo($query, $value = true) {
		return $query->where('esta_activo', $value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("codigo", "like", "%$value%")->orWhere("nombre", "like", "%$value%");
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->where('entidad_id', $value);
	}
	
	/**
	 * Funciones
	 */

	/**
	 * Valida si un valor esta contenido en uno de sus parámetros
	 * @param type $value
	 * @return type
	 */
	public function contenidoEnCondicion($value) {
		$respuesta = $this->rangoCondiciones
							->where('condicion_desde', '<=', $value)
							->where('condicion_hasta', '>=', $value)
							->count();

		return $respuesta ? true : false;
	}

	public function valorCondicionado($value) {
		$respuesta = $this->rangoCondiciones()
							->where('condicion_desde', '<=', $value)
							->where('condicion_hasta', '>=', $value)
							->first();
		return $respuesta;
	}

	public function calculoValorCobro($solicitud) {
		if(!$this->esta_activo)return 0;
		$baseCobro = $this->base_cobro;
		$factorCalculo = $this->factor_calculo;
		$valor = is_null($this->valor) ? 0 : $this->valor;
		if($this->es_condicionado) {
			$condicion = null;
			if($this->condicion == "MONTO") {
				$condicion = $this->valorCondicionado($solicitud->valor_credito);
			}
			else {
				$condicion = $this->valorCondicionado($solicitud->plazo);
			}
			if(is_null($condicion))return 0;
			$baseCobro = $condicion->base_cobro;
			$factorCalculo = $condicion->factor_calculo;
			$valor = $condicion->valor;
		}
		if($factorCalculo == "VALORFIJO") return $valor;

		if($baseCobro == "VALORCREDITO") {
			return ($solicitud->valor_credito * $valor) / 100;
		}
		else {
			$socio = optional($solicitud->tercero)->socio;
			$ahorros = $creditos = 0;
			if(!is_null($socio)) {
				$ahorros = $socio->getTotalAhorros($solicitud->fecha_aprobacion);
				$creditos = $socio->getTotalCapitalCreditos($solicitud->fecha_aprobacion);
			}
			$creditos += $solicitud->valor_credito;
			if($ahorros >= $creditos)return 0;
			$descubierto = $creditos - $ahorros;
			$descubierto = $descubierto > $solicitud->valor_credito ? $solicitud->valor_credito : $descubierto;
			return ($descubierto * $valor) / 100;
		}
	}

	public function calculoBaseCobro($solicitud) {
		if(!$this->esta_activo)return 0;
		$baseCobro = $this->base_cobro;
		$factorCalculo = $this->factor_calculo;
		$valor = is_null($this->valor) ? 0 : $this->valor;
		if($this->es_condicionado) {
			$condicion = null;
			if($this->condicion == "MONTO") {
				$condicion = $this->valorCondicionado($solicitud->valor_credito);
			}
			else {
				$condicion = $this->valorCondicionado($solicitud->plazo);
			}
			if(is_null($condicion))return 0;
			$baseCobro = $condicion->base_cobro;
			$factorCalculo = $condicion->factor_calculo;
			$valor = $condicion->valor;
		}
		$cobro = collect();
		$cobro->baseCobro = $baseCobro;
		$cobro->factorCalculo = $factorCalculo;
		$cobro->valor = $valor;
		return $cobro;
	}

	/**
	 * Valida si el cobro administrativo se encuentra parametrizado
	 * @return type
	 */
	public function estaParametrizado() {
		if($this->es_condicionado) {
			if($this->rangoCondiciones->count() == 0)return false;
		}
		else {
			if(is_null($this->valor))return false;
		}
		return true;
	}

	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function rangoCondiciones() {
		return $this->hasMany(CondicionCobroAdministrativoRango::class, 'cobro_administrativo_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function cuentaDestino() {
		return $this->belongsTo(Cuif::class, 'destino_cuif_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function modalidades() {
		return $this->belongsToMany(Modalidad::class, 'creditos.modalidades_por_cobro_administrativo', 'cobro_administrativo_id', 'modalidad_credito_id')
					->withTimestamps();
	}
}

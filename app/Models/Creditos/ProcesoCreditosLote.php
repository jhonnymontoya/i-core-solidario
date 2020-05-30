<?php

namespace App\Models\Creditos;

use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\General\ControlCierreModulo;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProcesoCreditosLote extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "creditos.procesos_creditos_lote";

	protected $detalleError = "";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'modalidad_credito_id',
		'contrapartida_cuif_id',
		'contrapartida_tercero_id',
		'fecha_proceso',
		'descripcion',
		'referencia',
		'consecutivo_proceso',
		'estado', //PRECARGA, CARGADO, DESEMBOLSADO, ANULADO
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_proceso',
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
	];

	/**
	 * Getters personalizados
	 */

	public function getDetalleErrorAttribute() {
		return $this->detalleError;
	}

	public function getCantidadSolicitudesCreditosAttribute() {
		return $this->detallesProcesoCreditoLote->count();
	}

	public function getTotalValorCreditosAttribute() {
		$solcicitudesCreditos = $this->getSolicitudesCreditos();
		if($solcicitudesCreditos->count()) {
			return $solcicitudesCreditos->sum('valor_credito');
		}
		else {
			return 0;
		}
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setDescripcionAttribute($value) {
		$this->attributes['descripcion'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setFechaProcesoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_proceso'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_proceso'] = null;
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
			$query->where('descripcion', 'like', '%' . $value . '%')->orWhere('referencia', 'like', '%' . $value . '%');
		}
	}
	
	/**
	 * Funciones
	 */

	public function getSolicitudesCreditos() {
		$solicitudes = $this->detallesProcesoCreditoLote;
		$solicitudesCreditos = collect();
		foreach ($solicitudes as $solicitud) {
			$solicitudesCreditos->push($solicitud->solicitud_credito);
		}
		return $solicitudesCreditos;
	}

	public function esValido() {
		//Se limpia el detalle de error
		$this->detalleError = "";

		//Se valida que el estado del proceso sea cargado
		if($this->estado != 'CARGADO') {
			$this->detalleError = "Estado del proceso no es válido";
			return false;
		}

		//Se valida que la modalidad de crédito esté activa
		if($this->modalidad->esta_activa != 1) {
			$this->detalleError = "Modalidad " . $this->modalidad->nombre . " inactiva";
			return false;
		}

		//Se valida que el módulo de contabilidad no se encuentre cerrado para la fecha de proceso
		$cierre = ControlCierreModulo::entidadId($this->entidad_id)
						->whereModuloId(2)
						->orderBy('fecha_cierre', 'desc')
						->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($this->fecha_proceso)) {
				$this->detalleError = "El módulo de contabilidad se encuentra cerrado para la fecha de proceso";
				return false;
			}
		}

		//Se valida que el módulo de cartera no se encuentre cerrado para la fecha de proceso
		$cierre = ControlCierreModulo::entidadId($this->entidad_id)
						->whereModuloId(7)
						->orderBy('fecha_cierre', 'desc')
						->first();
		if($cierre != null) {
			if($cierre->fecha_cierre->gte($this->fecha_proceso)) {
				$this->detalleError = "El módulo de cartera se encuentra cerrado para la fecha de proceso";
				return false;
			}
		}

		//Se valida que exista el tipo de comprobante para proceso DCLO
		$tipoComprobante = TipoComprobante::entidadId($this->entidad_id)
								->whereCodigo('DCLO')
								->uso('PROCESO')
								->first();
		if($tipoComprobante == null) {
			$this->detalleError = "No existe tipo de comprobante para contabilización de proceso";
			return false;
		}

		//Se valida que cada uno de los socios esten activos y la fecha de primer pago esté en estado de programado
		//para la pagaduría de cada socio
		$solicitudes = $this->getSolicitudesCreditos();
		if(!$solicitudes->count()) {
			$this->detalleError = "No hay solicitudes de crédito";
			return false;
		}
		foreach ($solicitudes as $solicitud) {
			$pagaduria = $solicitud->tercero->socio->pagaduria()->with('calendarioRecaudos')->first();
			if($pagaduria == null) {
				$this->detalleError = 'Tercero ' . $solicitud->tercero->nombre_completo . ' no es asociado';
				return false;
			}
			$calendario = $pagaduria->calendarioRecaudos->where('estado', 'PROGRAMADO')->sortBy('fecha_recaudo')->first();
			if($calendario == null) {
				$this->detalleError = 'No hay programación de recaudos para ' . $solicitud->tercero->nombre_completo;
				return false;
			}
			if($solicitud->tercero->socio->estado != 'ACTIVO') {
				$this->detalleError = "Socio " . $solicitud->tercero->nombre_completo . " no se encuentra ACTIVO";
				return false;
			}
		}

		return true;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function detallesProcesoCreditoLote() {
		return $this->hasMany(DetalleProcesoCreditoLote::class, 'proceso_credito_lote_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function modalidad() {
		return $this->belongsTo(Modalidad::class, 'modalidad_credito_id', 'id');
	}

	public function cuif() {
		return $this->belongsTo(Cuif::class, 'contrapartida_cuif_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'contrapartida_tercero_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}

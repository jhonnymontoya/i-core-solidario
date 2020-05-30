<?php

namespace App\Models\Ahorros;

use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;

class DetalleAjusteAhorroLote extends Model
{
	use ICoreTrait, ICoreModelTrait;
	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "ahorros.detalles_ajustes_ahorros_lote";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'ajuste_ahorro_lote_id',
		'detalle',
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'created_at',
		'updated_at',
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

	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */
	
	/**
	 * Funciones
	 */

	public function getSocio() {
		$arr = json_decode($this->detalle);
		$socio = Socio::find($arr->socio_id);
		return empty($socio) ? false : $socio;
	}

	public function getModalidadAhorro() {
		$arr = json_decode($this->detalle);
		$modalidadAhorro = ModalidadAhorro::find($arr->modalidad_ahorro_id);
		return empty($modalidadAhorro) ? false : $modalidadAhorro;
	}

	public function getValor() {
		$arr = json_decode($this->detalle);
		return $arr->valor;
	}

	public function getSaldoModalidadAhorro($fechaConsulta = null) {
		$fecha = $fechaConsulta == null ? $this->ajusteAhorroLote->fecha_proceso : Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$arr = json_decode($this->detalle);
		$respuesta = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$arr->socio_id, $arr->modalidad_ahorro_id, $fecha]);
		return $respuesta[0]->saldo;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	/**
	 * Relaciones Muchos a uno
	 */

	public function ajusteAhorroLote() {
		return $this->belongsTo(AjusteAhorroLote::class, 'ajuste_ahorro_lote_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}

<?php

namespace App\Models\Contabilidad;

use App\Models\Ahorros\AjusteAhorroLote;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\Creditos\AjusteCreditoLote;
use App\Models\Creditos\CobroAdministrativo;
use App\Models\Creditos\ProcesoCreditosLote;
use App\Models\General\Entidad;
use App\Models\Recaudos\Pagaduria;
use App\Models\Recaudos\RecaudoCaja;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cuif extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "contabilidad.cuifs";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'entidad_id',
		'nivel',
		'codigo',
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
		'esta_activo' 			=> 'boolean',
		'acepta_saldo_negativo' => 'boolean',
		'es_pyg' 				=> 'boolean',
		'es_orden'				=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */
	
	public function getValorAttribute() {
		$valor = 0;
		$debitos = 0;
		$debitos = $this->detalleMovimientos->sum('debito');
		$creditos = 0;
		$creditos = $this->detalleMovimientos->sum('credito');

		if($this->es_debito) {
			$valor = $debitos - $creditos;
		}
		else {
			$valor = $creditos - $debitos;
		}
		return $valor;
	}

	public function getFullAttribute() {
		return $this->codigo . " - " . $this->nombre;
	}

	public function getEsOrdenAttribute() {
		return strlen($this->cuenta_orden) == 0?false:true;
	}

	public function getParaComprobanteAttribute() {
		$res = false;
		switch($this->attributes['modulo_id']) {
			case 1:
				$res = true;
				break;
			case 2:
				$res = true;
				break;
			case 5:
				$res = true;
				break;			
			default:
				$res = false;
				break;
		}
		return $res;
	}

	public function getEsCreditoAttribute() {
		return $this->attributes['naturaleza'] == 'CRÉDITO' ? true : false;
	}

	public function getEsDebitoAttribute() {
		return $this->attributes['naturaleza'] == 'DÉBITO' ? true : false;
	}

	public function getCuentaPadreAttribute() {
		if($this->nivel <= 1) {
			return null;
		}
		$codigoPadre = "";
		switch ($this->nivel) {
			case 2:
				$codigoPadre = substr($this->codigo, 0, 1);
				break;
			case 3:
				$codigoPadre = substr($this->codigo, 0, 2);
				break;
			case 4:
				$codigoPadre = substr($this->codigo, 0, 4);
				break;
			case 5:
				$codigoPadre = substr($this->codigo, 0, 6);
				break;
			case 6:
				$codigoPadre = substr($this->codigo, 0, 8);
				break;
			case 7:
				$codigoPadre = substr($this->codigo, 0, 10);
				break;
			case 8:
				$codigoPadre = substr($this->codigo, 0, 12);
				break;
			case 9:
				$codigoPadre = substr($this->codigo, 0, 14);
				break;
			case 10:
				$codigoPadre = substr($this->codigo, 0, 16);
				break;
			default:
				$codigoPadre = '';
				break;
		}
		$cuentaPadre = Cuif::nivel($this->nivel - 1)->entidadId($this->entidad_id)->codigo($codigoPadre)->first();
		return $cuentaPadre;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setNombreAttribute($value) {
		if(!empty($value)) {
			$this->attributes['nombre'] = trim($value);
		}
		else {
			$this->attributes['nombre'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeCodigo($query, $value) {		
		if(!empty($value)) {
			$query->whereCodigo($value);
		}
	}

	public function scopeNombre($query, $value) {		
		if(trim($value) != '') {
			$query->where("codigo", "like", "$value%")->orWhere("nombre", "like", "%$value%");
		}
	}

	public function scopeNivel($query, $value) {
		if(trim($value) != '') {
			$query->where("nivel", $value);
		}
	}

	public function scopeTipoCuenta($query, $value) {
		if(!empty($value)) {
			$query->where('tipo_cuenta', $value);
		}
	}

	public function scopeCategoria($query, $value) {
		if(trim($value) != '') {
			$query->where("categoria", $value);
		}
	}

	public function scopeActiva($query, $value = true) {
		if(trim($value) != '') {
			$query->whereEstaActivo($value);
		}
	}

	public function scopeEntidadId($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		$query->where('entidad_id', $value);
	}

	public function scopeParaComprobante($query) {
		$query->whereIn('modulo_id', [1, 2]);
	}

	public function scopeModuloId($query, $value) {
		if(!empty($value)) {
			$query->with('modulo')->whereHas('modulo', function($q) use($value){
				$q->whereId($value);
			});
		}
	}
	
	/**
	 * Funciones
	 */
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function detalleMovimientos() {
		return $this->hasMany(DetalleMovimiento::class, 'cuif_id', 'id');
	}

	public function modalidadesAhorros() {
		return $this->hasMany(ModalidadAhorro::class, 'cuif_id', 'id');
	}

	/**
	 * Relación uno a muchos para cobros administrativos
	 * @return type|list CobroAdministrativo
	 */
	public function cobrosAdministrativos() {
		return $this->hasMany(CobroAdministrativo::class, 'destino_cuif_id', 'id');
	}

	/**
	 * Hace referencia a la modalidad de ahorro en su cuenta para rendimiento de intereses
	 * @return type
	 */
	public function modalidadesAhorrosRendimientoIntereses() {
		return $this->hasMany(ModalidadAhorro::class, 'intereses_cuif_id', 'id');
	}

	/**
	 * Hace referencia a la modalidad de ahorro en su cuenta para rendimiento de intereses
	 * por pagar
	 * @return type
	 */
	public function modalidadesAhorrosRendimientoInteresesPorPagar() {
		return $this->hasMany(ModalidadAhorro::class, 'intereses_por_pagar_cuif_id', 'id');
	}

	public function pagaduria() {
		return $this->hasMany(Pagaduria::class, 'ciudad_id', 'id');
	}

	public function procesosCreditosLote() {
		return $this->hasMany(ProcesoCreditosLote::class, 'contrapartida_cuif_id', 'id');
	}

	public function procesosAjustesAhorrosLote() {
		return $this->hasMany(AjusteAhorroLote::class, 'contrapartida_cuif_id', 'id');
	}

	public function procesosAjustesCreditosLote()
	{
		return $this->hasMany(
			AjusteCreditoLote::class,
			'contrapartida_cuif_id',
			'id'
		);
	}

	public function recaudosCaja()
	{
		return $this->hasMany(RecaudoCaja::class, 'recaudo_cuif_id', 'id');
	}

	public function recaudosAhorro()
	{
		return $this->hasMany(RecaudoAhorro::class, 'recaudo_cuif_id', 'id');
	}

	public function conceptosImpuestos()
	{
		return $this->hasMany(ConceptoImpuesto::class, 'destino_cuif_id', 'id');
	}

	/**
     * Relacion uno a muchos para los movimientos de impuestos temporales
     * @return
     */
    public function movimientosImpuestosTemporales() {
        return $this->hasMany(
            MovimientoImpuestoTemporal::class, 'impuesto_id', 'id'
        );
    }

    /**
     * Relacion uno a muchos para los movimientos de impuestos
     * @return
     */
    public function movimientosImpuestos() {
        return $this->hasMany(MovimientoImpuesto::class, 'impuesto_id', 'id');
    }
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function modulo() {
		return $this->belongsTo(Modulo::class, 'modulo_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}

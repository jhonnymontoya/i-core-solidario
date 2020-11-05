<?php

namespace App\Models\General;

use Carbon\Carbon;
use App\Traits\ICoreTrait;
use App\Models\Ahorros\SDAT;
use App\Models\Sistema\Perfil;
use App\Models\Tarjeta\Tarjeta;
use App\Models\Tesoreria\Banco;
use App\Traits\ICoreModelTrait;
use App\Models\Ahorros\TipoSDAT;
use App\Models\Tarjeta\Producto;
use App\Models\Contabilidad\Cuif;
use App\Models\Socios\Parentesco;
use App\Models\Creditos\Deterioro;
use App\Models\Creditos\Modalidad;
use App\Models\Recaudos\Pagaduria;
use App\Models\Socios\CausaRetiro;
use App\Models\Socios\EstadoCivil;
use App\Models\Contabilidad\Modulo;
use App\Models\Socios\TipoVivienda;
use App\Models\Recaudos\RecaudoCaja;
use App\Models\Tesoreria\Franquicia;
use App\Models\Ahorros\CondicionSDAT;
use App\Models\Contabilidad\Impuesto;
use App\Models\Creditos\TipoGarantia;
use App\Models\Creditos\CierreCartera;
use App\Models\Ahorros\ModalidadAhorro;
use App\Models\Ahorros\RendimientoSDAT;
use App\Models\Contabilidad\Movimiento;
use App\Models\Presupuesto\CentroCosto;
use App\Models\Socios\CuotaObligatoria;
use App\Models\Tarjeta\Tarjetahabiente;
use Illuminate\Database\Eloquent\Model;
use App\Models\Ahorros\AjusteAhorroLote;
use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Ahorros\TipoCuentaAhorro;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Creditos\AjusteCreditoLote;
use App\Models\Creditos\ParametroContable;
use App\Models\Contabilidad\TipoComprobante;
use App\Models\Creditos\CobroAdministrativo;
use App\Models\Creditos\ProcesoCreditosLote;
use App\Models\General\ControlPeriodoCierre;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Contabilidad\DetalleMovimiento;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Notificaciones\ConfiguracionFuncion;
use App\Models\Contabilidad\CausaAnulacionMovimiento;
use App\Models\ControlVigilancia\OficialCumplimiento;
use App\Models\Creditos\ParametroCalificacionCartera;
use App\Models\Creditos\ParametroDeterioroIndividual;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\Tarjeta\LogMovimientoTransaccionEnviado;
use App\Models\Tarjeta\LogMovimientoTransaccionRecibido;

class Entidad extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.entidades";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		//usa_tarjeta
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_inicio_contabilidad',
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
		'usa_dependencia'				=> 'boolean',
		'usa_centro_costos'				=> 'boolean',
		'usa_tarjeta'					=> 'boolean',
	];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'modulos',
	];

	/**
	 * Getters personalizados
	 */

	/**
	 * Setters Personalizados
	 */

	public function setFechaInicioContabilidadAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_inicio_contabilidad'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_inicio_contabilidad'] = null;
		}
	}

	/**
	 * Scopes
	 */

	public function scopeActiva($query, $value = true) {
		return $query->whereHas('terceroEntidad', function($q) use($value){
			$q->activo($value);
		})->with('terceroEntidad');
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->whereHas('terceroEntidad', function($q) use($value){
				$q->search($value);
			})->with('terceroEntidad');
		}
	}

	/**
	 * Funciones
	 */

	public function getModulos() {
		$arrModulos = explode(",", $this->modulos);
		$modulos = collect();
		foreach ($arrModulos as $idModulo) {
			$modulo = Modulo::find($idModulo);
			if(!empty($modulo)) {
				switch ($idModulo) {
					case 2: //Contabilidad
						$modulo->icono = 'fa-coins';
						break;
					case 3: //Convenios
						$modulo->icono = 'fa-lock';
						break;
					case 4: //Nómina
						$modulo->icono = 'fa-lock';
						break;
					case 6: //Ahorros y aportes
						$modulo->icono = 'fa-piggy-bank';
						break;
					case 7: //Cartera
						$modulo->icono = 'fa-file-invoice-dollar';
						break;
					case 10: //Socios
						$modulo->icono = 'fa-user-friends';
						break;
					default:
						$modulo->icono = 'fa-cc';
						break;
				}
				$modulos->push($modulo);
			}
		}
		return $modulos;
	}

	/**
	 * Relaciones Uno a Uno
	 */

	public function terceroEntidad() {
		return $this->hasOne(Tercero::class, 'id', 'tercero_id');
	}

	/**
	 * Relaciones Uno a muchos
	 */

	//RELACIONES DE AHORROS

	/**
	 * Relación uno a muchos para los procesos de ajustes de
	 * ahorros en lote
	 * @return Colección AjusteAhorroLote
	 */
	public function procesosAjustesAhorrosLote() {
		return $this->hasMany(AjusteAhorroLote::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para cuentas de ahorros
	 * @return Colección CuentaAhorro
	 */
	public function cuentasAhorros() {
		return $this->hasMany(CuentaAhorro::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para las modalidades de ahorro
	 * @return Colección ModalidadAhorro
	 */
	public function modalidadesAhorros() {
		return $this->hasMany(ModalidadAhorro::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para los movimientos de ahorros
	 * @return Colección MovimientoAhorro
	 */
	public function movimientosAhorros() {
		return $this->hasMany(MovimientoAhorro::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para los tipos de cuentas
	 * de ahorros
	 * @return Colección TipoCuentaAhorro
	 */
	public function tiposCuentasAhorros() {
		return $this->hasMany(TipoCuentaAhorro::class, 'entidad_id', 'id');
	}

	public function tiposSDAT() {
		return $this->hasMany(TipoSDAT::class, 'entidad_id', 'id');
	}

	public function condicionesSDAT() {
		return $this->hasMany(CondicionSDAT::class, 'entidad_id', 'id');
	}

	public function SDATs() {
		return $this->hasMany(SDAT::class, 'entidad_id', 'id');
	}

	public function rendimientosSDAT() {
		return $this->hasMany(RendimientoSDAT::class, 'entidad_id', 'id');
	}

	//RELACIONES DE CONTABILIDAD

	/**
	 * Relación uno a muchos para las causas de anulación de movimientos
	 * @return Colección CausaAnulacionMovimiento
	 */
	public function causasAnulacionMovimientos() {
		return $this->hasMany(CausaAnulacionMovimiento::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para las cuentas contables
	 * @return Colección Cuif
	 */
	public function cuifs() {
		return $this->hasMany(Cuif::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para detalles de movimientos contables
	 * @return Colección DetalleMovimiento
	 */
	public function detallesMovimientos() {
		return $this->hasMany(DetalleMovimiento::class, 'entidad_id', 'id');
	}

	/**
	 * Relacion uno a muchos para los movimientos contables
	 * @return Colección Movimiento
	 */
	public function movimientos() {
		return $this->hasMany(Movimiento::class, 'entidad_id', 'id');
	}

	/**
	 * Relacion uno a muchos para los movimientos de impuestos temporales
	 * @return
	 */
	public function movimientosImpuestosTemporales() {
		return $this->hasMany(
			MovimientoImpuestoTemporal::class, 'entidad_id', 'id'
		);
	}

	/**
	 * Relacion uno a muchos para los movimientos de impuestos
	 * @return
	 */
	public function movimientosImpuestos() {
		return $this->hasMany(MovimientoImpuesto::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para tipos de comprobantes
	 * @return Colección TipoComprobante
	 */
	public function tiposComprobantes() {
		return $this->hasMany(TipoComprobante::class, 'entidad_id', 'id');
	}

	//RELACIONES DE CRÉDITOS

	public function procesosAjustesCreditosLote() {
		return $this->hasMany(AjusteCreditoLote::class, 'entidad_id', 'id');
	}

	public function modalidadesCreditos() {
		return $this->hasMany(Modalidad::class, 'entidad_id', 'id');
	}

	public function dependencias() {
		return $this->hasMany(Dependencia::class, 'entidad_id', 'id');
	}

	public function terceros() {
		return $this->hasMany(Tercero::class, 'entidad_id', 'id');
	}

	public function centroCostos() {
		return $this->hasMany(CentroCosto::class, 'entidad_id', 'id');
	}

	public function estadosciviles() {
		return $this->hasMany(EstadoCivil::class, 'entidad_id', 'id');
	}

	public function parentescos() {
		return $this->hasMany(Parentesco::class, 'entidad_id', 'id');
	}

	public function tiposViviendas() {
		return $this->hasMany(TipoVivienda::class, 'entidad_id', 'id');
	}

	public function franquicias() {
		return $this->hasMany(Franquicia::class, 'entidad_id', 'id');
	}

	public function perfiles() {
		return $this->hasMany(Perfil::class, 'entidad_id', 'id');
	}

	public function bancos() {
		return $this->hasMany(Banco::class, 'entidad_id', 'id');
	}

	public function tiposCuotasObligatorias() {
		return $this->hasMany(TipoCuotaObligatoria::class, 'entidad_id', 'id');
	}

	public function parametrosInstitucionales() {
		return $this->hasMany(ParametroInstitucional::class, 'entidad_id', 'id');
	}

	public function tiposIndicadores() {
		return $this->hasMany(TipoIndicador::class, 'entidad_id', 'id');
	}

	public function parametrosContablesCartera() {
		return $this->hasMany(ParametroContable::class, 'entidad_id', 'id');
	}

	public function solicitudesCreditos() {
		return $this->hasMany(SolicitudCredito::class, 'entidad_id', 'id');
	}

	public function pagaduria() {
		return $this->hasMany(Pagaduria::class, 'entidad_id', 'id');
	}

	public function controlCierresModulos() {
		return $this->hasMany(ControlCierreModulo::class, 'entidad_id', 'id');
	}

	public function controlCierresPeriodos() {
		return $this->hasMany(ControlPeriodoCierre::class, 'entidad_id', 'id');
	}

	public function causasRetiros() {
		return $this->hasMany(CausaRetiro::class, 'entidad_id', 'id');
	}

	public function procesosCreditosLote() {
		return $this->hasMany(ProcesoCreditosLote::class, 'entidad_id', 'id');
	}

	public function tiposGarantias() {
		return $this->hasMany(TipoGarantia::class, 'entidad_id', 'id');
	}

	public function parametrosCalificacionCartera() {
		return $this->hasMany(ParametroCalificacionCartera::class, 'entidad_id', 'id');
	}

	public function parametrosDeterioroIndividual() {
		return $this->hasMany(ParametroDeterioroIndividual::class, 'entidad_id', 'id');
	}

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'entidad_id', 'id');
	}

	public function deterioros() {
		return $this->hasMany(Deterioro::class, 'entidad_id', 'id');
	}

	public function logMovimientosTransaccionesEnviados()
	{
		return $this->hasMany(
			LogMovimientoTransaccionEnviado::class,
			'entidad_id',
			'id'
		);
	}

	public function logMovimientosTransaccionRecibidos()
	{
		return $this->hasMany(
			LogMovimientoTransaccionRecibido::class,
			'entidad_id',
			'id'
		);
	}

	public function impuestos()
	{
		return $this->hasMany(Impuesto::class, 'entidad_id', 'id');
	}

	//RELACIONES DE CRÉDITOS

	/**
	 * Relación uno a muchos para el inventario de tarjetas
	 * @return Colección Tarjeta
	 */
	public function tarjetas() {
		return $this->hasMany(Tarjeta::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para los productos de tarjetas
	 * @return Colección Tarjeta
	 */
	public function productos() {
		return $this->hasMany(Producto::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para los tarjetahabientes
	 * @return type
	 */
	public function Trajetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'entidad_id', 'id');
	}

	/**
	 * Relación uno a muchos para cobros administrativos
	 * @return type|list CobroAdministrativo
	 */
	public function cobrosAdministrativos() {
		return $this->hasMany(CobroAdministrativo::class, 'entidad_id', 'id');
	}

	public function recaudosCaja() {
		return $this->hasMany(RecaudoCaja::class, 'entidad_id', 'id');
	}

	public function recaudosAhorro() {
		return $this->hasMany(RecaudoAhorro::class, 'entidad_id', 'id');
	}

	public function configuracionesFuncion()
	{
	    return $this->hasMany(ConfiguracionFuncion::class, 'entidad_id', 'id');
	}

	//RELACIONES DE CONTROL Y VIGILANCIA

	public function oficialesCumplimiento()
	{
	    return $this->hasMany(OficialCumplimiento::class, 'entidad_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	/**
	 * Relaciones Muchos a Muchos
	 */

	public function categoriaImagenes() {
		return $this->belongsToMany(CategoriaImagen::class, 'general.imagenes', 'entidad_id', 'categoria_imagen_id')
					->withPivot('nombre')
					->withTimestamps();
	}
}

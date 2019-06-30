<?php

namespace App\Models\General;

use App\Models\Ahorros\AjusteAhorroLote;
use App\Models\Contabilidad\DetalleMovimiento;
use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\Creditos\AjusteCreditoLote;
use App\Models\Creditos\CierreCartera;
use App\Models\Creditos\Codeudor;
use App\Models\Creditos\ProcesoCreditosLote;
use App\Models\Creditos\SolicitudCredito;
use App\Models\Recaudos\DatoParaAplicar;
use App\Models\Recaudos\Pagaduria;
use App\Models\Recaudos\RecaudoCaja;
use App\Models\Recaudos\RecaudoNomina;
use App\Models\Socios\Beneficiario;
use App\Models\Socios\Socio;
use App\Models\Tarjeta\LogMovimientoTransaccionRecibido;
use App\Models\Tarjeta\Tarjetahabiente;
use App\Models\Tesoreria\Banco;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tercero extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.terceros";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_nacimiento',
		'fecha_constitucion',
		'fecha_expedicion_documento_identidad',
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
		'matricula_renovada' 	=> 'boolean',
		'es_proveedor'			=> 'boolean',
		'es_cliente' 			=> 'boolean',
		'esta_activo' 			=> 'boolean',
		'es_asociado' 			=> 'boolean',
		'es_empleado'			=> 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	public function getNitAttribute() {
		$nit = number_format($this->attributes['numero_identificacion'], 0, '', '.');
		return $nit . '-' . $this->attributes['digito_verificacion'];
	}

	public function getIdentificacionAttribute() {
		$nit = $this->tipoIdentificacion->codigo . ' ';
		$nit .= number_format($this->attributes['numero_identificacion'], 0, '', '.');
		return $nit;
	}

	public function getNombreCompletoAttribute() {
		$nombre = $this->attributes['numero_identificacion'] . ' - ';
		if($this->attributes['tipo_tercero'] == 'NATURAL') {
			$nombre .= $this->attributes['primer_nombre'] . ' ' . $this->attributes['segundo_nombre'];
			$nombre = trim($nombre) . ' ' . $this->attributes['primer_apellido'] . ' ' . $this->attributes['segundo_apellido'];
		}
		else {
			$nombre .= $this->attributes['razon_social'];
		}
		$nombre = trim($nombre);
		return $nombre;
	}

	public function getNombreCortoAttribute() {
		$nombre = "";
		if($this->attributes['tipo_tercero'] == 'NATURAL') {
			$nombre .= $this->attributes['primer_nombre'] . ' ' . $this->attributes['primer_apellido'];
		}
		else {
			$nombre .= $this->attributes['razon_social'];
		}
		return $nombre;
	}
	
	/**
	 * Setters Personalizados
	 */

	public function setRazonSocialAttribute($value) {
		$this->attributes['razon_social'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	public function setNumeroIdentificacionAttribute($value) {
		if(!empty($value)) {
			$nit = trim($value);
			$this->attributes['numero_identificacion'] = $nit;			
			$dv = self::digitoVerificacion($value);
			$this->attributes['digito_verificacion'] = $dv;
		}
		else {
			$this->attributes['numero_identificacion'] = null;
			$this->attributes['digito_verificacion'] = null;
		}
	}

	public function setPrimerNombreAttribute($value) {
		$this->attributes['primer_nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setSegundoNombreAttribute($value) {
		$this->attributes['segundo_nombre'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setPrimerApellidoAttribute($value) {
		$this->attributes['primer_apellido'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setSegundoApellidoAttribute($value) {
		$this->attributes['segundo_apellido'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}
	
	public function setFechaNacimientoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_nacimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_nacimiento'] = null;
		}
	}
	
	public function setFechaConstitucionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_constitucion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_constitucion'] = null;
		}
	}
		
	public function setFechaExpedicionDocumentoIdentidadAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_expedicion_documento_identidad'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_expedicion_documento_identidad'] = null;
		}
	}
		
	/**
	 * Scopes
	 */
	
	public function scopeActivo($query, $value = true) {
		return $query->where('esta_activo', $value);
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->where("nombre", "like", "%$value%")
				->orWhere("sigla", "like", "%$value%")
				->orWhere("numero_identificacion", "like", "%$value%");
		}
	}

	public function scopeSocioTercero($query, $value = true) {
		return $query->whereEsAsociado($value);
	}

	public function scopeEntidadTercero($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->where('entidad_id', $value);
	}

	public function scopeTipoIdentificacionId($query, $value) {
		if(!empty($value)) {
			return $query->where('tipo_identificacion_id', $value);
		}
	}

	public function scopeTipoTercero($query, $value) {
		if(!empty($value)) {
			return $query->where('tipo_tercero', $value);
		}
	}
	
	/**
	 * Funciones
	 */

	public static function digitoVerificacion($value) {
		$dv = 0;
		if(!empty($value)) {
			$nit = trim($value);

			//seteo de dígito de verificación
			$arreglo = array(0, 3, 7, 13, 17, 19, 23, 29, 37, 41, 43, 47, 53, 59, 67, 71);
			$x = $y = 0;
			$z = strlen($nit);

			for($i = 0; $i < $z; $i++) {
				$y = substr($nit, $i, 1);
				$x += ($y * $arreglo[$z - $i]);
			}

			$y = $x % 11;
			if($y > 1) {
				$dv = 11 - $y;
			}
			else {
				$dv = $y;
			}
		}
		return $dv;
	}

	public function cupoDisponible($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_cupo_disponible(?, ?) AS cupo_disponible', [$this->attributes['id'], $fechaConsulta]);
		$cupoDisponible = count($res) ? intval($res[0]->cupo_disponible) : 0;
		return $cupoDisponible;
	}

	public function cupoDisponibleVista($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay(): Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select ahorros.fn_disponible_vista(?, ?) AS cupo_disponible_vista', [$this->attributes['id'], $fechaConsulta]);
		$cupoDisponible = count($res) ? intval($res[0]->cupo_disponible_vista) : 0;
		return $cupoDisponible;
	}

	/**
	 * Devuelve el contacto preferido o en su defecto el primer contacto
	 * en encontrar
	 * @param type|bool $preferido, busca en primera instancia el preferido
	 * @param type|bool $laboral, busca el contacto laboral
	 * @return Contacto|null
	 */
	public function getContacto($preferido = null, $laboral = null) {
		$contactos = $this->contactos;
		$contacto = null;
		$filtrado = false;
		if($contactos) {
			if($preferido !== null) {
				if($contactos->where('es_preferido', $preferido)->count() != 0) {
					$filtrado = true;
					$contactos = $contactos->where('es_preferido', $preferido);
				}
			}
			if($laboral !== null) {
				$laboral = $laboral ? 'LABORAL' : 'RESIDENCIAL';
				if($contactos->where('tipo_contacto', $laboral)->count() != 0) {
					$filtrado = true;
					$contactos = $contactos->where('tipo_contacto', $laboral);
				}
			}
			if(!$filtrado) {
				if($contactos->where('es_preferido', true)->count() != 0) {
					$contactos = $contactos->where('es_preferido', true);
				}
				if($contactos->where('tipo_contacto', 'LABORAL')->count() != 0) {
					$contactos = $contactos->where('tipo_contacto', 'LABORAL');
				}
			}
			$contacto = $contactos->first();
		}
		return $contacto;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */

	public function socio() {
		return $this->hasOne(Socio::class, 'tercero_id', 'id');
	}
	
	/**
	 * Relaciones Uno a muchos
	 */
	
	public function informacionesFinancieras() {
		return $this->hasMany(InformacionFinanciera::class, 'tercero_id', 'id');
	}

	public function detalleMovimientos() {
		return $this->hasMany(DetalleMovimiento::class, 'tercero_id', 'id');
	}

	public function contactos() {
		return $this->hasMany(Contacto::class, 'tercero_id', 'id');
	}

	public function beneficiarios() {
		return $this->hasMany(Beneficiario::class, 'tercero_id', 'id');
	}

	public function organismos() {
		return $this->hasMany(Organismo::class, 'tercero_id', 'id');
	}

	public function referidos() {
		return $this->hasMany(Socio::class, 'referido_por_tercero_id', 'id');
	}

	public function pagaduria() {
		return $this->hasMany(Pagaduria::class, 'tercero_empresa_id', 'id');
	}

	public function recaudosNomina() {
		return $this->hasMany(RecaudoNomina::class, 'tercero_id', 'id');
	}

	public function datosParaAplicar() {
		return $this->hasMany(DatoParaAplicar::class, 'tercero_id', 'id');
	}

	/*RELACIONES DE CRÉDITOS*/
	public function solicitudesCreditos() {
		return $this->hasMany(SolicitudCredito::class, 'tercero_id', 'id');
	}

	public function procesosCreditosLote() {
		return $this->hasMany(ProcesoCreditosLote::class, 'contrapartida_tercero_id', 'id');
	}

	public function procesosAjustesAhorrosLote() {
		return $this->hasMany(AjusteAhorroLote::class, 'contrapartida_tercero_id', 'id');
	}

	public function procesosAjustesCreditosLote() {
		return $this->hasMany(AjusteCreditoLote::class, 'contrapartida_tercero_id', 'id');
	}

	public function codeudas() {
		return $this->hasMany(Codeudor::class, 'tercero_id', 'id');
	}

	public function tarjetahabientes() {
		return $this->hasMany(Tarjetahabiente::class, 'tercero_id', 'id');
	}

	public function recaudosCaja() {
		return $this->hasMany(RecaudoCaja::class, 'tercero_id', 'id');
	}

	public function recaudosAhorro() {
		return $this->hasMany(RecaudoAhorro::class, 'tercero_id', 'id');
	}

	public function cierresCartera() {
		return $this->hasMany(CierreCartera::class, 'tercero_id', 'id');
	}

	public function logMovimientosTransaccionRecibidos()
	{
		return $this->hasMany(
			LogMovimientoTransaccionRecibido::class,
			'tercero_id',
			'id'
		);
	}

	/**
     * Relacion uno a muchos para los movimientos de impuestos temporales
     * @return
     */
    public function movimientosImpuestosTemporales() {
        return $this->hasMany(
            MovimientoImpuestoTemporal::class, 'tercero_id', 'id'
        );
    }

    /**
     * Relacion uno a muchos para los movimientos de impuestos
     * @return
     */
    public function movimientosImpuestos() {
        return $this->hasMany(MovimientoImpuesto::class, 'tercero_id', 'id');
    }

	/**
	 * Relaciones Muchos a uno
	 */

	public function ciudadExpedicionDocumento() {
		return $this->belongsTo(Ciudad::class, 'ciudad_expedicion_documento_id', 'id');
	}

	public function ciudadConstitucion() {
		return $this->belongsTo(Ciudad::class, 'ciudad_constitucion_id', 'id');
	}

	public function entidad() {
		return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
	}

	public function actividadEconomica() {
		return $this->belongsTo(Ciiu::class, 'actividad_economica_id', 'id');
	}

	public function tipoIdentificacion() {
		return $this->belongsTo(
			TipoIdentificacion::class,
			'tipo_identificacion_id',
			'id'
		);
	}

	public function sexo() {
		return $this->belongsTo(Sexo::class, 'sexo_id', 'id');
	}

	public function ciudadNacimiento() {
		return $this->belongsTo(Ciudad::class, 'ciudad_nacimiento_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a Muchos
	 */

	public function banco() {
		return $this->belongsToMany(Banco::class, 'tesoreria.cuentas_bancarias', 'tercero_id', 'banco_id')
					->withPivot('tipo_cuenta')
					->withPivot('numero')
					->withTimestamps();
	}
}

<?php

namespace App\Models\Ahorros;

use App\Models\Contabilidad\Cuif;
use App\Models\General\Entidad;
use App\Models\Recaudos\ConceptoRecaudos;
use App\Models\Recaudos\Pagaduria;
use App\Models\Recaudos\RecaudoNomina;
use App\Models\Socios\CuotaObligatoria;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use App\Helpers\CalendarioHelper;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ModalidadAhorro extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "ahorros.modalidades_ahorros";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'cuif_id',
        'intereses_cuif_id',
        'intereses_por_pagar_cuif_id',
        'tipo_ahorro',
        'codigo',
        'nombre',
        'es_reintegrable',
        'tipo_calculo',
        'valor',
        'valor_tope',
        'tasa',
        'periodicidad_interes',
        'capitalizacion_simultanea',
        'tipo_vencimiento',
        'plazo',
        'fecha_vencimiento_colectivo',
        'tasa_penalidad',
        'penalidad_por_retiro',
        'paga_retiros',
        'paga_intereses_retirados',//Indica si se incluyen los socios en estado liquidado para el proceso de cierre
        'apalancamiento_cupo', //Este campo se alimenta por /cupoCredito
        'esta_activa',
        'para_beneficiario'
    ];

    /**
     * Atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'fecha_vencimiento_colectivo',
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
        'es_reintegrable'               => 'boolean',
        'capitalizacion_simultanea'     => 'boolean',
        'penalidad_por_retiro'          => 'boolean',
        'paga_retiros'                  => 'boolean',
        'paga_intereses_retirados'      => 'boolean',
        'esta_activa'                   => 'boolean',
        'para_beneficiario'             => 'boolean',
        'tasa'                          => 'decimal:2',
        'tasa_penalidad'                => 'decimal:2'
    ];

    /**
     * Getters personalizados
     */

    public function getApalancamientoCupoAttribute() {
        $ret = $this->attributes['apalancamiento_cupo'] == 0 ? '0.00' : $this->attributes['apalancamiento_cupo'];
        return $ret;
    }

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

    public function setFechaVencimientoColectivoAttribute($value) {
        if(!empty($value)) {
            $this->attributes['fecha_vencimiento_colectivo'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
        }
        else {
            $this->attributes['fecha_vencimiento_colectivo'] = null;
        }
    }

    /**
     * Scopes
     */

    public function scopeEntidadId($query, $value = 0) {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->whereEntidadId($value);
    }

    public function scopeActiva($query, $value = true) {
        if(trim($value) != '') {
            $query->whereEstaActiva($value);
        }
    }

    public function scopeSearch($query, $value) {
        if(!empty($value)) {
            $query->where('codigo', 'like', '%' . $value . '%')->orWhere('nombre', 'like', '%' . $value . '%');
        }
    }

    public function scopeTipoAhorro($query, $value) {
        if(!empty($value)) {
            $query->whereTipoAhorro($value);
        }
    }

    public function scopeObligatorio($query) {
        $query->whereTipoAhorro('OBLIGATORIO');
    }

    public function scopeVoluntario($query) {
        $query->where('tipo_ahorro', '<>', 'OBLIGATORIO');
    }

    /**
     * Funciones
     */

    public function getFechaFinalizacion($fechaInicio = null)
    {
        if($this->attributes['tipo_ahorro'] != 'PROGRAMADO') {
            return null;
        }

        if($this->attributes['tipo_vencimiento'] == 'COLECTIVO') {
            return $this->fecha_vencimiento_colectivo;
        }

        if($this->attributes['tipo_vencimiento'] == 'INDIVIDUAL') {
            if($fechaInicio == null) {
                return null;
            }
            $plazo = $this->attributes['plazo'];
            while(--$plazo > 0) {
                $fechaInicio = CalendarioHelper::siguienteFechaSegunPeriodicidad($fechaInicio, 'MENSUAL');
            }
            return $fechaInicio;
        }

        return null;
    }

    public function getNombre($socioId = null, $incluirCodigo = true)
    {
        $nombre = $this->nombre;

        if($incluirCodigo) {
            $nombre = $this->codigo . ' - ' . $nombre;
        }

        if($this->tipo_ahorro == 'OBLIGATORIO' || $this->para_beneficiario == false || is_null($socioId)){
            return $nombre;
        }

        $cuotaVoluntaria = $this->cuotasVoluntarias()
            ->whereSocioId($socioId)
            ->first();

        if(is_null($cuotaVoluntaria)){
            return $nombre;
        }

        $nombre = $cuotaVoluntaria->nombre;
        if($incluirCodigo == false){
            $nombre = str_replace($this->codigo . ' - ', "", $nombre);
        }

        return $nombre;
    }

    /**
     * Relaciones Uno a Uno
     */

    /**
     * Relaciones Uno a muchos
     */

    public function cuotasObligatorias() {
        return $this->hasMany(CuotaObligatoria::class, 'modalidad_ahorro_id', 'id');
    }

    public function cuotasVoluntarias()     {
        return $this->hasMany(CuotaVoluntaria::class, 'modalidad_ahorro_id', 'id');
    }

    public function movimientosAhorros() {
        return $this->hasMany(MovimientoAhorro::class, 'modalidad_ahorro_id', 'id');
    }

    public function recaudosNomina() {
        return $this->hasMany(RecaudoNomina::class, 'modalidad_id', 'id');
    }

    /**
     * Relaciones Muchos a uno
     */

    public function entidad()   {
        return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
    }

    public function cuenta() {
        return $this->belongsTo(Cuif::class, 'cuif_id', 'id');
    }

    /**
     * Retorna la cuenta de rendimiento de intereses
     * @return type
     */
    public function cuentaRendimientoIntereses() {
        return $this->belongsTo(Cuif::class, 'intereses_cuif_id', 'id');
    }

    /**
     * Retorna la cuenta de rendimiento de intereses por pagar
     * @return type
     */
    public function cuentaRendimientoInteresesPorPagar() {
        return $this->belongsTo(Cuif::class, 'intereses_por_pagar_cuif_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */

    public function conceptosRecaudos() {
        return $this->belongsToMany(ConceptoRecaudos::class, 'recaudos.conceptos_modalidades_ahorros', 'modalidad_ahorro_id', 'concepto_id')->withTimestamps();
    }
}

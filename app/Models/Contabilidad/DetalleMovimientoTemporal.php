<?php

namespace App\Models\Contabilidad;

use Exception;
use Carbon\Carbon;
use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\General\Dependencia;
use App\Models\Presupuesto\CentroCosto;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;

class DetalleMovimientoTemporal extends Model
{
    use ICoreTrait;
    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "contabilidad.detalle_movimientos_temporal";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'codigo_comprobante',
        'movimiento_id',
        'tercero_id',
        'tercero_identificacion',
        'tercero',
        'cuif_id',
        'cuif_codigo',
        'cuif_nombre',
        'dependencia_id',
        'centro_costo_id',
        'debito',
        'credito',
        'serie',
        'fecha_movimiento',
        'referencia',
    ];

    /**
     * Atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'fecha_movimiento',
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

    public function getTieneDebitoAttribute() {
        return $this->attributes['debito'] == 0 ? false : true;
    }

    public function getTieneCreditoAttribute() {
        return $this->attributes['credito'] == 0 ? false : true;
    }

    public function getValorAttribute() {
        return $this->attributes['debito'] + $this->attributes['credito'];
    }

    /**
     * Setters Personalizados
     */

    public function setFechaMovimientoAttribute($value) {
        if(!empty($value)) {
            $this->attributes['fecha_movimiento'] = Carbon::createFromFormat(
                'd/m/Y',
                $value
            )->startOfDay();
        }
        else {
            $this->attributes['fecha_movimiento'] = null;
        }
    }

    public function setReferenciaAttribute($value) {
        $this->attributes['referencia'] = mb_convert_case(
            $value, MB_CASE_UPPER, "UTF-8"
        );
    }

    /**
     * Scopes
     */

    /**
     * Funciones
     */

    public function setTercero($t)
    {
        if (!($t instanceof Tercero)) {
            $mensaje = "Error al asignar el tercero al detalle contable";
            throw new Exception($mensaje);
        }
        $this->attributes["tercero_id"] = $t->id;
        $this->attributes["tercero_identificacion"] = $t->numero_identificacion;
        $this->attributes["tercero"] = $t->nombre;
    }

    public function setCuif($c)
    {
        if (!($c instanceof Cuif)) {
            $mensaje = "Error al asignar la cuenta al detalle contable";
            throw new Exception($mensaje);
        }
        $this->attributes["cuif_id"] = $c->id;
        $this->attributes["cuif_codigo"] = $c->codigo;
        $this->attributes["cuif_nombre"] = $c->nombre;
    }

    /**
     * Relaciones Uno a Uno
     */

    /**
     * Relaciones Uno a muchos
     */

    /**
     * Relacion uno a muchos para los movimientos de impuestos temporales
     * @return
     */
    public function movimientosImpuestosTemporales() {
        return $this->hasMany(
            MovimientoImpuestoTemporal::class,
            'detalle_movimientos_temporal_id',
            'id'
        );
    }

    /**
     * Relaciones Muchos a uno
     */

    public function dependencia() {
        return $this->belongsTo(Dependencia::class, 'dependencia_id', 'id');
    }

    public function entidad() {
        return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
    }

    public function terceroRelacion() {
        return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
    }

    public function cuenta() {
        return $this->belongsTo(Cuif::class, 'cuif_id', 'id');
    }

    public function movimiento() {
        return $this->belongsTo(MovimientoTemporal::class, 'movimiento_id', 'id');
    }

    public function centroCosto() {
        return $this->belongsTo(CentroCosto::class, 'centro_costo_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */
}

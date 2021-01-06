<?php

namespace App\Models\Contabilidad;

use Exception;
use Carbon\Carbon;
use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\Contabilidad\Cuif;
use App\Models\Contabilidad\Impuesto;
use Illuminate\Database\Eloquent\Model;
use App\Models\Contabilidad\ConceptoImpuesto;
use App\Models\Contabilidad\MovimientoTemporal;
use App\Models\Contabilidad\DetalleMovimientoTemporal;

class MovimientoImpuestoTemporal extends Model
{
    use ICoreTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "contabilidad.movimiento_impuesto_temporal";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'movimiento_termporal_id',
        'detalle_movimientos_temporal_id',
        'tercero_id',
        'tercero_identificacion',
        'tercero',
        'fecha_movimiento',
        'impuesto_id',
        'concepto_impuesto_id',
        'cuif_id',
        'cuif_codigo',
        'cuif_nombre',
        'base',
        'tasa',
        'iva',
        //'valor_impuesto' --este es un campo calculado en la base de datos
    ];

    /**
     * Atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'fecha_movimiento',
        'created_at',
        'updated_at'
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
        'base' => 'float',
        'tasa' => 'float',
        'iva' => 'float',
        'valor_impuesto' => 'float'
    ];

    /**
     * Getters personalizados
     */

    /**
     * Setters Personalizados
     */

    public function setFechaMovimientoAttribute($value) {
        if(!empty($value)) {
            $this->attributes['fecha_movimiento'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
        }
        else {
            $this->attributes['fecha_movimiento'] = null;
        }
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
     * Relaciones Muchos a uno
     */

    public function entidad() {
        return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
    }

    public function movimientoTemporal() {
        return $this->belongsTo(
            MovimientoTemporal::class, 'movimiento_termporal_id', 'id'
        );
    }

    public function detalleMovimientoTemporal() {
        return $this->belongsTo(DetalleMovimientoTemporal::class, 'detalle_movimientos_temporal_id', 'id');
    }

    public function terceroRelacion() {
        return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
    }

    public function impuesto() {
        return $this->belongsTo(Impuesto::class, 'impuesto_id', 'id');
    }

    public function conceptoImpueso() {
        return $this->belongsTo(
            ConceptoImpuesto::class, 'concepto_impuesto_id', 'id'
        );
    }

    public function cuif() {
        return $this->belongsTo(Cuif::class, 'cuif_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */
}

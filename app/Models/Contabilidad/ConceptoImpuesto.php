<?php

namespace App\Models\Contabilidad;

use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConceptoImpuesto extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "contabilidad.conceptos_impuestos";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'impuesto_id',
        'destino_cuif_id',
        'nombre',
        'tasa',
        'esta_activo',
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
        'tasa' => 'float',
        'esta_activo' => 'boolean',
    ];

    /**
     * Getters personalizados
     */
    
    /**
     * Setters Personalizados
     */
    
    public function setNombreAttribute($value) {
        $this->attributes['nombre'] = mb_convert_case(
            $value,
            MB_CASE_UPPER,
            "UTF-8"
        );
    }

    /**
     * Scopes
     */

    public function scopeImpuestoId($query, $value) {
        if (!empty($value)) {
            $query->where('impuesto_id', $value);
        }
    }
    
    public function scopeSearch($query, $value) {
        if(trim($value) != '') {
            $query->where('nombre', 'like', "%$value%");
        }
    }

    public function scopeActivo($query, $value = true) {
        if(trim($value) != '') {
            $query->where('esta_activo', $value);
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

    public function impuesto() {
        return $this->belongsTo(Impuesto::class, 'impuesto_id', 'id');
    }

    public function cuentaDestino() {
        return $this->belongsTo(Cuif::class, 'destino_cuif_id', 'id');
    }
    
    /**
     * Relaciones Muchos a Muchos
     */
}

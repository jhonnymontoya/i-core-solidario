<?php

namespace App\Models\Contabilidad;

use App\Models\Contabilidad\MovimientoImpuesto;
use App\Models\Contabilidad\MovimientoImpuestoTemporal;
use App\Models\General\Entidad;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Impuesto extends Model
{
    use SoftDeletes, FonadminTrait, FonadminModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "contabilidad.impuestos";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'nombre',
        'tipo', // NACIONAL, DISTRITAL, REGIONAL
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

    public function scopeEntidadId($query, $value = 0) {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->where('entidad_id', $value);
    }
    
    public function scopeSearch($query, $value) {
        if(trim($value) != '') {
            $query->where('nombre', 'like', "%$value%");
        }
    }

    public function scopeTipo($query, $value) {
        if(!empty($value)) {
            $query->where('tipo', $value);
        }
    }

    public function scopeActivo($query, $value = true) {
        if(!is_null($value)) {
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

    public function conceptosImpuestos() {
        return $this->hasMany(ConceptoImpuesto::class, 'impuesto_id', 'id');
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
    
    /**
     * Relaciones Muchos a Muchos
     */
}

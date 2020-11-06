<?php

namespace App\Models\ControlVigilancia;

use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OficialCumplimiento extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "controlVigilancia.oficiales_cumplimiento";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'nombre',
        'email',
        'email_copia',
        'esta_activo'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
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
        'esta_activo'       => 'boolean',

        'created_at'        => 'datetime:Y-m-d',
        'updated_at'        => 'datetime:Y-m-d',
        'deleted_at'        => 'datetime:Y-m-d',
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Getters personalizados
     */

    /**
     * Setters Personalizados
     */

    public function setNombreAttribute($value) {
        $this->attributes['nombre'] = mb_convert_case($value, MB_CASE_TITLE, "UTF-8");
    }

    /**
     * Scopes
     */

    public function scopeEntidadId($query, $value = 0) {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->where('entidad_id', $value);
    }

    public function scopeActivo($query, $value = 1) {
        $query->where('esta_activo', $value);
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
     * Relaciones Muchos a uno
     */

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */

}

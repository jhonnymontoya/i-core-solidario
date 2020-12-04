<?php

namespace App\Models\Sistema;

use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modulo extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "sistema.modulos";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        //entidad_id
        //codigo
        //nombre
        //esta_activo
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

    /**
     * Scopes
     */

    public function scopeEntidadId($query, $value = 0) {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->whereEntidadId($value);
    }

    public function scopeCodigo($query, $value) {
        if(!empty($value)) {
            return $query->where("codigo", $value);
        }
    }

    public function scopeEstado($query, $value) {
        if(!is_null($value)) {
            return $query->where("esta_activo", $value);
        }
    }

    public function scopeSearch($query, $value) {
        if(!empty($value)) {
            return $query->where("nombre", "like", "%$value%")
                ->orWhere("codigo", "like", "%$value%");
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

<?php

namespace App\Models\Notificaciones;

use App\Traits\ICoreTrait;
use App\Traits\ICoreModelTrait;
use App\Models\Contabilidad\Modulo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Funcion extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "notificaciones.funciones";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'modulo_id',
        'funcion',
        'descripcion'
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
        'deleted_at',
    ];

    /**
     * Atributos que deben ser convertidos a tipos nativos.
     *
     * @var array
     */
    protected $casts = [
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

    public function scopeModuloId($query, $value = 0)
    {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->whereEntidadId($value);
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

    public function configuracionesFuncion()
    {
        return $this->hasMany(ConfiguracionFuncion::class, 'funcion_id', 'id');
    }

    /**
     * Relaciones Muchos a uno
     */

    public function modulo()
    {
        return $this->belongsTo(Modulo::class, 'modulo_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */
}

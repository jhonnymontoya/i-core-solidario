<?php

namespace App\Models\Notificaciones;

use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Models\Sistema\Usuario;
use App\Traits\ICoreModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionFuncion extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "notificaciones.configuracion_funciones";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'funcion_id',
        'usuario_id',
        'enviar_correo',
        'enviar_notificacion',
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
        'enviar_correo'         => 'boolean',
        'enviar_notificacion'   => 'boolean',
        'created_at'            => 'datetime:Y-m-d',
        'updated_at'            => 'datetime:Y-m-d',
        'deleted_at'            => 'datetime:Y-m-d',
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

    public function getEmailAttribute()
    {
        return $this->usuario->email;
    }

    /**
     * Setters Personalizados
     */

    /**
     * Scopes
     */

    public function scopeEntidadId($query, $value = 0)
    {
        $value = empty($value) ? $this->getEntidad()->id : $value;
        $query->whereEntidadId($value);
    }

    public function scopeUsuarioActivo($query)
    {
        $query->whereHas("usuario", function($q){
            $q->activo(1);
        });
    }

    public function scopeCorreo($query)
    {
        $query->whereEnviarCorreo(1);
    }

    public function scopeNotificacion($query)
    {
        $query->whereEnviarNotificacion(1);
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

    public function funcion()
    {
        return $this->belongsTo(Funcion::class, 'funcion_id', 'id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */

}

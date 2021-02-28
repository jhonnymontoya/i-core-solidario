<?php

namespace App\Models\Reportes;

use Carbon\Carbon;
use App\Traits\ICoreTrait;
use App\Models\General\Entidad;
use App\Traits\ICoreModelTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfiguracionExtractoSocial extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "reportes.configuraciones_extracto_social";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'anio',

        'mensaje_general',
        'mensaje_ahorros',
        'mensaje_creditos',
        'mensaje_convenios',
        'mensaje_inversion_social',

        'tasa_promedio_ahorros_externa',
        'tasa_promedio_creditos_externa',

        'gasto_social_total',
        'gasto_social_individual',

        'fecha_inicio_socio_visible', //fecha inicial desde la cual el socio en la consulta lo puede ejecutar el reporte
        'fecha_fin_socio_visible', //fecha fin hasta la cual el socio en la consulta lo puede ejecutar el reporte
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
        'fecha_inicio_socio_visible',
        'fecha_fin_socio_visible',
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
        'tasa_promedio_ahorros_externa'     => 'number',
        'tasa_promedio_creditos_externa'    => 'number',
        'gasto_social_total'                => 'number',
        'gasto_social_individual'           => 'number',
        'fecha_inicio_socio_visible'        => 'number',
        'fecha_fin_socio_visible'           => 'datetime:Y-m-d',
        'created_at'                        => 'datetime:Y-m-d',
        'updated_at'                        => 'datetime:Y-m-d',
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
        $query->where('entidad_id', $value);
    }

    /**
     * Funciones
     */

    public function estaVisibleParaSocios()
    {
        $fechaActual = Carbon::now();
        if($fechaActual->lessThan($this->fecha_inicio_socio_visible)){
            return false;
        }

        if($fechaActual->greaterThan($this->fecha_fin_socio_visible)){
            return false;
        }
        return true;
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

    public function entidad()
    {
        return $this->belongsTo(Entidad::class, 'entidad_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */

}

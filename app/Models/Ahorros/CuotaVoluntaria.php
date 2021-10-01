<?php

namespace App\Models\Ahorros;

use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CuotaVoluntaria extends Model
{
    use SoftDeletes, ICoreTrait, ICoreModelTrait;

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "ahorros.cuotas_voluntarias";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'socio_id',
        'modalidad_ahorro_id',
        'factor_calculo',
        'valor',
        'periodicidad',
        'periodo_inicial',
        'periodo_final',
        'beneficiario'
    ];

    /**
     * Atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'periodo_inicial',
        'periodo_final',
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

    /**
     * Setters Personalizados
     */

    public function setPeriodoInicialAttribute($value) {
        if(!empty($value)) {
            $this->attributes['periodo_inicial'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
        }
        else {
            $this->attributes['periodo_inicial'] = null;
        }
    }

    public function setPeriodoFinalAttribute($value) {
        if(!empty($value)) {
            $this->attributes['periodo_final'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
        }
        else {
            $this->attributes['periodo_final'] = null;
        }
    }

    /**
     * Scopes
     */

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

    public function socio() {
        return $this->belongsTo(Socio::class, 'socio_id', 'id');
    }

    public function modalidadAhorro() {
        return $this->belongsTo(ModalidadAhorro::class, 'modalidad_ahorro_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */
}

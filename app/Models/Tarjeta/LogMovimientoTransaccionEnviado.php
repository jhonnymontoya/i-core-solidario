<?php

namespace App\Models\Tarjeta;

use App\Models\Creditos\MovimientoCapitalCredito;
use App\Models\Creditos\SolicitudCredito;
use App\Models\General\Entidad;
use Illuminate\Database\Eloquent\Model;

class LogMovimientoTransaccionEnviado extends Model
{
    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "tarjeta.log_movimientos_transaccion_enviado";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'solicitud_credito_id',
        'movimiento_capital_credito_id',
        'secuencia',
        'data',
        'error_codigo',
        'error_mensaje',
    ];

    /**
     * Atributos que deben ser convertidos a fechas.
     *
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at'
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
    
    /**
     * Scopes
     */
    
    /**
     * Funciones
     */

    public static function obtenerSecuencia()
    {
        return self::obtenerFecha() .
            self::obtenerHora() .
            rand(100, 999);
    }

    public static function obtenerFecha()
    {
        return date("Ymd");
    }

    public static function obtenerHora()
    {
        return date("His");
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

    public function solicitudCredito()
    {
        return $this->belongsTo(SolicitudCredito::class, 'solicitud_credito_id', 'id');
    }

    public function movimientoCapitalCredito()
    {
        return $this->belongsTo(MovimientoCapitalCredito::class, 'movimiento_capital_credito_id', 'id');
    }
    
    /**
     * Relaciones Muchos a Muchos
     */
}

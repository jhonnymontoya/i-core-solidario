<?php

namespace App\Models\Tarjeta;

use App\Models\Contabilidad\Movimiento;
use App\Models\General\Entidad;
use App\Models\General\Tercero;
use App\Models\General\TipoIdentificacion;
use Illuminate\Database\Eloquent\Model;

class LogMovimientoTransaccionRecibido extends Model
{
    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "tarjeta.log_movimientos_transaccion_recibido";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'entidad_id',
        'producto_id',
        'tercero_id',
        'movimiento_id',
        'data',
        //esta_procesado
        //es_erroneo
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
        'entidad_id' => 'integer',
        'producto_id' => 'integer',
        'tercero_id' => 'integer',
        'movimiento_id' => 'integer',
        'esta_procesado' => 'boolean',
        'es_erroneo' => 'boolean',
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

    public function jsonData()
    {
        return json_decode($this->attributes["data"]);
    }

    public function getConvenio()
    {
        $data = $this->jsonData();
        if (!isset($data->S025)) {
            return null;
        }
        $convenio = ltrim($data->S025, "0");
        return $convenio;
    }

    public function getTipoIdentificacion()
    {
        $data = $this->jsonData();
        if (!isset($data->S012)) {
            return null;
        }
        $valor = trim($data->S012);
        $codigo = null;
        switch ($valor) {
            case '9':
                $codigo = "NIT";
                break;
            case '2':
                $codigo = "TI";
                break;
            case '1':
                $codigo = "CE";
                break;
            case '0':            
            default:
                $codigo = "CC";
                break;
        }
        $tipoIdentificacion = TipoIdentificacion::whereCodigo($codigo)->first();
        if(!$tipoIdentificacion) {
            return null;
        }
        return $tipoIdentificacion;
    }

    public function getNumeroIdentificacion()
    {
        $data = $this->jsonData();
        if (!isset($data->S011)) {
            return null;
        }
        return $data->S011;
    }

    public function getCostoTransaccion()
    {
        $costo = $this->getValor("S037") + $this->getValor("S056");
        return $costo;
    }

    public function getValor($tag)
    {
        $data = $this->jsonData();
        if (!isset($data->$tag)) {
            return null;
        }
        $valor = is_numeric($data->$tag) ? intval($data->$tag) : 0;
        $valor /= 100;
        return $valor;
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

    public function producto()
    {
        return $this->belongsTo(Producto::class, 'producto_id', 'id');
    }

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
    }

    public function movimiento()
    {
        return $this->belongsTo(Movimiento::class, 'movimiento_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */
}

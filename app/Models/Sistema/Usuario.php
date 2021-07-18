<?php

namespace App\Models\Sistema;

use Image;
use Storage;
use App\Traits\ICoreTrait;
use Illuminate\Support\Str;
use App\Traits\ICoreModelTrait;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use App\Models\General\TipoIdentificacion;
use App\Models\General\ControlCierreModulo;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Creditos\CumplimientoCondicion;
use App\Models\Notificaciones\ConfiguracionFuncion;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Usuario extends Authenticatable
{
    use SoftDeletes, Notifiable, ICoreTrait, ICoreModelTrait;

    protected $guard = 'admin';

    /**
     * La tabla que está asociada con el modelo
     * @var String
     */
    protected $table = "sistema.usuarios";

    /**
     * Atributos que se pueden masivamente asignar.
     *
     * @var array
     */
    protected $fillable = [
        'tipo_identificacion_id',
        'identificacion',
        'usuario',
        'primer_nombre',
        'segundo_nombre',
        'primer_apellido',
        'segundo_apellido',
        'email',
        'esta_activo',
        //es_root
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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
        'es_root'   => 'boolean',
    ];

    /**
     * Getters personalizados
     */

    public function getNombreCompletoAttribute() {
        $segundo_nombre = isset($this->attributes['segundo_nombre'])?$this->attributes['segundo_nombre']:"";
        $segundo_apellido = isset($this->attributes['segundo_apellido'])?$this->attributes['segundo_apellido']:"";

        $nombre = $this->attributes['primer_nombre'] . " " . $segundo_nombre;
        $nombre = trim($nombre) . " ";
        $nombre .= $this->attributes['primer_apellido'] . " " . $segundo_apellido;
        $nombre = trim($nombre);

        return $nombre;
    }

    public function getNombreCortoAttribute() {
        $nombre = $this->attributes['primer_nombre'] . " " . $this->attributes['primer_apellido'];
        return $nombre;
    }

    public function getUiConfiguracionAttribute() {
        $uiConfiguracion = json_decode($this->attributes["ui_configuracion"]);
        $resp = "";
        if(!empty($uiConfiguracion->clase)) $resp = $uiConfiguracion->clase . " ";
        $resp .= empty($uiConfiguracion->clase) ? 'skin-blue' : $uiConfiguracion->tema;
        $resp = trim($resp);
        return $resp;
    }

    /**
     * Setters Personalizados
     */

    public function setPasswordAttribute($value) {
        if(!empty($value)) {
            $this->attributes['password'] = $value;
        }
    }

    public function setImagenAttribute($value) {
        $avatar = Image::make($value);
        $avatar = $avatar->orientate();

        $avatar->encode('jpg');
        $imagen = sprintf("%s_%s.jpg", $this->usuario, Str::random(10));

        $avatar->save(storage_path('app/public/avatars/' . $imagen));

        if(!empty($this->attributes['avatar'])) {
            $desde = sprintf('public/avatars/%s', $this->attributes['avatar']);
            $hasta = sprintf('imgOld/%s', $this->attributes['avatar']);
            Storage::move($desde, $hasta);
        }
        $this->attributes['avatar'] = $imagen;
    }

    public function setPrimerNombreAttribute($value) {
        $this->attributes['primer_nombre'] = Str::title($value);
    }

    public function setSegundoNombreAttribute($value) {
        $this->attributes['segundo_nombre'] = Str::title($value);
    }

    public function setPrimerApellidoAttribute($value) {
        $this->attributes['primer_apellido'] = Str::title($value);
    }

    public function setSegundoApellidoAttribute($value) {
        $this->attributes['segundo_apellido'] = Str::title($value);
    }

    public function setUsuarioAttribute($value) {
        $this->attributes['usuario'] = mb_strtolower($value);
    }

    /**
     * Scopes
     */

    public function scopeSearch($query, $value) {
        if(!empty($value)) {
            $query->where("primer_nombre", "like", "%$value%")
                          ->orWhere("segundo_nombre", "like", "%$value%")
                          ->orWhere("primer_apellido", "like", "%$value%")
                          ->orWhere("segundo_apellido", "like", "%$value%")
                          ->orWhere("identificacion", "like", "%$value%")
                          ->orWhere("usuario", "like", "%$value%")
                          ->orWhere("email", "like", "%$value%");
        }
    }

    public function scopeEntidad($query, $value = 0) {
        if(!empty($value)) {
            $query->whereHas('perfiles', function($queryPerfil) use($value){
                $queryPerfil->whereHas('entidad', function($queryEntidad) use($value){
                    $queryEntidad->where('id', $value);
                });
            });
        }
    }

    public function scopeCompleto($query, $value) {
        if(strlen($value) > 0) {
            if($value) {
                $query->whereRaw('len(segundo_nombre) > 0 and len(segundo_apellido) > 0 and len(avatar) > 0');
            }
            else {
                $query->whereRaw('len(segundo_nombre) = 0 or len(segundo_apellido) = 0 or len(avatar) = 0');
            }
        }
    }

    public function scopeActivo($query, $value) {
        if(strlen($value) > 0) {
            $query->where('esta_activo', $value);
        }
    }

    /**
     * Funciones
     */

    public function porcentajePerfilCompleto() {
        $valor = 50;

        $valor += empty($this->attributes['segundo_nombre'])?0:10;
        $valor += empty($this->attributes['segundo_apellido'])?0:10;
        $valor += empty($this->attributes['avatar'])?0:30;

        return $valor;
    }

    /**
     * Relaciones Uno a Uno
     */

    /**
     * Relaciones Uno a muchos
     */

    public function cumplimientoCondiciones() {
        return $this->hasMany(CumplimientoCondicion::class, 'aprobado_por_usuario_id', 'id');
    }

    public function controlCierresModulos() {
        return $this->hasMany(ControlCierreModulo::class, 'usuario_id', 'id');
    }

    /**
     * Obtiene los eventos que ha realizado el usuario
     * @return Collection LogEvento
     */
    public function logsEventos() {
        return $this->hasMany(LogEvento::class, 'usuario_id', 'id');
    }

    public function configuracionesFuncion()
    {
        return $this->hasMany(ConfiguracionFuncion::class, 'usuario_id', 'id');
    }

    /**
     * Relaciones Muchos a uno
     */

    public function tipoIdentificacion() {
        return $this->belongsTo(TipoIdentificacion::class, 'tipo_identificacion_id', 'id');
    }

    /**
     * Relaciones Muchos a Muchos
     */

    public function perfiles() {
        return $this->belongsToMany(Perfil::class, 'sistema.usuario_perfil', 'usuario_id', 'perfil_id')->withTimestamps();
    }
}

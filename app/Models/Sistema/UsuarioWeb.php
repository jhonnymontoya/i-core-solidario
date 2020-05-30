<?php

namespace App\Models\Sistema;

use Laravel\Passport\HasApiTokens;
use App\Models\Socios\Socio;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Notifications\Notifiable;
use Image;
use Storage;

class UsuarioWeb extends Authenticatable
{
	use HasApiTokens, SoftDeletes, Notifiable, ICoreTrait, ICoreModelTrait;

	protected $guard = 'web';

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "sistema.usuarios_web";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		//id
		'usuario',
		'esta_activo',
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
		'esta_activo' => 'boolean',
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

	public function scopeActivo($query, $value = true) {
		$value = $value ? 1 : 0;
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

	public function socios() {
		return $this->hasMany(Socio::class, 'usuario_web_id', 'id');
	}

	/**
	 * Relaciones Muchos a uno
	 */

	/**
	 * Relaciones Muchos a Muchos
	 */
}

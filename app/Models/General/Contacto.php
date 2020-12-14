<?php

namespace App\Models\General;

use App\Models\Socios\TipoVivienda;
use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contacto extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.contactos";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'tipo_contacto',
		'direccion',
		'estrato',
		'tipo_vivienda',
		'email',
		'telefono',
		'extension',
		'movil',
		'es_preferido',
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
		'es_preferido' => 'boolean',
	];

	/**
	 * Getters personalizados
	 */

	/**
	 * Setters Personalizados
	 */

	/**
	 * Se guarda el email en minúscula
	 * @param type $value
	 * @return type
	 */
	public function setEmailAttribute($value) {
		$this->attributes['email'] = mb_convert_case($value, MB_CASE_LOWER, "UTF-8");
	}

	/**
	 * Scopes
	 */

	/**
	 * Funciones
	 */

	public function hayCampos() {
		$conCampos = false;
		$conCampos = empty($this->attributes['ciudad_id'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['direccion'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['estrato'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['tipo_vivienda_id'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['email'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['telefono'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['extension'])?false:true;
		if($conCampos)return true;
		$conCampos = empty($this->attributes['movil'])?false:true;
		if($conCampos)return true;

		return false;
	}

	public function getTelefono()
	{
		if(!empty($this->attributes["movil"])){
			return $this->attributes["movil"];
		}
		elseif(!empty($this->attributes["telefono"])){
			return $this->attributes["telefono"];
		}
		return "";
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

	public function ciudad() {
		return $this->belongsTo(Ciudad::class, 'ciudad_id', 'id');
	}

	public function tercero() {
		return $this->belongsTo(Tercero::class, 'tercero_id', 'id');
	}

	public function tipoVivienda() {
		return $this->belongsTo(TipoVivienda::class, 'tipo_vivienda_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}

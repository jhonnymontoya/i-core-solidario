<?php

namespace App\Models\General;

use App\Traits\ICoreModelTrait;
use App\Traits\ICoreTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ciiu extends Model
{
	use SoftDeletes, ICoreTrait, ICoreModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "general.ciius";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
		'seccion',
		'division',
		'grupo',
		'clase',
		'descripcion',
		'version',
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
	];

	/**
	 * Getters personalizados
	 */

	public function getNombreAttribute() {
		return $this->attributes['clase'] . ' - ' . $this->attributes['descripcion'];
	}
	
	/**
	 * Setters Personalizados
	 */
	
	/**
	 * Scopes
	 */
	
	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			$query->where('grupo', '<>', '')
					->whereNotNull('clase')
					->where('clase', '<>', '')
					->where("clase", "like", "%$value%")
					->orWhere("descripcion", "like", "%$value%");
		}
	}

	public function scopeId($query, $value) {
		if(!empty($value)) {
			$query->where('grupo', '<>', '')->whereNotNull('clase')->where('id', $value);
		}
	}

	public function scopeClase($query) {
		$query->where('grupo', '<>', '')->whereNotNull('clase')->where('clase', '<>', '');
	}
	
	/**
	 * Funciones
	 */

	public static function select() {
		$grupos = Ciiu::where('grupo', '<>', '')
					->whereNotNull('grupo')
					->where('clase', '')
					->orderBy('descripcion')
					->get();

		$actividades = array();

		foreach($grupos as $grupo) {
			$clases = Ciiu::where('grupo', $grupo->grupo)
							->whereNotNull('clase')
							->where('clase', '<>', '')
							->orderBy('descripcion')
							->get();
			$elementos = array();
			foreach($clases as $clase)$elementos[$clase->id] = $clase->clase . " - " . $clase->descripcion;
			$actividades[$grupo->descripcion] = $elementos;

		}
		return $actividades;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function terceros() {
		return $this->hasMany(Tercero::class, 'actividad_economica_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */
	
	/**
	 * Relaciones Muchos a Muchos
	 */
}

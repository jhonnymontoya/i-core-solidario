<?php

namespace App\Models\Socios;

use App\Models\Ahorros\CuentaAhorro;
use App\Models\Ahorros\CuotaVoluntaria;
use App\Models\Ahorros\MovimientoAhorro;
use App\Models\Ahorros\RendimientoSDAT;
use App\Models\Ahorros\SDAT;
use App\Models\General\Ciudad;
use App\Models\General\Dependencia;
use App\Models\General\Profesion;
use App\Models\General\Tercero;
use App\Models\Presupuesto\CentroCosto;
use App\Models\Recaudos\Pagaduria;
use App\Models\Sistema\UsuarioWeb;
use App\Models\Socios\CuotaObligatoria;
use App\Traits\FonadminModelTrait;
use App\Traits\FonadminTrait;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\UploadedFile;
use Image;
use Storage;

class Socio extends Model
{
	use SoftDeletes, FonadminTrait, FonadminModelTrait;

	/**
	 * La tabla que está asociada con el modelo
	 * @var String
	 */
	protected $table = "socios.socios";

	/**
	 * Atributos que se pueden masivamente asignar.
	 *
	 * @var array
	 */
	protected $fillable = [
	];

	/**
	 * Atributos que deben ser convertidos a fechas.
	 *
	 * @var array
	 */
	protected $dates = [
		'fecha_afiliacion',
		'fecha_antiguedad',
		'fecha_ultimo_retiro',
		'fecha_retiro',
		'fecha_ingreso',
		'fecha_fin_contrato',
		//'usuario_web_id',
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
		'es_mujer_cabeza_familia'	=> 'boolean',
		'sueldo_mes'				=> 'integer',
		'valor_comision'			=> 'integer',
		'valor_prima'				=> 'integer',
		'valor_extra_prima'			=> 'integer',
		'descuentos_nomina'			=> 'integer',
		'descuento_prima'			=> 'integer',
		'descuento_extra_prima'		=> 'integer',
		'consecutivo_retiro'		=> 'integer',
	];

	/**
	 * Getters personalizados
	 */
	
	/**
	 * Setters Personalizados
	 */

	public function setCargoAttribute($value) {
		$this->attributes['cargo'] = mb_convert_case($value, MB_CASE_UPPER, "UTF-8");
	}

	public function setAvatarAttribute($value) {
		if(!empty($this->attributes['avatar'])) {
			$desde = sprintf('public/asociados/%s', $this->attributes['avatar']);
			$hasta = sprintf('imgOld/%s', $this->attributes['avatar']);
			Storage::move($desde, $hasta);
		}
		if(!empty($value)) {

			$avatar = Image::make($value);
			$avatar = $avatar->orientate();

			$avatar->encode('jpg');
			$imagen = sprintf("%s_%s.jpg", $this->tercero->numero_identificacion, str_random(10));

			$avatar->save(storage_path('app/public/asociados/' . $imagen));

			$this->attributes['avatar'] = $imagen;
		}
		else {
			$this->attributes['avatar'] = null;
		}
	}

	public function setFirmaAttribute($value) {
		if(!empty($value)) {
			$fileName = str_random(10) . "_" . time() . "_";
			$avatar = Image::make($value);

			$avatar = $avatar->orientate();
			$avatar->encode('jpg');

			$d = $avatar->width() . "x" . $avatar->height() . ".jpg";
			$avatar->save(storage_path('app/public/asociados/' . $fileName . $d));

			$avatar->resize(640, null, function($constraint){
			    $constraint->aspectRatio();
			});
			$d = $avatar->width() . "x" . $avatar->height() . ".jpg";
			$avatar->save(storage_path('app/public/asociados/' . $fileName . $d));

			$avatar->resize(320, null, function($constraint){
			    $constraint->aspectRatio();
			});
			$d = $avatar->width() . "x" . $avatar->height() . ".jpg";
			$avatar->save(storage_path('app/public/asociados/' . $fileName . $d));

			$this->attributes['firma'] = $fileName;
		}
		else {
			$this->attributes['firma'] = null;
		}
	}

	public function SetFechaAfiliacionAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_afiliacion'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_afiliacion'] = null;
		}
	}

	public function setFechaAntiguedadAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_antiguedad'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_antiguedad'] = null;
		}
	}

	public function setFechaUltimoRetiroAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_ultimo_retiro'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_ultimo_retiro'] = null;
		}
	}

	public function setFechaRetiroAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_retiro'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_retiro'] = null;
		}
	}

	public function setFechaIngresoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_ingreso'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_ingreso'] = null;
		}
	}

	public function setFechaFinContratoAttribute($value) {
		if(!empty($value)) {
			$this->attributes['fecha_fin_contrato'] = Carbon::createFromFormat('d/m/Y', $value)->startOfDay();
		}
		else {
			$this->attributes['fecha_fin_contrato'] = null;
		}
	}
	
	/**
	 * Scopes
	 */

	public function scopeEntidad($query, $value = 0) {
		$value = empty($value) ? $this->getEntidad()->id : $value;
		return $query->whereHas('tercero', function($q) use($value){
			$q->where('entidad_id', $value);
		});
	}

	public function scopeSearch($query, $value) {
		if(!empty($value)) {
			return $query->whereHas('tercero', function($q) use($value){
				$q->search($value);
			});
		}
	}

	public function scopePagaduria($query, $value) {
		if(!empty($value)) {
			return $query->where("pagaduria_id", "LIKE", "%$value%");
		}
	}

	public function scopeEstado($query, $value) {
		if(!empty($value)) {
			return $query->where('estado', $value);
		}
	}
	
	/**
	 * Funciones
	 */

	public function endeudamiento($fechaConsulta = null) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay() : Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_endeudamiento_socio(?, ?) AS endeudamiento', [$this->attributes['id'], $fechaConsulta]);
		$endeudamiento = count($res) ? $res[0]->endeudamiento : 0;
		return $endeudamiento;
	}

	public function endeudamientoEstudioSolicitud($fechaConsulta = null, $creditoestudio) {
		$fechaConsulta = empty($fechaConsulta) ? Carbon::now()->startOfDay() : Carbon::createFromFormat('d/m/Y', $fechaConsulta)->startOfDay();
		$res = DB::select('select creditos.fn_endeudamiento_estudio_solicitud(?, ?, ?) AS endeudamiento', [$this->attributes['id'], $fechaConsulta, $creditoestudio]);
		$endeudamiento = count($res) ? $res[0]->endeudamiento : 0;
		return $endeudamiento;
	}

	public function getTotalAhorros($fechaConsulta) {
		$res = DB::select("exec ahorros.sp_estado_cuenta_ahorros ?, ?", [$this->id, $fechaConsulta]);
		$ahorros = collect($res);
		$totalAhorros = 0;
		foreach($ahorros as $ahorro)$totalAhorros += $ahorro->saldo;
		return $totalAhorros;
	}

	public function getTotalCapitalCreditos($fechaConsulta) {
		$totalCreditos = 0;
		$creditos = $this->tercero->solicitudesCreditos()->where('fecha_desembolso', '<=', $fechaConsulta)->estado('DESEMBOLSADO')->get();
		foreach($creditos as $credito)$totalCreditos += $credito->saldoObligacion($fechaConsulta);
		return $totalCreditos;
	}

	public function obtenerAvatar() {
		$avatar = "avatarMale.png";

		if(strlen($this->avatar) > 0) {
			$avatar = $this->avatar;
		}
		else {
			$sexo = $this->tercero->sexo;
			if($sexo) {
				if($sexo->codigo == 2) {
					$avatar = "avatarFemale.png";
				}
			}
		}
		return $avatar;
	}
	 
	/**
	 * Relaciones Uno a Uno
	 */

	public function tercero() {
		return $this->hasOne(Tercero::class, 'id', 'tercero_id');
	}
	
	/**
	 * Relaciones Uno a muchos
	 */

	public function tarjetasCredito() {
		return $this->hasMany(TarjetaCredito::class, 'socio_id', 'id');
	}

	public function beneficiarios() {
		return $this->hasMany(Beneficiario::class, 'socio_id', 'id');
	}

	public function obligacionesFinancieras() {
		return $this->hasMany(ObligacionFinanciera::class, 'socio_id', 'id');
	}

	public function cuotasObligatorias() {
		return $this->hasMany(CuotaObligatoria::class, 'socio_id', 'id');
	}

	public function cuotasVoluntarias() {
		return $this->hasMany(CuotaVoluntaria::class, 'socio_id', 'id');
	}

	public function movimientosAhorros() {
		return $this->hasMany(MovimientoAhorro::class, 'socio_id', 'id');
	}

	public function sociosRetiros() {
		return $this->hasMany(SocioRetiro::class, 'socio_id', 'id');
	}

	public function logLiquidacionesRetiros() {
		return $this->hasMany(LogLiquidacionRetiro::class, 'socio_id', 'id');
	}

	/**
	 * Relación uno a muchos para cuentas de ahorros
	 * @return Colección CuentaAhorro
	 */
	public function cuentasAhorros() {
		return $this->hasMany(CuentaAhorro::class, 'titular_socio_id', 'id');
	}

	public function SDATs() {
		return $this->hasMany(SDAT::class, 'socio_id', 'id');
	}

	public function rendimientosSDAT() {
		return $this->hasMany(RendimientoSDAT::class, 'socio_id', 'id');
	}
	
	/**
	 * Relaciones Muchos a uno
	 */

	public function pagaduria() {
		return $this->belongsTo(Pagaduria::class, 'pagaduria_id', 'id');
	}
	
	public function dependencia() {
		return $this->belongsTo(Dependencia::class, 'dependencia_id', 'id');
	}

	public function centroCosto() {
		return $this->belongsTo(CentroCosto::class, 'centro_costo_id', 'id');
	}

	public function profesion() {
		return $this->belongsTo(Profesion::class, 'profesion_id', 'id');
	}

	public function estadoCivil() {
		return $this->belongsTo(EstadoCivil::class, 'estado_civil_id', 'id');
	}

	public function referente() {
		return $this->belongsTo(Tercero::class, 'referido_por_tercero_id', 'id');
	}

	public function usuarioWeb() {
		return $this->belongsTo(UsuarioWeb::class, 'usuario_web_id', 'id');
	}

	/**
	 * Relaciones Muchos a Muchos
	 */
}

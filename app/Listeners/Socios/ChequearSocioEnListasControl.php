<?php

namespace App\Listeners\Socios;

use App\Events\Socios\SocioAfiliado;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;

class ChequearSocioEnListasControl implements ShouldQueue
{
	use InteractsWithQueue;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct(){}

	/**
	 * Handle the event.
	 *
	 * @param  SocioAfiliado  $event
	 * @return void
	 */
	public function handle(SocioAfiliado $event){
		$socio = $event->socio;
		$usuario = $event->usuario;

		$porcentajeCoincidencia = 50;

		$sql = "EXEC controlVigilancia.sp_chequeo_listas_control ?, ?";
		$res = DB::select($sql, [$socio->id, $usuario->usuario]);

		//$usuario->email;
		if(!$res) {
			return;
		}
		$coincidencia = (float)$res[0]->PORCENTAJE_COINCIDENCIA;
		if($coincidencia >= $porcentajeCoincidencia) {
			//ENVIAR ALERTAS A OFICIAL DE CUMPLIMIENTO Y USUARIOS PARAMETRIZADOS
			//ENVIAR CORREO A USUARIO
		}
	}
}

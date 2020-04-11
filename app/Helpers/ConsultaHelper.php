<?php

namespace App\Helpers;

use Auth;
use DB;
use Carbon\Carbon;

class ConsultaHelper {

	protected $ahorros;
	protected $creditos;
	protected $recaudos;
	protected $socio;
	protected $fechaConsulta;

	public function __construct() {
		$this->ahorros = (object)array("saldo" => 0, "variacionAhorro" => 0);
		$this->creditos = (object)array("saldo" => 0, "porcentajePago" => 0);
		$this->recaudos = (object)array("aplicado" => 0, "fechaRecaudo" => "");

		if(Auth::guest()) {
			return;
		}

		$user = Auth::user();
		$socio = $user->socios;
		$this->socio = $socio[0];
		$this->fechaConsulta = Carbon::now()->startOfDay();

		$this->cargarAhorros();
		$this->cargarCreditos();
		$this->cargarRecaudos();
	}

	protected function cargarAhorros() {
		$sql = "select ahorros.fn_saldo_total_ahorros(?, ?) AS saldo, ahorros.fn_saldo_total_ahorros(?, ?) AS saldo_anterior;";
		$res = DB::select($sql, [$this->socio->id, $this->fechaConsulta, $this->socio->id, $this->fechaConsulta->copy()->addMonth(-1)]);
		if($res) {
			$saldoAnterior = $res[0]->saldo_anterior;
			$saldo = $res[0]->saldo;
			try{
				$this->ahorros->variacionAhorro = intval(($saldo * 100) / $saldoAnterior) - 100;
			}
			catch(\ErrorException $e) {
				$this->ahorros->variacionAhorro = 0;
			}
			$this->ahorros->saldo = $saldo;
		}
	}

	protected function cargarCreditos() {
		$sql = "exec creditos.sp_saldo_total_creditos ?, ?";
		$res = DB::select($sql, [$this->socio->id, $this->fechaConsulta]);
		if($res) {
			$this->creditos->porcentajePago = intval($res[0]->porcentajePago);
			$this->creditos->saldo = $res[0]->saldo;
		}
	}

	protected function cargarRecaudos() {
		$controlProceso = $this->socio->pagaduria->controlProceso()->whereEstado('APLICADO')->orderBy('fecha_aplicacion', 'desc')->first();
		$rec = $this->socio->tercero->recaudosNomina()
			->select(
				DB::raw('SUM(capital_aplicado) + SUM(intereses_aplicado) + SUM(seguro_aplicado) as total_aplicado'),
			)
			->where('control_proceso_id', $controlProceso->id)
			->get();
		if($rec->count()) {
			$this->recaudos->aplicado = $rec[0]->total_aplicado;
		}
		$recaudoAplicado = $this->socio->pagaduria->calendarioRecaudos()
			->whereHas('controlProceso', function($query){
				$query->where('estado', 'APLICADO')->orWhere('estado', 'AJUSTADO');
			})
			->where('estado', 'EJECUTADO')->orderBy('fecha_recaudo', 'desc')->first();
		$this->recaudos->fechaRecaudo = $recaudoAplicado->fecha_recaudo;
	}

	public function ahorros() {
		return $this->ahorros;
	}

	public function creditos() {
		return $this->creditos;
	}

	public function recaudos() {
		return $this->recaudos;
	}
}
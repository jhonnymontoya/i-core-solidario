<?php

namespace App\Helpers;

use Carbon\Carbon;

class CalendarioHelper
{
	//CONVIERTE UN VALOR DE UNA PERIODICIDAD ORIGEN A UNA PERIODICIDAD DESTINO
	static public function siguienteFechaSegunPeriodicidad($fecha, $periodicidad) {
		if(!($fecha instanceof Carbon)) {
			$fecha = Carbon::createFromFormat('d/m/Y', $fecha)->startOfDay();
		}
		
		$siguienteFecha = $fecha->copy()->startOfDay();
		$esFinDeMes = $siguienteFecha->eq($siguienteFecha->copy()->endOfMonth()->startOfDay()) ? true : false;
		$diaDelMes = $fecha->day;

		switch ($periodicidad) {
			case 'ANUAL':
				$siguienteFecha->addYear(1);
				break;
			case 'SEMESTRAL':
				//$siguienteFecha->addMonths(6)->endOfMonth()->startOfDay();
				if(!$esFinDeMes) {
					if($siguienteFecha->month != 2) {
						$siguienteFecha->addDays($diaDelMes - $siguienteFecha->day)->startOfDay();
					}
				}
				else {
					$siguienteFecha->addDays(160)->endOfMonth()->startOfDay();
				}
				break;
			case 'CUATRIMESTRAL':
				//$siguienteFecha->addMonths(4)->endOfMonth()->startOfDay();
				if(!$esFinDeMes) {
					if($siguienteFecha->month != 2) {
						$siguienteFecha->addDays($diaDelMes - $siguienteFecha->day)->startOfDay();
					}
				}
				else {
					$siguienteFecha->addDays(100)->endOfMonth()->startOfDay();
				}
				break;
			case 'TRIMESTRAL':
				//$siguienteFecha->addMonths(3)->endOfMonth()->startOfDay();
				if(!$esFinDeMes) {
					if($siguienteFecha->month != 2) {
						$siguienteFecha->addDays($diaDelMes - $siguienteFecha->day)->startOfDay();
					}
				}
				else {
					$siguienteFecha->addDays(70)->endOfMonth()->startOfDay();
				}
				break;
			case 'BIMESTRAL':
				//$siguienteFecha->addMonths(2)->endOfMonth()->startOfDay();
				if(!$esFinDeMes) {
					if($siguienteFecha->month != 2) {
						$siguienteFecha->addDays($diaDelMes - $siguienteFecha->day)->startOfDay();
					}
				}
				else {
					$siguienteFecha->addDays(40)->endOfMonth()->startOfDay();
				}
				break;
			case 'MENSUAL':
				if(!$esFinDeMes) {
					$siguienteFecha->addMonth()->startOfMonth()->addDays($diaDelMes - 1)->startOfDay();
				}
				else {
					$siguienteFecha->addDays(1)->endOfMonth()->startOfDay();
				}
				break;
			case 'QUINCENAL':
				if($siguienteFecha->day < 15) {
					$siguienteFecha->addDays(15 - $siguienteFecha->day);
				}
				else {
					if($esFinDeMes) {
						$siguienteFecha->addDays(15);
					}
					else {
						$siguienteFecha->endOfMonth()->startOfDay();
					}
				}
				break;
			case 'CATORCENAL':
				$siguienteFecha->addDays(14)->startOfDay();
				break;
			case 'SEMANAL':
				$siguienteFecha->addDays(7)->startOfDay();
				break;
			case 'DECADAL':
				if($siguienteFecha->day < 10) {
					$siguienteFecha->addDays(10 - $siguienteFecha->day);
				}
				elseif($siguienteFecha->day < 20) {
					$siguienteFecha->addDays(20 - $siguienteFecha->day);
				}
				elseif($siguienteFecha->day >= 20 && $siguienteFecha->day != $siguienteFecha->daysInMonth) {
					$siguienteFecha->endOfMonth()->startOfDay();
				}
				else {
					$siguienteFecha->addDays(10);
				}
				break;
			case 'DIARIO':
				$siguienteFecha->addDays(1)->startOfDay();
				break;			
			default:
				$siguienteFecha = $valor;
				break;
		}

		return $siguienteFecha;
	}
}
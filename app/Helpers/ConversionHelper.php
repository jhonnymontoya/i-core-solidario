<?php

namespace App\Helpers;

class ConversionHelper
{
	//CONVIERTE UN VALOR DE UNA PERIODICIDAD ORIGEN A UNA PERIODICIDAD DESTINO
	static public function conversionValorPeriodicidad($valor, $periodicidadOrigen, $periodicidadDestino) {
		
		$valorAnual = 0;
		$valorDestino = 0;

		$periodicidadOrigen = strtoupper($periodicidadOrigen);
		$periodicidadDestino = strtoupper($periodicidadDestino);

		//SE PASA EL VALOR A ANUAL
		switch ($periodicidadOrigen) {
			case 'ANUAL':
				$valorAnual = $valor;
				break;
			case 'SEMESTRAL':
				$valorAnual = $valor * 2;
				break;
			case 'CUATRIMESTRAL':
				$valorAnual = $valor * 3;
				break;
			case 'TRIMESTRAL':
				$valorAnual = $valor * 4;
				break;
			case 'BIMESTRAL':
				$valorAnual = $valor * 6;
				break;
			case 'MENSUAL':
				$valorAnual = $valor * 12;
				break;
			case 'QUINCENAL':
				$valorAnual = $valor * 24;
				break;
			case 'CATORCENAL':
				$valorAnual = $valor * 26;
				break;
			case 'DECADAL':
				$valorAnual = $valor * 36;
				break;
			case 'SEMANAL':
				$valorAnual = $valor * 52;
				break;
			case 'DIARIO':
				$valorAnual = $valor * 360;
				break;			
			default:
				$valorAnual = $valor;
				break;
		}

		//SE PASA EL VALOR A LA PERIODICIDAD DESTINO
		switch ($periodicidadDestino) {
			case 'ANUAL':
				$valorDestino = $valorAnual;
				break;
			case 'SEMESTRAL':
				$valorDestino = $valorAnual / 2;
				break;
			case 'CUATRIMESTRAL':
				$valorDestino = $valorAnual / 3;
				break;
			case 'TRIMESTRAL':
				$valorDestino = $valorAnual / 4;
				break;
			case 'BIMESTRAL':
				$valorDestino = $valorAnual / 6;
				break;
			case 'MENSUAL':
				$valorDestino = $valorAnual / 12;
				break;
			case 'QUINCENAL':
				$valorDestino = $valorAnual / 24;
				break;
			case 'CATORCENAL':
				$valorDestino = $valorAnual / 26;
				break;
			case 'DECADAL':
				$valorDestino = $valorAnual / 36;
				break;
			case 'SEMANAL':
				$valorDestino = $valorAnual / 52;
				break;
			case 'DIARIO':
				$valorDestino = $valorAnual / 360;
				break;			
			default:
				$valorDestino = $valorAnual;
				break;
		}

		return $valorDestino;
	}

	//Retorna los días por periodicidad
	static public function diasPorPeriodicidad($periodicidad) {
		$dias = 0;
		switch ($periodicidad) {
			case 'ANUAL':
				$dias = 360;
				break;
			case 'SEMESTRAL':
				$dias = 180;
				break;
			case 'CUATRIMESTRAL':
				$dias = 120;
				break;
			case 'TRIMESTRAL':
				$dias = 90;
				break;
			case 'BIMESTRAL':
				$dias = 60;
				break;
			case 'MENSUAL':
				$dias = 30;
				break;
			case 'QUINCENAL':
				$dias = 15;
				break;
			case 'CATORCENAL':
				$dias = 14;
				break;
			case 'SEMANAL':
				$dias = 7;
				break;
			case 'DECADAL':
				$dias = 10;
				break;
			case 'DIARIO':
				$dias = 1;
				break;			
			default:
				$dias = $dias;
				break;
		}
		return $dias;
	}
}
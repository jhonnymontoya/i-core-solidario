<?php

namespace App\Helpers;

use App\Models\Creditos\Amortizacion;
use Carbon\Carbon;
use Log;

class FinancieroHelper
{
	static public function obtenerValorCuota($valorCredito = 0, $plazo = 0, $tipoAmortizacion = 'CAPITAL', $tasaMV = 0, $periodicidad = 'MENSUAL') {
		//Si la tasaMV es cero o nula se obliga a que el tipo de amortización
		//sea Capital
		if(empty($tasaMV)) {
			$tipoAmortizacion = 'CAPITAL';
		}


		//SI EL TIPO DE AMORTIZACIÓN ES CAPITAL, SE TOMA EL VALOR DEL CRÉDITO Y SE DIVIDE ENTRE EL PLAZO
		if($tipoAmortizacion == 'CAPITAL') {
			if($plazo == 0) return 0;
			return $valorCredito / $plazo;
		}

		//SI EL TIPO DE AMORTIZACIÓN ES FIJO
		//SE CONVIERTE LA TASA MV A LA PERIODICIDAD
		$tasa = ConversionHelper::conversionValorPeriodicidad($tasaMV, 'MENSUAL', $periodicidad);
		$tasa = $tasa / 100;
		log::info("info", [$valorCredito, $tasa, $tasa, $plazo]);

		$valorCuota = ($valorCredito * $tasa) / (1 - pow(1 + $tasa, -$plazo));

		return $valorCuota;
	}

	static public function obtenerAmortizacion($modalidad, $valorCredito, $fechaCredito, $fechaPrimerPago, $plazo, $periodicidad) {
		$tasa = null;
		$plazoTmp = ConversionHelper::conversionValorPeriodicidad($plazo, "MENSUAL", $periodicidad);
		if($modalidad->es_tasa_condicionada) {
			$condicion = $modalidad->condicionesModalidad()->whereTipoCondicion("TASA")->first();
			if(!$condicion)return null;
			if(!$condicion->contenidoEnCondicion($plazoTmp))return null;
			$tasa = floatval($condicion->valorCondicionado($plazoTmp));
		}
		else {
			$tasa = $modalidad->tasa;
		}
		$valorCuota = FinancieroHelper::obtenerValorCuota($valorCredito, $plazo, $modalidad->tipo_cuota, $tasa, $periodicidad);
		//$modalidad->tipo_cuota = "CAPITAL";
		//$valorCuota = FinancieroHelper::obtenerValorCuota($valorCredito, $plazo, "CAPITAL", $modalidad->tasa, $periodicidad);
		$plazo = intval($plazo);

		$dias = $intereses = $capital = 0;
		$tmp = 0;
		$tasa = ($tasa / 100) / 30;
		$amortizaciones = array();
		if($modalidad->tipo_cuota == 'FIJA') {
			while($tmp < $plazo) {
				if($tmp == 0) {
					$saldoCapital = $valorCredito;
					$fechaPrimerPago = $fechaPrimerPago;
					$dias = $fechaCredito->diffInDays($fechaPrimerPago);
					$intereses = $tasa * $dias * $valorCredito;
				}
				else {
					$saldoCapital = $amortizaciones[$tmp - 1]['nuevoSaldoCapital'];
					$fechaPrimerPago = $amortizaciones[$tmp - 1]['fechaCuota'];
					$fechaPrimerPago = CalendarioHelper::siguienteFechaSegunPeriodicidad($fechaPrimerPago, $periodicidad);
					$dias = ConversionHelper::diasPorPeriodicidad($periodicidad);
					$intereses = $tasa * $dias * $saldoCapital;
				}
				$capital = $valorCuota - $intereses;
				$nuevoSaldoCapital = $saldoCapital - $capital;

				if($tmp == ($plazo - 1)){
					$capital = $saldoCapital;
					$valorCuota = $capital + $intereses;
					$nuevoSaldoCapital = 0;
				}
				array_push($amortizaciones, [
					"numeroCuota" => ($tmp + 1),
					"fechaCuota" => $fechaPrimerPago,
					"capital" => $capital,
					"intereses" => $intereses,
					"total" => $valorCuota,
					"nuevoSaldoCapital" => $nuevoSaldoCapital
				]);
				$tmp++;
			}
		}
		elseif($modalidad->tipo_cuota == 'CAPITAL') {
			while($tmp < $plazo) {
				if($tmp == 0) {
					$saldoCapital = $valorCredito;
					$fechaPrimerPago = $fechaPrimerPago;
					$dias = $fechaCredito->diffInDays($fechaPrimerPago);
					$intereses = $tasa * $dias * $valorCredito;
				}
				else {
					$saldoCapital = $amortizaciones[$tmp - 1]['nuevoSaldoCapital'];
					$fechaPrimerPago = $amortizaciones[$tmp - 1]['fechaCuota'];
					$fechaPrimerPago = CalendarioHelper::siguienteFechaSegunPeriodicidad($fechaPrimerPago, $periodicidad);
					$dias = ConversionHelper::diasPorPeriodicidad($periodicidad);
					$intereses = $tasa * $dias * $saldoCapital;
				}
				$capital = $valorCuota + $intereses;
				$nuevoSaldoCapital = $saldoCapital - $valorCuota;

				if($tmp == ($plazo - 1)){
					$nuevoSaldoCapital = 0;
				}
				array_push($amortizaciones, [
					"numeroCuota" => ($tmp + 1),
					"fechaCuota" => $fechaPrimerPago,
					"capital" => $valorCuota,
					"intereses" => $intereses,
					"total" => $capital,
					"nuevoSaldoCapital" => $nuevoSaldoCapital
				]);
				$tmp++;
			}
		}
		return $amortizaciones;
	}

	/**
	 * Obtiene las amortizaciones para una reliquidación de crédito
	 * @param type $credito al que se le aplica la reliquidación
	 * @param type $formaReliquidar 1 = Por plazo, 2 = Por cuota
	 * @param type $plazo nuevo del credito
	 * @param type $cuota nueva del crédito
	 * @param type $periodicidad periodicidad del credito
	 * @param type $fechaProximoPago del crédito
	 * @param type $fechaProximoPagoIntereses del crédito
	 * @return type
	 */
	static public function reliquidarAmortizacion($credito, $fechaReliquidacion, $formaReliquidar, $plazo, $cuota, $periodicidad, $fechaProximoPago, $fechaProximoPagoIntereses) {
		$amortizaciones = $credito->amortizaciones;
		$modalidad = $credito->modalidadCredito;
		$seguroCartera = empty($credito->seguroCartera) ? 0 : $credito->seguroCartera->tasa_mes;
		$tasaParaValorCuota = ConversionHelper::conversionValorPeriodicidad($credito->tasa + $seguroCartera, 'MENSUAL', $credito->periodicidad) / 100;

		//Obtener del calendario de recaudos de la pagaduría los calendarios programados
		$calendario = $credito->tercero->socio->pagaduria->calendarioRecaudos();
		$calendario = $calendario
						->where('estado', 'EJECUTADO')
						->orderBy('fecha_recaudo')
						->orderBy('numero_periodo')
						->get()
						->last();

		$saldoCapital = $credito->saldoObligacion($fechaReliquidacion);
		$saldoIntereses = $credito->saldoInteresObligacion($fechaProximoPago);
		$saldoIntereses = $saldoIntereses < 0 ? 0 : $saldoIntereses;
		$saldoSeguro = $credito->saldoSeguroObligacion($fechaProximoPago);
		$saldoSeguro = $saldoSeguro < 0 ? 0 : $saldoSeguro;

		if($formaReliquidar == 2) {
			$valorCuota = $cuota;
			$valorCuota = $valorCuota > ($saldoCapital + $saldoIntereses + $saldoSeguro) ? $saldoCapital + $saldoIntereses + $saldoSeguro : $valorCuota;
		}
		else {
			$valorCuota = FinancieroHelper::obtenerValorCuota($saldoCapital, $plazo, $credito->tipo_amortizacion, $credito->tasa, $periodicidad);
		}

		$numeroCuota = 0;
		$nuevaAmortizacion = collect();

		//se busca el numero de la cuota para calcular la nueva amortización
		$numeroCuota = 1;
		if($plazo == 1) {
			$amortizacion = new Amortizacion;
			$amortizacion->obligacion_id = $credito->id;
			$amortizacion->numero_cuota = $numeroCuota;
			$amortizacion->naturaleza_cuota = "ORDINARIA";
			$amortizacion->forma_pago = $credito->forma_pago;
			$amortizacion->fecha_cuota = $fechaProximoPago;

			$amortizacion->abono_capital = $saldoCapital;
			$amortizacion->abono_intereses = $saldoIntereses;
			$amortizacion->abono_seguro_cartera = $saldoSeguro;
			$amortizacion->nuevo_saldo_capital = 0;
			$amortizacion->total_cuota = $saldoCapital + $saldoIntereses + $saldoSeguro;

			$amortizacion->estado_cuota = 'PENDIENTE';
			$nuevaAmortizacion->push($amortizacion);
			return $nuevaAmortizacion;
		}

		if($credito->tipo_amortizacion == 'FIJA') {
			$amortizacion = new Amortizacion;
			$amortizacion->obligacion_id = $credito->id;
			$amortizacion->numero_cuota = $numeroCuota;
			$amortizacion->naturaleza_cuota = "ORDINARIA";
			$amortizacion->forma_pago = $credito->forma_pago;
			$amortizacion->fecha_cuota = $fechaProximoPago;
			if($valorCuota - $saldoIntereses - $saldoSeguro < 0) {
				$amortizacion->abono_capital = 0;
				$amortizacion->abono_intereses = $saldoIntereses;
				$amortizacion->abono_seguro_cartera = $saldoSeguro;
				$amortizacion->nuevo_saldo_capital = $saldoCapital;
				$amortizacion->total_cuota = $saldoIntereses + $saldoSeguro;
			}
			else {
				$amortizacion->abono_capital = $valorCuota - ($saldoIntereses + $saldoSeguro);
				$amortizacion->abono_intereses = $saldoIntereses;
				$amortizacion->abono_seguro_cartera = $saldoSeguro;
				$amortizacion->total_cuota = $valorCuota;
				$amortizacion->nuevo_saldo_capital = $saldoCapital - $amortizacion->abono_capital;
				$saldoCapital -= $amortizacion->abono_capital;
			}
			$amortizacion->estado_cuota = 'PENDIENTE';
			$nuevaAmortizacion->push($amortizacion);
			while($saldoCapital > 0) {
				$fechaProximoPago = CalendarioHelper::siguienteFechaSegunPeriodicidad($fechaProximoPago, $periodicidad);
				$cuotaIntereses = $saldoCapital * (ConversionHelper::conversionValorPeriodicidad($credito->tasa, 'MENSUAL', $credito->periodicidad) / 100);
				$cuotaSeguro = $saldoCapital * (ConversionHelper::conversionValorPeriodicidad($seguroCartera, 'MENSUAL', $credito->periodicidad) / 100);

				$amortizacion = new Amortizacion;
				$amortizacion->obligacion_id = $credito->id;
				$amortizacion->numero_cuota = ++$numeroCuota;
				$amortizacion->naturaleza_cuota = "ORDINARIA";
				$amortizacion->forma_pago = $credito->forma_pago;
				$amortizacion->fecha_cuota = $fechaProximoPago;
				if($saldoCapital < ($valorCuota - ($cuotaIntereses + $cuotaSeguro))){
					$amortizacion->abono_capital = $saldoCapital;
					$amortizacion->abono_intereses = $cuotaIntereses;
					$amortizacion->abono_seguro_cartera = $cuotaSeguro;
					$amortizacion->total_cuota = $saldoCapital + $cuotaIntereses + $cuotaSeguro;
					$amortizacion->nuevo_saldo_capital = 0;
				}
				else {
					$amortizacion->abono_capital = $valorCuota - ($cuotaIntereses + $cuotaSeguro);
					$amortizacion->abono_intereses = $cuotaIntereses;
					$amortizacion->abono_seguro_cartera = $cuotaSeguro;
					$amortizacion->total_cuota = $valorCuota;
					$amortizacion->nuevo_saldo_capital = $saldoCapital - $amortizacion->abono_capital;
				}
				$amortizacion->estado_cuota = 'PENDIENTE';
				$saldoCapital -= $amortizacion->abono_capital;
				$nuevaAmortizacion->push($amortizacion);
			}//dd($nuevaAmortizacion);
		}
		else {
			$f1 = Carbon::createFromFormat('d/m/Y', $fechaProximoPago)->startOfDay();
			$f2 = Carbon::createFromFormat('d/m/Y', $fechaProximoPagoIntereses)->startOfDay();
			$fechaInicial = $f1->gt($f2) ? $f2->copy() : $f1->copy();
			$diferenciaPeriodos = 0;
			while(!$f1->eq($f2)) {
				$f2 = CalendarioHelper::siguienteFechaSegunPeriodicidad($f2, $periodicidad);
				$diferenciaPeriodos++;
			}
			$cuota = empty($cuota) ? $saldoCapital / ($plazo - $diferenciaPeriodos) : $cuota;
			$cuota = $cuota > ($saldoCapital + $saldoIntereses + $saldoSeguro) ? $saldoCapital + $saldoIntereses + $saldoSeguro : $cuota;

			$saldoIntereses = $credito->saldoInteresObligacion($fechaProximoPagoIntereses);
			$saldoIntereses = $saldoIntereses < 0 ? 0 : $saldoIntereses;
			$saldoSeguro = $credito->saldoSeguroObligacion($fechaProximoPagoIntereses);
			$saldoSeguro = $saldoSeguro < 0 ? 0 : $saldoSeguro;

			$amortizacion = new Amortizacion;
			$amortizacion->obligacion_id = $credito->id;
			$amortizacion->numero_cuota = $numeroCuota;
			$amortizacion->naturaleza_cuota = "ORDINARIA";
			$amortizacion->forma_pago = $credito->forma_pago;
			$amortizacion->fecha_cuota = $fechaProximoPagoIntereses;

			if($diferenciaPeriodos > 0) {
				$diferenciaPeriodos--;
				$amortizacion->abono_capital = 0;
				$amortizacion->abono_intereses = $saldoIntereses;
				$amortizacion->abono_seguro_cartera = $saldoSeguro;
				$amortizacion->nuevo_saldo_capital = $saldoCapital;
				$amortizacion->total_cuota = $saldoIntereses + $saldoSeguro;
			}
			else {
				$amortizacion->abono_capital = $cuota;
				$amortizacion->abono_intereses = $saldoIntereses;
				$amortizacion->abono_seguro_cartera = $saldoSeguro;
				$amortizacion->nuevo_saldo_capital = $saldoCapital - $amortizacion->abono_capital;
				$amortizacion->total_cuota = $cuota + $saldoIntereses + $saldoSeguro;
				$saldoCapital -= $amortizacion->abono_capital;
			}

			$amortizacion->estado_cuota = 'PENDIENTE';
			$nuevaAmortizacion->push($amortizacion);

			while($saldoCapital > 0) {
				$fechaInicial = CalendarioHelper::siguienteFechaSegunPeriodicidad($fechaInicial, $periodicidad);
				$cuotaIntereses = $saldoCapital * (ConversionHelper::conversionValorPeriodicidad($credito->tasa, 'MENSUAL', $periodicidad) / 100);
				$cuotaSeguro = $saldoCapital * (ConversionHelper::conversionValorPeriodicidad($seguroCartera, 'MENSUAL', $periodicidad) / 100);
				$amortizacion = new Amortizacion;
				$amortizacion->obligacion_id = $credito->id;
				$amortizacion->numero_cuota = ++$numeroCuota;
				$amortizacion->naturaleza_cuota = "ORDINARIA";
				$amortizacion->forma_pago = $credito->forma_pago;
				$amortizacion->fecha_cuota = $fechaInicial;
				if($diferenciaPeriodos > 0) {
					$diferenciaPeriodos--;
					$amortizacion->abono_capital = 0;
					$amortizacion->abono_intereses = $cuotaIntereses;
					$amortizacion->abono_seguro_cartera = $cuotaSeguro;
					$amortizacion->nuevo_saldo_capital = $saldoCapital;
					$amortizacion->total_cuota = $cuotaIntereses + $cuotaSeguro;
				}
				else {

					if($saldoCapital < ($cuota - ($cuotaIntereses + $cuotaSeguro))){
						$amortizacion->abono_capital = $saldoCapital;
						$amortizacion->abono_intereses = $cuotaIntereses;
						$amortizacion->abono_seguro_cartera = $cuotaSeguro;
						$amortizacion->total_cuota = $saldoCapital + $cuotaIntereses + $cuotaSeguro;
						$amortizacion->nuevo_saldo_capital = 0;
					}
					else {
						$amortizacion->abono_capital = $cuota;
						$amortizacion->abono_intereses = $cuotaIntereses;
						$amortizacion->abono_seguro_cartera = $cuotaSeguro;
						$amortizacion->nuevo_saldo_capital = $saldoCapital - $amortizacion->abono_capital;
						$amortizacion->total_cuota = $cuota + $cuotaIntereses + $cuotaSeguro;
					}
					$saldoCapital -= $amortizacion->abono_capital;
				}
				$amortizacion->estado_cuota = 'PENDIENTE';
				$nuevaAmortizacion->push($amortizacion);
			}
		}
		return $nuevaAmortizacion;
	}

	static public function efectivaToNominal($tasa, $periodicidadOrigen, $peiodicidadDestino) {
		$diasOrigen = ConversionHelper::diasPorPeriodicidad($periodicidadOrigen);
		$mensual = ConversionHelper::diasPorPeriodicidad("MENSUAL");
		$m = $mensual / $diasOrigen;
		$tasaNominal = (pow(1 + $tasa, $m) - 1);
		$diasDestino = ConversionHelper::diasPorPeriodicidad($peiodicidadDestino);
		$m = $diasDestino / $mensual;
		$tasaNominal *= $m;
		return $tasaNominal;
	}
}

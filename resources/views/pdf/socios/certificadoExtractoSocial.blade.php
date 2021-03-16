@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
	$eid = "%s %s";
	$eid = sprintf($eid, $tercero->tipoIdentificacion->codigo, number_format($tercero->numero_identificacion));
	$ter = $socio->tercero;
	$nombre = $ter->nombre;
	$id = "%s %s";
	$id = sprintf($id, $ter->tipoIdentificacion->codigo, number_format($ter->numero_identificacion));
	$beneficio = $configuracion->gasto_social_individual;
@endphp
<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title></title>
		<style type="text/css">
			@page {
				margin: 0cm 0cm;
			}

			/** Define now the real margins of every page in the PDF **/
			body {
				margin-top: 3cm;
				margin-left: 2cm;
				margin-right: 2cm;
				margin-bottom: 3cm;
			}

			header {
				position: fixed;
				top: 0cm;
				left: 0cm;
				right: 0cm;
				height: 2cm;

				/** Extra personal styles **/
				/**background-color: #888;**/
				/**color: white;**/
				text-align: left;
				line-height: 0.7cm;
			}

			footer {
				position: fixed;
				bottom: 0cm;
				left: 0cm;
				right: 0cm;
				height: 2cm;
				/**background-color: #888;**/
                /**color: white;**/
				text-align: center;
				line-height: 0.7cm;
			}

			.cth, .ctd{
				border: 1px solid #000;
			}

			table {
				font-size: 12px;
			}
			.resumen {
				color: #003b99;
			}
		</style>
	</head>
	<body>
		<header>
            <img src="{{ asset('storage/entidad/' . $imagen) }}">
        </header>
		<footer>
			<strong>{{ $tercero->nombre }}</strong><br>
			<strong>{{ $eid }}</strong>
		</footer>
		<center>
			EXTRACTO SOCIAL
			<br>
			AÑO {{ $anio }}
		</center>
		<br>
		<strong>
			<center>
				{{ $nombre }}
				<br>
				{{ $id }}
			</center>
		</strong>
		<br>
		<p>{{ $configuracion->mensaje_general }}</p>

		@if(count($dataAhorros) > 0)
			<br>
			<center style="text-decoration: underline;">AHORROS</center>
			<p>{{ $configuracion->mensaje_ahorros }}</p>

			<table align="center" cellpadding="3" cellspacing="0">
				<thead>
					<tr>
						<th class="cth">Modalidad</th>
						<th class="cth">Tipo de ahorro</th>
						<th class="cth">Saldo</th>
						<th class="cth">Abonos periodo</th>
						<th class="cth">Tasa E.A</th>
						<th class="cth">Interes periodo</th>
						<th class="cth">Interes externo</th>
						<th class="cth">Beneficio</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$totalSaldo = 0;
						$totalValorMovimiento = 0;
						$totalRendimientos = 0;
						$totalRendimientosExterno = 0;
						$totalBeneficio = 0;
						foreach ($dataAhorros as $ahorro) {
							$totalSaldo += $ahorro->valor_saldo;
							$totalValorMovimiento += $ahorro->valor_movimiento;
							$totalRendimientos += $ahorro->rendimientos;
							$totalRendimientosExterno += $ahorro->rendimientos_externo;
							$totalBeneficio += $ahorro->beneficio;
							$beneficio += $ahorro->beneficio;
							?>
							<tr>
								<td class="ctd">{{ $ahorro->modalidad_nombre }}</td>
								<td class="ctd">{{ $ahorro->tipo_ahorro }}</td>
								<td class="ctd" align="right">${{ number_format($ahorro->valor_saldo, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($ahorro->valor_movimiento, 0) }}</td>
								<td class="ctd" align="right">{{ number_format($ahorro->tasa, 2) }}%</td>
								<td class="ctd" align="right">${{ number_format($ahorro->rendimientos, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($ahorro->rendimientos_externo, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($ahorro->beneficio, 0) }}</td>
							</tr>
							<?php
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th class="cth" colspan="2">Totales</th>
						<th class="cth" align="right">${{ number_format($totalSaldo, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalValorMovimiento, 0) }}</th>
						<th class="cth" align="right"></th>
						<th class="cth" align="right">${{ number_format($totalRendimientos, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalRendimientosExterno, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalBeneficio, 0) }}</th>
					</tr>
				</tfoot>
			</table>
		@endif

		@if(count($dataCreditos) > 0)
			<br>
			<center style="text-decoration: underline;">CRÉDITOS</center>
			<p>{{ $configuracion->mensaje_creditos }}</p>

			<table align="center" cellpadding="3" cellspacing="0">
				<thead>
					<tr>
						<th class="cth">Modalidad</th>
						<th class="cth">Número cédito</th>
						<th class="cth">Valor inicial</th>
						<th class="cth">Tasa E.A</th>
						<th class="cth">Saldo capital</th>
						<th class="cth">Abonos periodo</th>
						<th class="cth">Interes periodo</th>
						<th class="cth">Interes externo</th>
						<th class="cth">Beneficio</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$totalValorCredito = 0;
						$totalValorSaldoCapital = 0;
						$totalValorMovimientoPeriodo = 0;
						$totalPagoInteresesPeriodo = 0;
						$totalInteresExterno = 0;
						$totalBeneficio = 0;
						foreach ($dataCreditos as $credito) {
							$totalValorCredito += $credito->valor_credito;
							$totalValorSaldoCapital += $credito->valor_saldo_capital;
							$totalValorMovimientoPeriodo += $credito->valor_movimiento_periodo;
							$totalPagoInteresesPeriodo += $credito->pago_intereses_periodo;
							$totalInteresExterno += $credito->interes_externo;
							$totalBeneficio += $credito->beneficio;
							$beneficio += $credito->beneficio;
							?>
							<tr>
								<td class="ctd">{{ $credito->modalidad_credito }}</td>
								<td class="ctd">{{ $credito->numero_obligacion }}</td>
								<td class="ctd" align="right">${{ number_format($credito->valor_credito, 0) }}</td>
								<td class="ctd" align="right">{{ number_format($credito->tasa, 2) }}%</td>
								<td class="ctd" align="right">${{ number_format($credito->valor_saldo_capital, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($credito->valor_movimiento_periodo, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($credito->pago_intereses_periodo, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($credito->interes_externo, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($credito->beneficio, 0) }}</td>
							</tr>
							<?php
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th class="cth" colspan="2">Totales</th>
						<th class="cth" align="right">${{ number_format($totalValorCredito, 0) }}</th>
						<th class="cth" align="right"></th>
						<th class="cth" align="right">${{ number_format($totalValorSaldoCapital, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalValorMovimientoPeriodo, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalPagoInteresesPeriodo, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalInteresExterno, 0) }}</th>
						<th class="cth" align="right">${{ number_format($totalBeneficio, 0) }}</th>
					</tr>
				</tfoot>
			</table>
		@endif

		@if(count($dataConvenios) > 0 && $dataConvenios[0]->operaciones > 0)
			<br>
			<center style="text-decoration: underline;">CONVENIOS</center>
			<p>{{ $configuracion->mensaje_convenios }}</p>

			<table align="center" cellpadding="3" cellspacing="0">
				<thead>
					<tr>
						<th class="cth">Operaciones</th>
						<th class="cth">Total periodo</th>
						<th class="cth">Promedio periodo</th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($dataConvenios as $convenios) {
							?>
							<tr>
								<td class="ctd">{{ $convenios->operaciones }}</td>
								<td class="ctd" align="right">${{ number_format($convenios->valor, 0) }}</td>
								<td class="ctd" align="right">${{ number_format($convenios->valor_promedio, 0) }}</td>
							</tr>
							<?php
						}
					?>
				</tbody>
			</table>
		@endif

		<br>
		<center style="text-decoration: underline;">INVERSIÓN SOCIAL</center>
		<p>{{ $configuracion->mensaje_inversion_social }}</p>

		<table align="center" cellpadding="3" cellspacing="0">
			<thead>
				<tr>
					<th class="cth">Benficio individual</th>
					<th class="cth">Inversión social total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="ctd" align="right">${{ number_format($configuracion->gasto_social_individual, 0) }}</td>
					<td class="ctd" align="right">${{ number_format($configuracion->gasto_social_total, 0) }}</td>
				</tr>
			</tbody>
		</table>

		<br><br>
		<center><p class="resumen">El beneficio total durante el año {{ $anio }}, está avaluado en <strong>${{ number_format($beneficio, 0) }}</strong></p></center>
	</body>
</html>

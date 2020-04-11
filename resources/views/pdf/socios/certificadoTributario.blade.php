@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
	$eid = "%s %s";
	$eid = sprintf($eid, $tercero->tipoIdentificacion->codigo, number_format($tercero->numero_identificacion));	
	$ter = $socio->tercero;
	$nombre = $ter->nombre;
	$id = "%s %s";
	$id = sprintf($id, $ter->tipoIdentificacion->codigo, number_format($ter->numero_identificacion));
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
			CERTIFICACIÓN DE RETENCIÓN EN LA FUENTE Y OTROS CONCEPTOS
			<br><br>
			AÑO GRAVABLE {{ $anio }}
		</center>
		<br><br>
		<strong>
			<center>
				{{ $nombre }}
				<br>
				{{ $id }}
			</center>
		</strong>
		<br><br>
		@if($data[0]->tipo == "CREDITO")
			<center style="text-decoration: underline;">CREDITOS</center>
			<br>
			<p>CLASE: CARTERA CONSUMO</p>
			<br>
			<table align="center" cellpadding="3" cellspacing="0">
				<thead>
					<tr>
						<th class="cth">No. Obligación</th>
						<th class="cth">Modalidad</th>
						<th class="cth">Saldo Capital</th>
						<th class="cth">Pago intereses periodo</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$totalCapital = 0;
						$totalInteres = 0;
						foreach($data as $dato) {
							if($dato->tipo == "CREDITO") {
								$totalCapital += $dato->capital;
								$totalInteres += $dato->interes;
								?>
								<tr>
									<td class="ctd">{{ $dato->numero_obligacion }}</td>
									<td class="ctd">{{ $dato->modalidad }}</td>
									<td class="ctd" align="right">${{ number_format($dato->capital) }}</td>
									<td class="ctd" align="right">${{ number_format($dato->interes) }}</td>
								</tr>
								<?php
							}
						}
					?>
				</tbody>
				<tfoot>
					<tr>
						<th class="cth" colspan="2">Totales</th>
						<th class="cth" align="right">${{ number_format($totalCapital) }}</th>
						<th class="cth" align="right">${{ number_format($totalInteres) }}</th>
					</tr>
				</tfoot>
			</table>
		@endif
		<br><br>
		<center style="text-decoration: underline;">AHORROS Y APORTES</center>
		<br>
		<p>CLASE: AHORROS/APORTES</p>
		<?php		
			foreach($data as $dato) {
				if($dato->tipo == "AHORRO") {
					?>
					<table align="center" cellpadding="3" cellspacing="0">
						<tbody>
							<tr>
								<th>SALDO AHORROS/APORTES</th>
								<td align="right">${{ number_format($dato->ahorros) }}</td>
							</tr>
							<tr>
								<th>TOTAL INTERESES PERIODO</th>
								<td align="right">${{ number_format($dato->interes) }}</td>
							</tr>
							<tr>
								<th>TOTAL RETENCIONES PERIODO</th>
								<td align="right">${{ number_format($dato->retefuente) }}</td>
							</tr>
						</tbody>
					</table>
					<?php
				}
			}
		?>
		<p>De acuerdo  con  el  Artículo  10  del  Decreto  Reglamentario  836  de  1991,  este  certificado  es  válido  sin  la  firma autógrafa.</p>
		<br>
	</body>
</html>
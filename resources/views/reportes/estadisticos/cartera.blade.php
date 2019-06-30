@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
@endphp
<div class="row">
	<div class="col-xs-2 text-center">
		<img src="{{ asset('storage/entidad/' . $imagen) }}">
	</div>
	<div class="col-xs-10 text-center">
		<br>
		<strong>
			<label class="text-primary">{{ $tercero->nombre }}</label>
			<br>
			{{ $tercero->tipoIdentificacion->codigo }}: {{ number_format($tercero->numero_identificacion) }}-{{ $tercero->digito_verificacion }} 
		</strong>
		<h4>
			Cartera
		</h4>
		{{ $fechaMostrar }}
	</div>
</div>
<br><br>
<div class="row">
	<div class="col-md-7 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Saldos por modalidad</th>
				</tr>
				<tr>
					<th>Modalidad</th>
					<th class="text-center">#</th>
					<th class="text-center">%</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">%</th>
					<th class="text-center">Tasa</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$cantidadCreditos = 0;
					$participacion = 0;
					$saldo = 0;
					$participacionSaldo = 0;
					$tasaPromedio = 0;
					foreach ($carteraPorModalidad as $element) {
						$cantidadCreditos += $element->cantidadCreditos;
						$participacion += $element->participacion;
						$saldo += $element->saldo;
						$participacionSaldo += $element->participacionSaldo;
						$tasaPromedio += (($element->tasaPromedio / 100) * $element->saldo);
						?>
						<tr>
							<td>{{ $element->modalidad }}</td>
							<td class="text-right">{{ number_format($element->cantidadCreditos, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacion, 2) }}%</td>
							<td class="text-right">${{ number_format($element->saldo, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacionSaldo, 2) }}%</td>
							<td class="text-right">{{ number_format($element->tasaPromedio, 2) }}%</td>
						</tr>
						<?php
					}
					$tasaPromedio = ($tasaPromedio / $saldo) * 100;
				?>
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($cantidadCreditos) }}</th>
				<th class="text-right">{{ number_format($participacion, 2) }}%</th>
				<th class="text-right">${{ number_format($saldo) }}</th>
				<th class="text-right">{{ number_format($participacionSaldo, 2) }}%</th>
				<th class="text-right">{{ number_format($tasaPromedio, 2) }}%</th>
			</tfoot>
		</table>
	</div>
	<div class="col-md-5 col-sm-12">
		<strong><p>Participación modalidades</p></strong>
		<div class="chart" id="saldosPorModalidad" style="height: 300px; position: relative;">
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Distribucion por calificación</th>
				</tr>
				<tr>
					<th>Calificación</th>
					<th class="text-center">#</th>
					<th class="text-center">%</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">%</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$cantidadCreditos = 0;
					$participacion = 0;
					$saldo = 0;
					$participacionSaldo = 0;
					$tasaPromedio = 0;
					foreach ($distribucionPorCalificacion as $element) {
						$cantidadCreditos += $element->cantidadCreditos;
						$participacion += $element->participacion;
						$saldo += $element->saldo;
						$participacionSaldo += $element->participacionSaldo;
						?>
						<tr>
							<td>{{ $element->calificacion }}</td>
							<td class="text-right">{{ number_format($element->cantidadCreditos, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacion, 2) }}%</td>
							<td class="text-right">${{ number_format($element->saldo, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacionSaldo, 2) }}%</td>
						</tr>
						<?php
					}
				?>
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($cantidadCreditos) }}</th>
				<th class="text-right">{{ number_format($participacion, 2) }}%</th>
				<th class="text-right">${{ number_format($saldo) }}</th>
				<th class="text-right">{{ number_format($participacionSaldo, 2) }}%</th>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Distribucion por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">#</th>
					<th class="text-center">%</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">%</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$cantidadCreditos = 0;
					$participacion = 0;
					$saldo = 0;
					$participacionSaldo = 0;
					$tasaPromedio = 0;
					foreach ($distribucionPorEmpresa as $element) {
						$cantidadCreditos += $element->cantidadCreditos;
						$participacion += $element->participacion;
						$saldo += $element->saldo;
						$participacionSaldo += $element->participacionSaldo;
						?>
						<tr>
							<td>{{ $element->empresa }}</td>
							<td class="text-right">{{ number_format($element->cantidadCreditos, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacion, 2) }}%</td>
							<td class="text-right">${{ number_format($element->saldo, 0) }}</td>
							<td class="text-right">{{ number_format($element->participacionSaldo, 2) }}%</td>
						</tr>
						<?php
					}
				?>
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($cantidadCreditos) }}</th>
				<th class="text-right">{{ number_format($participacion, 2) }}%</th>
				<th class="text-right">${{ number_format($saldo) }}</th>
				<th class="text-right">{{ number_format($participacionSaldo, 2) }}%</th>
			</tfoot>
		</table>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Top 15 por modalidad</h3>
	</div>
</div>

<div class="row">
	@php
		array_unshift($modalidades, "CONSOLIDADO");
	@endphp
	<div class="col-md-4 col-sm-12">
		<br>
		{!! Form::select("modalidad", $modalidades, 0, ["size" => 8, "style" => "width: 100%"]) !!}
	</div>
	<div class="col-md-8 col-sm-12 table-responsive">
		<table class="table table-responsive" id="topQuince">
			<thead>
				<tr>
					<th>Nombre</th>
					<th class="text-center">Ahorros</th>
					<th class="text-center">Cartera</th>
					<th class="text-center">% Modalidad</th>
					<th class="text-center">% Cartera</th>
				</tr>
			</thead>
			<tbody></tbody>
			<tfoot></tfoot>
		</table>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Comparativo cartera por mes</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="chart" id="comparativoAnual" style="height: 300px;"></div>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Colocación por modalidad</th>
				</tr>
				<tr>
					<th>Modalidad</th>
					<th class="text-center">#</th>
					<th class="text-center">Solicitud</th>
					<th class="text-center">Consolidado</th>
					<th class="text-center">Neto</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$cantidadCreditos = 0;
					$solicitud = 0;
					$consolidado = 0;
					$neto = 0;
					foreach ($colocacionesPorModalidad as $element) {
						$cantidadCreditos += $element->cantidad;
						$solicitud += $element->monto;
						$consolidado += $element->consolidado;
						$neto += $element->neto;
						?>
						<tr>
							<td>{{ $element->nombre }}</td>
							<td class="text-right">{{ number_format($element->cantidad) }}</td>
							<td class="text-right">${{ number_format($element->monto) }}</td>
							<td class="text-right">${{ number_format($element->consolidado) }}</td>
							<td class="text-right">${{ number_format($element->neto) }}</td>
						</tr>
						<?php
					}
				?>
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($cantidadCreditos) }}</th>
				<th class="text-right">${{ number_format($solicitud) }}</th>
				<th class="text-right">${{ number_format($consolidado) }}</th>
				<th class="text-right">${{ number_format($neto) }}</th>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Colocación por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">#</th>
					<th class="text-center">Solicitud</th>
					<th class="text-center">Consolidado</th>
					<th class="text-center">Neto</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$cantidadCreditos = 0;
					$solicitud = 0;
					$consolidado = 0;
					$neto = 0;
					foreach ($colocacionesPorEmpresa as $element) {
						$cantidadCreditos += $element->cantidad;
						$solicitud += $element->monto;
						$consolidado += $element->consolidado;
						$neto += $element->neto;
						?>
						<tr>
							<td>{{ $element->empresa }}</td>
							<td class="text-right">{{ number_format($element->cantidad) }}</td>
							<td class="text-right">${{ number_format($element->monto) }}</td>
							<td class="text-right">${{ number_format($element->consolidado) }}</td>
							<td class="text-right">${{ number_format($element->neto) }}</td>
						</tr>
						<?php
					}
				?>
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($cantidadCreditos) }}</th>
				<th class="text-right">${{ number_format($solicitud) }}</th>
				<th class="text-right">${{ number_format($consolidado) }}</th>
				<th class="text-right">${{ number_format($neto) }}</th>
			</tfoot>
		</table>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Comparativo colocaciones por mes</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="chart" id="comparativoColocaciones" style="height: 300px;"></div>
	</div>
</div>

<script type="text/javascript">
	var topQuince = [
		<?php
		foreach($topQuince as $item) {
			?>
			{
				DATA: [
					<?php
						foreach($item->DATA as $dato) {
							?>
							{
								"nombre": "{{ $dato->nombre }}",
								"ahorros": "{{ number_format($dato->ahorros, 0) }}",
								"cartera": "{{ number_format($dato->value, 0) }}",
								"participacionModalidad": "{{ number_format($dato->participacionModalidad, 2) }}",
								"participacionCartera": "{{ number_format($dato->participacionTotalCartera, 2) }}",
							},
							<?php
						}
					?>
				],
				"ahorros": "{{ number_format($item->ahorros) }}",
				"cartera": "{{ number_format($item->cartera) }}",
				"participacionModalidad": "{{ number_format($item->participacionModalidad, 2) }}",
				"participacionTotalCartera": "{{ number_format($item->participacionTotalCartera, 2) }}",
				"totalModalidadAhorro": "{{ number_format($item->totalModalidadAhorro) }}",
				"totalModalidadCartera": "{{ number_format($item->totalModalidadCartera) }}",
				"totalAhorros": "{{ number_format($item->totalAhorros) }}",
				"totalCartera": "{{ number_format($item->totalCartera) }}",
				"totalParticipacionCartera": "{{ number_format($item->totalParticipacionCartera, 2) }}",
			},
			<?php
		}
		?>
	];
	var saldosPorModalidadData = [
		@foreach ($carteraPorModalidad as $element)
			{label: "{{ $element->modalidad }}", value: {{ number_format($element->participacionSaldo, 2) }}},
		@endforeach
	];
	var comparativoCartera = [
		@foreach ($comparativoCartera as $cartera)
			{y: "{{ $cartera->mes }}", a: {{ $cartera->anterior }}, b: {{ $cartera->actual }}},
		@endforeach
	];
	var labels = ["{{ $fecha->year - 1 }}", "{{ $fecha->year }}"];
	var comparativoColocaciones = [
		@foreach ($comparativoColocaciones as $colocaciones)
			{y: "{{ $colocaciones->mes }}", a: {{ $colocaciones->anterior }}, b: {{ $colocaciones->actual }}},
		@endforeach
	];
</script>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Consolidado colocaciones año <small>(en millones)</small></h3>
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<canvas id="consolidadoColocacionesAnio" style="height: 150px;"></canvas>
	</div>
</div>

<script type="text/javascript">
	var labelsConsolidadoColocacionesAnioConfig = ['{!! implode("','", array_column($consolidadoColocacionesAnio, "mes")) !!}'];
	var dataAnterior = [{!! implode(",", array_map(function($value){return round($value / 1000000, 0);}, array_column($consolidadoColocacionesAnio, "anterior"))) !!}];
	var dataActual = [{!! implode(",", array_map(function($value){return round($value / 1000000, 0);}, array_column($consolidadoColocacionesAnio, "actual"))) !!}];
</script>
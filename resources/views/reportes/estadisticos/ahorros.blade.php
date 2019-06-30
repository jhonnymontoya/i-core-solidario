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
			Ahorros
		</h4>
		{{ $fechaMostrar }}
	</div>
</div>
<br><br>

<div class="row">
	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Saldos por modalidad</th>
				</tr>
				<tr>
					<th>Nombre</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$total = 0;
				@endphp
				@foreach ($saldosPorModalidad as $saldo)
					@php
						$total += $saldo->valor;
					@endphp
					<tr>
						<td>{{ $saldo->codigo . '-' . $saldo->nombre }}</td>
						<td class="text-right">${{ number_format($saldo->valor, 0) }}</td>
						<td class="text-right">{{ number_format($saldo->participacion, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">${{ number_format($total, 0) }}</th>
				<th class="text-right">100%</th>
			</tfoot>
		</table>
	</div>
	<div class="col-md-6 col-sm-12">
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
					<th colspan="3">Rangos de saldos de ahorros</th>
				</tr>
				<tr>
					<th>Rango</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$total = 0;
				@endphp
				@foreach ($rangosDeSaldos as $rango)
					@php
						$total += $rango->cantidad;
					@endphp
					<tr>
						<td>{{ $rango->nombre }}</td>
						<td class="text-right">{{ number_format($rango->cantidad) }}</td>
						<td class="text-right">{{ number_format($rango->porcentaje, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">{{ number_format($total) }}</th>
				<th class="text-right">100%</th>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Saldos de ahorro por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$total = 0;
				@endphp
				@foreach ($saldosPorEmpresa as $saldo)
					@php
						$total += $saldo->valor;
					@endphp
					<tr>
						<td>{{ $saldo->nombre }}</td>
						<td class="text-right">${{ number_format($saldo->valor) }}</td>
						<td class="text-right">{{ number_format($saldo->porcentaje, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<th>Totales:</th>
				<th class="text-right">${{ number_format($total) }}</th>
				<th class="text-right">100%</th>
			</tfoot>
		</table>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Top 10 por modalidad</h3>
	</div>
</div>

<div class="row">
	@php
		$keys = $topDiezPorModalidad->keys()->toArray();
		array_unshift($keys, "CONSOLIDADO");
	@endphp
	<div class="col-md-4 col-sm-12">
		<br>
		{!! Form::select("modalidad", $keys, 0, ["size" => 8, "style" => "width: 100%"]) !!}
	</div>
	<div class="col-md-8 col-sm-12 table-responsive">
		<table class="table table-responsive" id="topDiez">
			<thead>
				<tr>
					<th>Nombre</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Participación</th>
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
		<h3>Comparativo ahorros por mes</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="chart" id="comparativoAnual" style="height: 300px;"></div>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Neto variación ahorros por mes</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="chart" id="variacionAhorro" style="height: 300px;"></div>
	</div>
</div>
<script type="text/javascript">
	var topDiez = [
		{
			DATA: [
				<?php
					$subTotal = 0;
					$total_participacion = 0;
					foreach ($totalConsolidados as $key => $value) {
						$subTotal += $value;
						$participacion = ($value * 100) / $total;
						$total_participacion += $participacion;
						?>
						{
							nombre: "{{ $key }}",
							value: "{{ number_format($value) }}",
							participacion: "{{ number_format($participacion, 2) }}"
						},
						<?php
					}
				?>
			],
			totalSaldo: "{{ number_format($subTotal) }}",
			totalParticipacion: "{{ number_format($total_participacion, 2) }}",
			totalModalidad: "{{ number_format($total) }}",
			participacionModalidad: "100",
			totalAhorro: "{{ number_format($total) }}"
		},
		@foreach ($topDiezPorModalidad as $key => $items)
			<?php
				$subTotal = 0;
				foreach ($items as $item) {
					$subTotal += $item->valor;
				}
				if($subTotal == 0)continue;
			?>
			{
				DATA: [
				<?php
					$i = 0;
					$total_saldo = 0;
					$total_participacion = 0;
					foreach ($items as $element) {
						$i++;
						if($i > 10)break;
						$total_saldo += $element->valor;
						$participacion = ($element->valor * 100) / $subTotal;
						$total_participacion += $participacion;
						?>
						{
							nombre: "{{ $element->nombre }}",
							value: "{{ number_format($element->valor) }}",
							participacion: "{{ number_format($participacion, 2) }}"
						},
						<?php
					}
				?>
				],
				totalSaldo: "{{ number_format($total_saldo) }}",
				totalParticipacion: "{{ number_format($total_participacion, 2) }}",
				totalModalidad: "{{ number_format($subTotal) }}",
				participacionModalidad: "{{ number_format(($subTotal * 100) / $total, 2) }}",
				totalAhorro: "{{ number_format($total) }}"
			},
		@endforeach
	];
	var saldosPorModalidadData = [
		@foreach ($saldosPorModalidad as $saldo)
			{label: "{{ $saldo->nombre }}", value: {{ number_format($saldo->participacion, 2) }}},
		@endforeach
	];
	var comparativoAhorros = [
		@foreach ($comparativoAhorros as $ahorro)
			{y: "{{ $ahorro->mes }}", a: {{ $ahorro->anterior }}, b: {{ $ahorro->actual }}},
		@endforeach
	];
	var labels = ["{{ $fecha->year - 1 }}", "{{ $fecha->year }}"];

	var variacionAhorroData = [
		@foreach ($variacionAhorros as $variacion)
			{y: "{{ $variacion->mes }}", a: {{ $variacion->anterior }}, b: {{ $variacion->actual }}},
		@endforeach
	];
</script>
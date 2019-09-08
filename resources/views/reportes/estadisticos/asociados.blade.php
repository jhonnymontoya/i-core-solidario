@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
@endphp
<div class="row">
	<div class="col-2 text-center">
		<img src="{{ asset('storage/entidad/' . $imagen) }}">
	</div>
	<div class="col-10 text-center">
		<br>
		<strong>
			<label class="text-primary">{{ $tercero->nombre }}</label>
			<br>
			{{ $tercero->tipoIdentificacion->codigo }}: {{ number_format($tercero->numero_identificacion) }}-{{ $tercero->digito_verificacion }} 
		</strong>
		<h4>
			Asociados
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
					<th colspan="3">Asociados por género</th>
				</tr>
				<tr>
					<th>Género</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totales = 0;
				@endphp
				@foreach ($asociadosPorGenero as $item)
					@php
						$totales += $item->cantidad;
					@endphp
					<tr>
						<td>{{ $item->genero }}</td>
						<td class="text-right">{{ number_format($item->cantidad) }}</td>
						<td class="text-right">{{ $item->porcentaje }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format(100) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Asociados por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totales = 0;
				@endphp
				@foreach ($asociadosPorPagaduria as $item)
					@php
						$totales += $item->cantidad;
					@endphp
					<tr>
						<td>{{ $item->pagaduria }}</td>
						<td class="text-right">{{ number_format($item->cantidad) }}</td>
						<td class="text-right">{{ $item->porcentaje }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format(100) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Asociados por antigüedad</th>
				</tr>
				<tr>
					<th>Antigüedad</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totales = 0;
				@endphp
				@foreach ($asociadosPorAntiguedad as $item)
					@php
						$totales += $item->cantidad;
					@endphp
					<tr>
						<td>{{ $item->nombre }}</td>
						<td class="text-right">{{ number_format($item->cantidad) }}</td>
						<td class="text-right">{{ $item->porcentaje }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format(100) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Asociados por edad</th>
				</tr>
				<tr>
					<th>Edad</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totales = 0;
				@endphp
				@foreach ($asociadosPorEdad as $item)
					@php
						$totales += $item->cantidad;
					@endphp
					<tr>
						<td>{{ $item->nombre }}</td>
						<td class="text-right">{{ number_format($item->cantidad) }}</td>
						<td class="text-right">{{ $item->porcentaje }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format(100) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Asociados sin créditos</th>
				</tr>
				<tr>
					<th>Género</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totales = 0;
					$porcentaje = 0;
				@endphp
				@foreach ($asociadosSinCreditos as $item)
					@php
						$totales += $item->cantidad;
						$porcentaje += $item->porcentaje;
					@endphp
					<tr>
						<td>{{ $item->genero }}</td>
						<td class="text-right">{{ number_format($item->cantidad) }}</td>
						<td class="text-right">{{ $item->porcentaje }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format($porcentaje, 2) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Afiliaciones por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$agrupacion = collect($asociadosDelMes)->groupBy("pagaduria");
					$totales = count($asociadosDelMes);
					$porcentaje = 0;
				@endphp
				@foreach ($agrupacion as $key => $item)
					@php
						$subTotal = $item->count();
						$subTotalPorcentaje = ($subTotal * 100) / $totales;
						$porcentaje += $subTotalPorcentaje;
					@endphp
					<tr>
						<td>{{ $key }}</td>
						<td class="text-right">{{ number_format($subTotal) }}</td>
						<td class="text-right">{{ number_format($subTotalPorcentaje, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($totales) }}</th>
					<th class="text-right">{{ number_format($porcentaje, 2) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>

</div>

<div class="row">

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Retiros por causal</th>
				</tr>
				<tr>
					<th>Tipo causal</th>
					<th class="text-center">Cantidad</th>
				</tr>
			</thead>
			<tbody>
				@php
					$total = 0;
				@endphp
				@foreach (collect($retirosDelMes)->groupBy('tipo_causa_retiro') as $key => $item)
					@php
						$total += $item->count();
					@endphp
					<tr>
						<td>{{ $key }}</td>
						<td class="text-right">{{ number_format($item->count()) }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($total) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>

	<div class="col-md-6 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Retiros por empresa</th>
				</tr>
				<tr>
					<th>Empresa</th>
					<th class="text-center">Cantidad</th>
					<th class="text-center">Participación</th>
				</tr>
			</thead>
			<tbody>
				@php
					$agrupacion = collect($retirosDelMes)->groupBy('pagaduria');
					$total = count($retirosDelMes);
					$porcentaje = 0;
				@endphp
				@foreach ($agrupacion as $key => $item)
					@php
						$subTotal = $item->count();
						$subTotalPorcentaje = ($subTotal * 100) / $total;
						$porcentaje += $subTotalPorcentaje;
					@endphp
					<tr>
						<td>{{ $key }}</td>
						<td class="text-right">{{ number_format($subTotal) }}</td>
						<td class="text-right">{{ number_format($subTotalPorcentaje, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales:</th>
					<th class="text-right">{{ number_format($total) }}</th>
					<th class="text-right">{{ number_format($porcentaje, 2) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Afiliaciones del mes</th>
				</tr>
				<tr>
					<th class="text-center">Identificación</th>
					<th>Nombre</th>
					<th>Fecha afiliación</th>
					<th>Empresa</th>
					<th class="text-center">Cuota obligatoría</th>
					<th class="text-center">Cuota voluntaría</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				@php
					$cuotasObligatorias = 0;
					$cuotasVoluntarias = 0;
					$total = 0;
				@endphp
				@foreach ($asociadosDelMes as $item)
					@php
						$cuotasObligatorias += $item->cuota_oblogatoria;
						$cuotasVoluntarias += $item->cuota_voluntaria;
						$total += ($item->cuota_oblogatoria + $item->cuota_voluntaria);
					@endphp
					<tr>
						<td class="text-right">{{ $item->numero_identificacion }}</td>
						<td>{{ $item->nombre }}</td>
						<td class="text-center">{{ $item->fecha_afiliacion }}</td>
						<td>{{ $item->pagaduria }}</td>
						<td class="text-right">${{ number_format($item->cuota_oblogatoria) }}</td>
						<td class="text-right">${{ number_format($item->cuota_voluntaria) }}</td>
						<td class="text-right">${{ number_format($item->cuota_oblogatoria + $item->cuota_voluntaria) }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales</th>
					<th>{{ count($asociadosDelMes) }}</th>
					<th></th>
					<th></th>
					<th class="text-right">${{ number_format($cuotasObligatorias) }}</th>
					<th class="text-right">${{ number_format($cuotasVoluntarias) }}</th>
					<th class="text-right">${{ number_format($total) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-md-12 col-sm-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="3">Retiros del mes</th>
				</tr>
				<tr>
					<th class="text-center">Identificación</th>
					<th>Nombre</th>
					<th>Fecha retiro</th>
					<th>Causal</th>
					<th>Tipo causal</th>
					<th class="text-center">Ahorros</th>
					<th class="text-center">Créditos</th>
					<th class="text-center">Total</th>
				</tr>
			</thead>
			<tbody>
				@php
					$ahorros = 0;
					$creditos = 0;
					$total = 0;
				@endphp
				@foreach ($retirosDelMes as $item)
					@php
						$ahorros += $item->ahorros;
						$creditos += $item->cartera;
						$subTotal = $item->ahorros - $item->cartera;
						$total += $subTotal;
					@endphp
					<tr>
						<td class="text-right">{{ $item->numero_identificacion }}</td>
						<td>{{ $item->nombre }}</td>
						<td class="text-center">{{ $item->fecha_retiro }}</td>
						<td>{{ $item->causal }}</td>
						<td>{{ $item->tipo_causa_retiro }}</td>
						<td class="text-right">${{ number_format($item->ahorros) }}</td>
						<td class="text-right">${{ number_format($item->cartera) }}</td>
						<td class="text-right {{ ($subTotal < 0 ? 'text-danger' : '') }}">${{ number_format($subTotal) }}</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales</th>
					<th>{{ count($retirosDelMes) }}</th>
					<th></th>
					<th></th>
					<th></th>
					<th class="text-right">${{ number_format($ahorros) }}</th>
					<th class="text-right">${{ number_format($creditos) }}</th>
					<th class="text-right">${{ number_format($total) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

<hr>
<div class="row">
	<div class="col-md-12">
		<h3>Afiliaciones por mes</h3>
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="chart" id="afiliacionesPorMes" style="height: 300px;"></div>
	</div>
</div>
<script type="text/javascript">
	var afiliacionesPorMes = [
		@foreach ($afiliacionesPorMes as $afiliacion)
			{y: "{{ $afiliacion->mes }}", a: {{ $afiliacion->anterior }}, b: {{ $afiliacion->cantidad }}},
		@endforeach
	];
	var labels = ["{{ $fecha->year - 1 }}", "{{ $fecha->year }}"];
</script>
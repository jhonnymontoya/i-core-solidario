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
			Cuotas de ahorros
		</h4>
		<h5>Fecha reporte: {{ $fecha }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="text-center">Identificación</th>
					<th>Nombre</th>
					<th>Estado socio</th>
					<th>Modalidad</th>
					<th>Tipo ahorro</th>
					<th class="text-center">Valor</th>
					<th>Periodicidad</th>
					<th class="text-center">Valor mensual</th>
					<th>Fecha inicio</th>
					<th>Fecha final</th>

				</tr>
			</thead>
			<tbody>
				@foreach ($cuotas as $cuota)
					<tr>
						<td class="text-right">{{ $cuota->numero_identificacion }}</td>
						<td>{{ $cuota->nombre}}</td>
						<td>{{ $cuota->estado_socio }}</td>
						<td>{{ $cuota->modalidad }}</td>
						<td>{{ $cuota->tipo_ahorro }}</td>
						<td class="text-right">${{ number_format($cuota->valor) }}</td>
						<td>{{ $cuota->periodicidad }}</td>
						<td class="text-right">${{ number_format($cuota->valor_mes) }}</td>
						<td>{{ $cuota->fecha_inicio }}</td>
						<td>{{ $cuota->fecha_fin }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

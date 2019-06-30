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
			Colocación de créditos
		</h4>
		<h5>Desde: {{ $fechaInicio }} Hasta: {{ $fechaFin }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th class="text-center">Identificación</th>
					<th>Nombre</th>
					<th>Obligación</th>
					<th>Modalidad</th>
					<th class="text-center">Valor crédito</th>
					<th class="text-center">Consolidado</th>
					<th class="text-center">Neto</th>
					<th>Fecha</th>
					<th>Tasa</th>
					<th>Plazo</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($colocacionesCreditos as $colocacion)
					<tr>
						<td class="text-right">{{ $colocacion->identificacion }}</td>
						<td>{{ $colocacion->nombre }}</td>
						<td>{{ $colocacion->obligacion }}</td>
						<td>{{ $colocacion->modalidad }}</td>
						<td class="text-right">${{ number_format($colocacion->valor) }}</td>
						<td class="text-right">${{ number_format($colocacion->consolidado) }}</td>
						<td class="text-right">${{ number_format($colocacion->neto) }}</td>
						<td>{{ $colocacion->fecha }}</td>
						<td class="text-right">{{ number_format($colocacion->tasa, 2) }}%</td>
						<td class="text-right">{{ $colocacion->plazo }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
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
			Detalle información tributaria
		</h4>
		<h5><strong>Impuesto:</strong> {{ $impuesto->nombre }}</h5>
		<h5><strong>Periodo desde</strong>: {{ $fechaInicio }} <strong>Hasta</strong>: {{ $fechaFinal }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Fecha movimiento</th>
					<th>Comprobante</th>
					<th>Número</th>
					<th>Identificación</th>
					<th>Tercero</th>
					<th>Impuesto</th>
					<th>Concepto</th>
					<th>Cuenta</th>
					<th class="text-center">Tasa</th>
					<th class="text-center">Base</th>
					<th class="text-center">Impuesto</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($detalle as $info)
					<tr>
						<td>{{ $info->fecha_movimiento }}</td>
						<td>{{ $info->codigo }}</td>
						<td>{{ $info->numero_comprobante }}</td>
						<td>{{ $info->tercero_identificacion }}</td>
						<td>{{ $info->tercero }}</td>
						<td>{{ $info->impuesto }}</td>
						<td>{{ $info->nombre }}</td>
						<td>{{ $info->cuif_codigo }}</td>
						<td class="text-center">{{ number_format($info->tasa, 2) }}%</td>
						<td class="text-center">${{ number_format($info->base) }}</td>
						<td class="text-center">${{ number_format($info->valor_impuesto) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

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
			Detalle recaudos de nómina por proceso
		</h4>
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
					<th>Tipo</th>
					<th>Concepto</th>
					<th class="text-center">Capital generado</th>
					<th class="text-center">Interes generado</th>
					<th class="text-center">Seguro generado</th>
					<th class="text-center">Total generado</th>
					<th class="text-center">Capital aplicado</th>
					<th class="text-center">Interes aplicado</th>
					<th class="text-center">Seguro aplicado</th>
					<th class="text-center">Total aplicado</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($recaudos as $recaudo)
					<tr>
						<td class="text-right">{{ $recaudo->identificacion }}</td>
						<td>{{ $recaudo->nombre}}</td>
						<td>{{ $recaudo->tipo}}</td>
						<td>{{ $recaudo->concepto}}</td>
						<td class="text-right">${{ number_format($recaudo->capitalgenerado) }}</td>
						<td class="text-right">${{ number_format($recaudo->interesgenerado) }}</td>
						<td class="text-right">${{ number_format($recaudo->segurogenerado) }}</td>
						<td class="text-right"><strong>${{ number_format($recaudo->totalgenerado) }}</strong></td>
						<td class="text-right">${{ number_format($recaudo->capitalaplicado) }}</td>
						<td class="text-right">${{ number_format($recaudo->interesesaplicado) }}</td>
						<td class="text-right">${{ number_format($recaudo->seguroaplicado) }}</td>
						<td class="text-right"><strong>${{ number_format($recaudo->totalaplicado) }}</strong></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

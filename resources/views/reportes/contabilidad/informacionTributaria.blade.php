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
			Información tributaria
		</h4>
		<h5><strong>Impuesto:</strong> {{ $impuesto->nombre }}</h5>
		<h5><strong>Periodo desde</strong>: {{ $fechaInicio }} <strong>Hasta</strong>: {{ $fechaFinal }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Concepto</th>
					<th class="text-center">Base</th>
					<th class="text-center">Impuesto</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($informacionesTributarias as $info)
					<tr>
						<td>{{ $info->nombre }}</td>
						<td class="text-center">${{ number_format($info->base) }}</td>
						<td class="text-center">${{ number_format($info->impuesto) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

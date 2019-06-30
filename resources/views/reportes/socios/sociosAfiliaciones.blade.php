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
			Afiliaciones en un periodo de tiempo
		</h4>
		<h5><strong>Desde:</strong> {{ $fechaInicio }} - <strong>Hasta:</strong> {{ $fechaFinal }}</h5>
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
					<th>Afiliación</th>
					<th>Empresa</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($sociosAfiliaciones as $socioAfiliacion)
					<tr>
						<td class="text-right">{{ $socioAfiliacion->identificacion }}</td>
						<td>{{ $socioAfiliacion->nombre }}</td>
						<td>{{ $socioAfiliacion->afiliacion }}</td>
						<td>{{ $socioAfiliacion->empresa }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
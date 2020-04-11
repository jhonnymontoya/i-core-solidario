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
			Socios activos
		</h4>
		<h5>Fecha de corte {{ $fechaCorte }}</h5>
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
					<th>Afiliación</th>
					<th>Empresa</th>
					<th>Estado actual</th>
					
				</tr>
			</thead>
			<tbody>
				@foreach ($socios as $socio)
					<tr>
						<td class="text-right">{{ $socio->identificacion }}</td>
						<td>{{ $socio->nombre }}</td>
						<td>{{ $socio->afiliacion }}</td>
						<td>{{ $socio->empresa }}</td>
						<td>{{ $socio->estado }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
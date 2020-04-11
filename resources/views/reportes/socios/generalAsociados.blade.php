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
			Información General de Asociados
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
					<th>Estado</th>
					<th>Sexo</th>
					<th>Nacimiento</th>
					<th>Empresa</th>
					<th>Afiliación</th>
					<th>Antigüedad</th>
					<th>Estado Civil</th>
					<th>Ingreso Empresa</th>
					<th>Tipo Contrato</th>
					<th class="text-center">Sueldo</th>
					<th>País</th>
					<th>Departamento</th>
					<th>Ciudad</th>
					<th>Dirección</th>
					<th>Teléfono</th>
					<th>Móvil</th>
					<th>E-Mail</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($generalAsociados as $generalAsociado)
					<tr>
						<td class="text-right">{{ $generalAsociado->identificacion }}</td>
						<td>{{ $generalAsociado->nombre }}</td>
						<td>{{ $generalAsociado->estado }}</td>
						<td>{{ $generalAsociado->sexo }}</td>
						<td>{{ $generalAsociado->nacimiento }}</td>
						<td>{{ $generalAsociado->empresa }}</td>
						<td>{{ $generalAsociado->afiliacion }}</td>
						<td>{{ $generalAsociado->antiguedad }}</td>
						<td>{{ $generalAsociado->ecivil }}</td>
						<td>{{ $generalAsociado->iempresa }}</td>
						<td>{{ $generalAsociado->contrato }}</td>
						<td class="text-right">${{ number_format($generalAsociado->sueldo, 0) }}</td>
						<td>{{ $generalAsociado->pais }}</td>
						<td>{{ $generalAsociado->departamento }}</td>
						<td>{{ $generalAsociado->ciudad }}</td>
						<td>{{ $generalAsociado->direccion }}</td>
						<td>{{ $generalAsociado->telefono }}</td>
						<td>{{ $generalAsociado->movil }}</td>
						<td>{{ $generalAsociado->email }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
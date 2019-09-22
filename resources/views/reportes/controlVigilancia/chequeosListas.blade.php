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
			Chequeo en Listas de Control por Rango de Tiempo
		</h4>
		<h5><strong>Desde</strong>: {{ $fechaInicio }} <strong>Hasta</strong>: {{ $fechaFin }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Fecha Registro</th>
					<th>Usuario</th>
					<th>Número Identificación</th>
					<th>Primer Nombre</th>
					<th>Segundo Nombre</th>
					<th>Primer Apellido</th>
					<th>Segundo Apellido</th>
					<th>Tercero</th>
					<th>Asociado</th>
					<th>Empleado</th>
					<th>Proveedor</th>
					<th>PEP</th>
					<th>Departamento</th>
					<th>Ciudad</th>
					<th class="text-center">Porcentaje Coincidencia</th>
					<th>Tipo Coincidencia</th>
					<th>Lista</th>
					<th>Fecha Lista</th>
					<th>Lista Número Documento</th>
					<th>Lista Primer Nombre</th>
					<th>Lista Segundo Nombre</th>
					<th>Lista Primer Apellido</th>
					<th>Lista Segundo Apellido</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($chequeosListas as $chequeo)
					<tr>
						<td>{{ $chequeo->fecha_proceso }}</td>
						<td>{{ $chequeo->usuario }}</td>
						<td>{{ $chequeo->identificacion }}</td>
						<td>{{ $chequeo->primer_nombre }}</td>
						<td>{{ $chequeo->segundo_nombre }}</td>
						<td>{{ $chequeo->primer_apellido }}</td>
						<td>{{ $chequeo->segundo_apellido }}</td>
						<td>{{ $chequeo->tercero }}</td>
						<td>{{ $chequeo->asociado }}</td>
						<td>{{ $chequeo->empleado }}</td>
						<td>{{ $chequeo->proveedor }}</td>
						<td>{{ $chequeo->pep }}</td>
						<td>{{ $chequeo->departamento }}</td>
						<td>{{ $chequeo->ciudad }}</td>
						<td class="text-right">{{ number_format($chequeo->coincidencia) }}%</td>
						<td>{{ $chequeo->tipo }}</td>
						<td>{{ $chequeo->lista }}</td>
						<td>{{ $chequeo->fecha_lista }}</td>
						<td>{{ $chequeo->documento_lista }}</td>
						<td>{{ $chequeo->lista_primer_nombre }}</td>
						<td>{{ $chequeo->lista_segundo_nombre }}</td>
						<td>{{ $chequeo->lista_primer_apellido }}</td>
						<td>{{ $chequeo->lista_segundo_apellido }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

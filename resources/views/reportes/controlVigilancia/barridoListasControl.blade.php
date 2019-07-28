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
			Barrido listas de control
		</h4>
		<h5><strong>Fecha reporte</strong>: {{ $fechaGeneracion->format("d/m/Y H:i:s") }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Persona</th>
					<th>Tipo Identificacion</th>
					<th>Numero Identificacion</th>
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
					<th>Tipo Lista</th>
					<th>Fecha Lista</th>
					<th>Lista Persona</th>
					<th>Lista Tipo Documento</th>
					<th>Lista Numero Documento</th>
					<th>Lista Primer Nombre</th>
					<th>Lista Segundo Nombre</th>
					<th>Lista Primer Apellido</th>
					<th>Lista Segundo Apellido</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($barrido as $dato)
					<tr>
						<td>{{ $dato->persona }}</td>
						<td>{{ $dato->tipo_identificacion }}</td>
						<td>{{ $dato->numero_identificacion }}</td>
						<td>{{ $dato->primer_nombre }}</td>
						<td>{{ $dato->segundo_nombre }}</td>
						<td>{{ $dato->primer_apellido }}</td>
						<td>{{ $dato->segundo_apellido }}</td>
						<td>{{ $dato->es_tercero }}</td>
						<td>{{ $dato->es_asociado }}</td>
						<td>{{ $dato->es_empleado }}</td>
						<td>{{ $dato->es_proveedor }}</td>
						<td>{{ $dato->es_pep }}</td>
						<td>{{ $dato->departamento }}</td>
						<td>{{ $dato->ciudad }}</td>
						<td class="text-right">{{ number_format($dato->porcentaje_coincidencia) }}%</td>
						<td>{{ $dato->tipo_coincidencia }}</td>
						<td>{{ $dato->tipo_lista }}</td>
						<td>{{ $dato->fecha_lista }}</td>
						<td>{{ $dato->tipo }}</td>
						<td>{{ $dato->tipo_documento }}</td>
						<td>{{ $dato->numero_documento }}</td>
						<td>{{ $dato->lista_primer_nombre }}</td>
						<td>{{ $dato->lista_segundo_nombre }}</td>
						<td>{{ $dato->lista_primer_apellido }}</td>
						<td>{{ $dato->lista_segundo_apellido }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

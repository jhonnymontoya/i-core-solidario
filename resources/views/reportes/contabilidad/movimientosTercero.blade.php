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
			Movimiento contable por tercero
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
					<th>Fecha</th>
					<th>Comprobante</th>
					<th>Número</th>
					<th>Identificación</th>
					<th>Nombre</th>
					<th>Código</th>
					<th>Cuenta</th>
					<th class="text-center">Débitos</th>
					<th class="text-center">Créditos</th>
					<th class="text-center">Referencia</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($movimientos as $movimiento)
					<tr>
						<td>{{ $movimiento->fecha }}</td>
						<td>{{ $movimiento->comprobante }}</td>
						<td>{{ $movimiento->numero }}</td>
						<td class="text-right">{{ $movimiento->identificacion }}</td>
						<td>{{ $movimiento->nombre }}</td>
						<td>{{ $movimiento->cuenta }}</td>
						<td>{{ $movimiento->nombrecuenta }}</td>
						<td class="text-right">${{ number_format($movimiento->debito, 0) }}</td>
						<td class="text-right">${{ number_format($movimiento->credito, 0) }}</td>
						<td class="text-right">{{ $movimiento->referencia }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
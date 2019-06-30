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
			Saldos de ahorros individuales por modalidad
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
					<th>Modalidad</th>
					<th class="text-center">Saldo</th>
					
				</tr>
			</thead>
			<tbody>
				@foreach ($saldos as $saldo)
					<tr>
						<td class="text-right">{{ $saldo->Identificacion }}</td>
						<td>{{ $saldo->Nombre}}</td>
						<td>{{ $saldo->Modalidad }}</td>
						<td class="text-right">${{ number_format($saldo->Saldo) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
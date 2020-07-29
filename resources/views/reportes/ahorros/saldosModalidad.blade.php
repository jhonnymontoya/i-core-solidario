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
			Saldos de ahorros individuales por modalidad
		</h4>
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
					<th>Modalidad</th>
					<th class="text-center">Valor cuota</th>
					<th>Periodicidad cuota</th>
					<th class="text-center">Cuota mensual</th>
					<th class="text-center">Saldo</th>

				</tr>
			</thead>
			<tbody>
				@foreach ($saldos as $saldo)
					<tr>
						<td class="text-right">{{ $saldo->identificacion }}</td>
						<td>{{ $saldo->nombre}}</td>
						<td>{{ $saldo->modalidad }}</td>
						<td class="text-right">${{ number_format($saldo->valor_cuota) }}</td>
						<td>{{ $saldo->periodicidad }}</td>
						<td class="text-right">${{ number_format($saldo->valor_cuota_mes) }}</td>
						<td class="text-right">${{ number_format($saldo->saldo) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

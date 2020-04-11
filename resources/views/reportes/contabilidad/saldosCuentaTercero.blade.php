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
			Saldos cuenta por tercero
		</h4>
		<h5>Fecha consulta {{ $fechaCorte }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Identificación</th>
					<th>Nombre</th>
					<th>Saldo anterior</th>
					<th>Débitos</th>
					<th>Créditos</th>
					<th>Saldo</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($saldos as $saldo)
					<tr>
						<td class="text-center">{{ $saldo->identificacion }}</td>
						<td>{{ $saldo->nombre }}</td>
						<td class="text-center">${{ number_format($saldo->saldo_anterior, 0) }}</td>
						<td class="text-center">${{ number_format($saldo->debitos, 0) }}</td>
						<td class="text-center">${{ number_format($saldo->creditos, 0) }}</td>
						<td class="text-center">${{ number_format($saldo->saldo_nuevo, 0) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
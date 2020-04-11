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
			Saldos de cartera por persona
		</h4>
		<h5>Fecha de corte: {{ $fechaCorte }}</h5>
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
					<th class="text-center">Fecha nacimiento</th>
					<th class="text-center">Saldo consolidado</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($saldosConsolidados as $saldos)
					<tr>
						<td class="text-right">{{ $saldos->identificacion }}</td>
						<td>{{ $saldos->nombre }}</td>
						<td class="text-center">{{ $saldos->nacimiento }}</td>
						<td class="text-right">${{ number_format($saldos->saldo) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
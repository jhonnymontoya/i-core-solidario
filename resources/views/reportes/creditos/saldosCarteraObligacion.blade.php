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
			Saldos de cartera por obligación
		</h4>
		<h5>Fecha de corte {{ $fechaCorte }}</h5>
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
					<th>Obligación</th>
					<th>Modalidad</th>
					<th>Fecha</th>
					<th class="text-center">Valor inicial</th>
					<th>Tasa</th>
					<th>Plazo</th>
					<th class="text-center">Valor cuota</th>
					<th class="text-center">Fecha inicio</th>
					<th class="text-center">Saldo</th>
					
				</tr>
			</thead>
			<tbody>
				@foreach ($saldosObligacion as $saldo)
					<tr>
						<td class="text-right">{{ $saldo->identificacion }}</td>
						<td>{{ $saldo->nombre }}</td>
						<td>{{ $saldo->obligacion }}</td>
						<td>{{ $saldo->modalidad }}</td>
						<td>{{ $saldo->fecha }}</td>
						<td class="text-right">${{ number_format($saldo->valorinicial) }}</td>
						<td class="text-right">{{ number_format($saldo->tasa, 2) }}%</td>
						<td class="text-right">{{ $saldo->plazo }}</td>
						<td class="text-right">${{ number_format($saldo->valorcuota) }}</td>
						<td>{{ $saldo->fechainicio }}</td>
						<td class="text-right">${{ number_format($saldo->saldo) }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
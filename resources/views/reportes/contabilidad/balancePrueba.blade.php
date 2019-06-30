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
			Balance de prueba
		</h4>
		<h5>Periodo: {{ $mes . ' ' . $anio }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Código</th>
					<th>Nombre</th>
					<th class="text-center">Saldo anterior</th>
					<th class="text-center">Movimientos débitos</th>
					<th class="text-center">Movimientos créditos</th>
					<th class="text-center">Saldo nuevo</th>
				</tr>
			</thead>
			<tbody>
				@foreach($movimientos as $movimiento)
					@if($movimiento->nivel == 1)
						<tr>
							<td><strong>{{ $movimiento->cuenta }}</strong></td>
							<td><strong>{{ $movimiento->nombre }}</strong></td>
							<td class="text-right"><strong>${{ number_format($movimiento->saldo_anterior, 2) }}</strong></td>
							<td class="text-right"><strong>${{ number_format($movimiento->debitos, 2) }}</strong></td>
							<td class="text-right"><strong>${{ number_format($movimiento->creditos, 2) }}</strong></td>
							<td class="text-right"><strong>${{ number_format($movimiento->saldo, 2) }}</strong></td>
						</tr>
					@else
						<tr>
							<td>{{ $movimiento->cuenta }}</td>
							<td>{{ $movimiento->nombre }}</td>
							<td class="text-right">${{ number_format($movimiento->saldo_anterior, 2) }}</td>
							<td class="text-right">${{ number_format($movimiento->debitos, 2) }}</td>
							<td class="text-right">${{ number_format($movimiento->creditos, 2) }}</td>
							<td class="text-right">${{ number_format($movimiento->saldo, 2) }}</td>
						</tr>
					@endif
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" class="text-right">Débitos</th>
					<th class="text-right">${{ number_format($resultados['saldoAnteriorDebito'], 0) }}</th>
					<th class="text-right">${{ number_format($resultados['debitos'], 0) }}</th>
					<th class="text-right">${{ number_format($resultados['creditos'], 0) }}</th>
					<th class="text-right">${{ number_format($resultados['saldoDebito'], 0) }}</th>
				</tr>
				<tr>
					<th colspan="2" class="text-right">Créditos</th>
					<th class="text-right">${{ number_format($resultados['saldoAnteriorCredito'], 0) }}</th>
					<th class="text-right"></th>
					<th class="text-right"></th>
					<th class="text-right">${{ number_format($resultados['saldoCredito'], 0) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

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
			Auxiliar Contable
		</h4>
		<h5><strong>Desde</strong>: {{ $fechaInferior }} <strong>Hasta</strong>: {{ $fechaSuperior }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Fecha</th>
					<th>Comp.</th>
					<th>Número</th>
					<th>Identificación</th>
					<th>Nombre</th>
					<th>Código</th>
					<th>Cuenta</th>
					<th>Descripción</th>
					<th>Referencia</th>
					<th>Débitos</th>
					<th>Créditos</th>
					<th>Saldo</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="font-size: 12px;" colspan="11">Saldo anterior</td>
					<td style="font-size: 12px;" class="text-right">{{ number_format($saldo, 0) }}</td>
				</tr>
				@php
					$nuevoSaldo = 0;
				@endphp
				@foreach ($movimientos as $movimiento)
					<tr>
						<td style="font-size: 12px;">{{ $movimiento->Fecha }}</td>
						<td style="font-size: 12px;">{{ $movimiento->Comprobante}}</td>
						<td style="font-size: 12px;">{{ $movimiento->Numero }}</td>
						<td style="font-size: 12px;" class="text-right">{{ $movimiento->Identificacion }}</td>
						<td style="font-size: 10px;">{{ str_limit($movimiento->Nombre, 20) }}</td>
						<td style="font-size: 12px;">{{ $movimiento->Cuenta }}</td>
						<td style="font-size: 10px;">{{ str_limit($movimiento->NombreCuenta, 15) }}</td>
						<td style="font-size: 10px;">{{ str_limit($movimiento->Descripcion, 30) }}</td>
						<td style="font-size: 12px;">{{ $movimiento->Referencia }}</td>
						<td style="font-size: 12px;" class="text-right">{{ number_format($movimiento->Debito) }}</td>
						<td style="font-size: 12px;" class="text-right">{{ number_format($movimiento->Credito) }}</td>
						<td style="font-size: 12px;" class="text-right">{{ number_format($movimiento->saldo) }}</td>
					</tr>
					@php
						$nuevoSaldo = $movimiento->saldo;
					@endphp
				@endforeach
				<tr>
					<td style="font-size: 12px;" colspan="11">Saldo final</td>
					<td style="font-size: 12px;" class="text-right">{{ number_format($nuevoSaldo, 0) }}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

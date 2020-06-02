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
			Libro Mayor oficial
		</h4>
		<h5>Año {{ $anio }} Mes {{ $mes }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Código Cuenta</th>
					<th>Nombre cuenta</th>
					<th class="text-center">Saldo Anterior</th>
					<th class="text-center">Débitos</th>
					<th class="text-center">Créditos</th>
					<th class="text-center">Nuevo Saldo</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$debitos = 0;
					$creditos = 0;

					$saldoAnteriorDebitos = 0;
					$saldoAnteriorCreditos = 0;

					$nuevoSaldoDebitos = 0;
					$nuevoSaldoCreditos = 0;

					foreach($detalles as $detalle) {
						$debitos += $detalle->debitos;
						$creditos += $detalle->creditos;

						if($detalle->naturaleza == 'DÉBITO') {
							$saldoAnteriorDebitos += $detalle->saldo_anterior;
							$nuevoSaldoDebitos += $detalle->nuevo_saldo;
						}
						else {
							$saldoAnteriorCreditos += $detalle->saldo_anterior;
							$nuevoSaldoCreditos += $detalle->nuevo_saldo;
						}
						?>
						<tr>
							<td>{{ $detalle->cuenta_codigo }}</td>
							<td>{{ $detalle->cuenta_nombre }}</td>
							<td class="text-right">${{ number_format($detalle->saldo_anterior, 2) }}</td>
							<td class="text-right">${{ number_format($detalle->debitos, 2) }}</td>
							<td class="text-right">${{ number_format($detalle->creditos, 2) }}</td>
							<td class="text-right">${{ number_format($detalle->nuevo_saldo, 2) }}</td>
						</tr>
						<?php
					}
				?>
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" class="text-right">Débitos</th>
					<th class="text-right">${{ number_format($saldoAnteriorDebitos, 2) }}</th>
					<th class="text-right">${{ number_format($debitos, 2) }}</th>
					<th class="text-right">${{ number_format($creditos, 2) }}</th>
					<th class="text-right">${{ number_format($nuevoSaldoDebitos, 2) }}</th>
				</tr>
				<tr>
					<th colspan="2" class="text-right">Créditos</th>
					<th class="text-right">${{ number_format($saldoAnteriorCreditos, 2) }}</th>
					<th colspan="2" class="text-right"></th>
					<th class="text-right">${{ number_format($nuevoSaldoCreditos, 2) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

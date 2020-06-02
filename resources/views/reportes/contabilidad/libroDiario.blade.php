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
			Libro Diario por Cuenta y Tipo de Documento
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
					<th>Tipo de documento</th>
					<th></th>
					<th class="text-center">Débitos</th>
					<th class="text-center">Créditos</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$totalDebitos = 0;
					$totalCreditos = 0;
				?>
				@foreach ($detalles as $conceptos)
					<tr>
						<th colspan="4">{{ $conceptos[0]->cuenta_codigo }} {{ $conceptos[0]->cuenta_nombre }}</th>
					</tr>
					<?php
						$debitos = 0;
						$creditos = 0;
						foreach($conceptos as $concepto) {
							$debitos += $concepto->debitos;
							$creditos += $concepto->creditos;
							?>
							<tr>
								<td class="text-right">{{ $concepto->tipo_comprobante_codigo }}</td>
								<td>{{ $concepto->tipo_comprobante_nombre }}</td>
								<td class="text-right">${{ number_format($concepto->debitos, 2) }}</td>
								<td class="text-right">${{ number_format($concepto->creditos, 2) }}</td>
							</tr>
							<?php
						}
						$totalDebitos += $debitos;
						$totalCreditos += $creditos;
					?>
					<tr>
						<th colspan="2" class="text-right">
							Total {{ $conceptos[0]->cuenta_codigo }} {{ $conceptos[0]->cuenta_nombre }}
						</th>
						<th class="text-right">${{ number_format($debitos, 2) }}</th>
						<th class="text-right">${{ number_format($creditos, 2) }}</th>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th colspan="2" class="text-right">Total General</th>
					<th class="text-right">${{ number_format($totalDebitos, 2) }}</th>
					<th class="text-right">${{ number_format($totalCreditos, 2) }}</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

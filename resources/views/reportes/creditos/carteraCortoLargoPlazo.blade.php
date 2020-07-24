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
			Cartera corto y largo plazo
		</h4>
		<h5>Periodo: {{ $mesTexto }} del {{ $anio }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th colspan="2"></th>
					<th colspan="2" class="text-center">Corto plazo<br>< 12 meses</th>
					<th colspan="2" class="text-center">Largo plazo<br>> 12 meses</th>
				</tr>
				<tr>
					<th>Modalidad</th>
					<th class="text-center">Saldo total</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Porcentaje</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Porcentaje</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($saldos as $saldo)
					<tr>
						<td>{{ $saldo->modalidad }}</td>
						<td class="text-right">${{ number_format($saldo->saldo, 0) }}</td>
						<td class="text-right">${{ number_format($saldo->corto_plazo, 0) }}</td>
						<td class="text-right">{{ number_format($saldo->porcentaje_corto_plazo, 2) }}%</td>
						<td class="text-right">${{ number_format($saldo->largo_plazo, 0) }}</td>
						<td class="text-right">{{ number_format($saldo->porcentaje_largo_plazo, 2) }}%</td>
					</tr>
				@endforeach
			</tbody>
			<tfoot>
				<tr>
					<th>Totales</th>
					<th class="text-right">${{ number_format($totales->saldo, 0) }}</th>
					<th class="text-right">${{ number_format($totales->corto_plazo, 0) }}</th>
					<th class="text-right">{{ number_format($totales->porcentaje_corto_plazo, 2) }}%</th>
					<th class="text-right">${{ number_format($totales->largo_plazo, 0) }}</th>
					<th class="text-right">{{ number_format($totales->porcentaje_largo_plazo, 2) }}%</th>
				</tr>
			</tfoot>
		</table>
	</div>
</div>

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
			Codeudores
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
					<th class="text-center">Id codeudor</th>
					<th>Codeudor</th>
					<th>Obligación</th>
					<th class="text-center">Valor inicial</th>
					<th>Fecha</th>
					<th>Tasa</th>
					<th>Calificación</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Id deudor</th>
					<th>Deudor</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($codeudores as $codeudor)
					<tr>
						<td class="text-right">{{ $codeudor->idcodeudor }}</td>
						<td>{{ $codeudor->codeudor }}</td>
						<td>{{ $codeudor->obligacion }}</td>
						<td class="text-right">${{ number_format($codeudor->valorinicial) }}</td>
						<td>{{ $codeudor->fechadesembolso }}</td>
						<td class="text-right">{{ number_format($codeudor->tasa, 2) }}%</td>
						<td class="text-center">{{ $codeudor->calificacion }}</td>
						<td class="text-right">${{ number_format($codeudor->saldo) }}</td>
						<td class="text-right">{{ $codeudor->iddeudor }}</td>
						<td>{{ $codeudor->deudor }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
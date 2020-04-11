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
			Informe cierre de cartera por periodo
		</h4>
		<h5>Fecha: {{ $fechaCorte }}</h5>
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
					<th>Estado</th>
					<th>Empresa</th>
					<th>Obligación</th>
					<th>Desembolso</th>
					<th class="text-center">Valor crédito</th>
					<th>Tasa</th>
					<th>Plazo</th>
					<th class="text-center">Valor cuota</th>
					<th>Altura</th>
					<th>Pendientes</th>
					<th>Cod. Modalidad</th>
					<th>Modalidad</th>
					<th class="text-center">Saldo</th>
					<th class="text-center">Saldo intereses</th>
					<th class="text-center">Interes causado</th>
					<th class="text-center">Saldo seguro</th>
					<th>Días vencidos</th>
					<th class="text-center">Capital vencido</th>
					<th>Cartera</th>
					<th>Tipo garantía</th>
					<th>Tipo pago</th>
					<th>Frecuencia</th>
					<th>Inicio pago</th>
					<th>Fecha final</th>
					<th>Último pago</th>
					<th>Calif. Anterior</th>
					<th>Calif. Actual</th>
					<th>Calif. Final</th>
					<th>Ahorros deterioro</th>
					<th>Base deterioro</th>
					<th>Deterioro capital</th>
					<th>Deterioro intereses</th>
					<th>Fecha cancelación</th>
					<th>Estado crédito</th>
					<th>Cuenta capital</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($cierreCartera as $cierre)
					<tr>
						<td class="text-right">{{ $cierre->Identificacion }}</td>
						<td>{{ $cierre->Nombre }}</td>
						<td>{{ $cierre->Estado }}</td>
						<td>{{ $cierre->Empresa }}</td>
						<td>{{ $cierre->Obligacion }}</td>
						<td>{{ $cierre->Desembolso }}</td>
						<td class="text-right">${{ number_format($cierre->Monto) }}</td>
						<td class="text-right">{{ number_format($cierre->Tasa, 2) }}%</td>
						<td class="text-right">{{ $cierre->Plazo }}</td>
						<td class="text-right">${{ number_format($cierre->Cuota) }}</td>
						<td class="text-right">{{ $cierre->Altura }}</td>
						<td class="text-right">{{ $cierre->Pendientes }}</td>
						<td class="text-right">{{ $cierre->ModalidadCodigo }}</td>
						<td>{{ $cierre->Modalidad }}</td>
						<td class="text-right">${{ number_format($cierre->Saldo) }}</td>
						<td class="text-right">${{ number_format($cierre->SaldoInteres) }}</td>
						<td class="text-right">${{ number_format($cierre->InteresCausado) }}</td>
						<td class="text-right">${{ number_format($cierre->SaldoSeguro) }}</td>
						<td class="text-right">{{ $cierre->DiasVencidos }}</td>
						<td class="text-right">${{ number_format($cierre->CapitalVencido) }}</td>
						<td>{{ $cierre->Cartera }}</td>
						<td>{{ $cierre->TipoGarantia }}</td>
						<td>{{ $cierre->TipoPago }}</td>
						<td>{{ $cierre->Frecuencia }}</td>
						<td>{{ $cierre->InicioPago }}</td>
						<td>{{ $cierre->FechaFinal }}</td>
						<td>{{ $cierre->UltimoPago }}</td>
						<td>{{ $cierre->CalificacionAnterior }}</td>
						<td>{{ $cierre->CalificacionActual }}</td>
						<td>{{ $cierre->CalificacionFinal }}</td>
						<td class="text-right">${{ number_format($cierre->AhorrosDeterioro) }}</td>
						<td class="text-right">${{ number_format($cierre->BaseDeterioro) }}</td>
						<td class="text-right">${{ number_format($cierre->DeterioroCapital) }}</td>
						<td class="text-right">${{ number_format($cierre->DeterioroIntereses) }}</td>
						<td>{{ $cierre->FechaCancelación }}</td>
						<td>{{ $cierre->EstadoCredito }}</td>
						<td class="text-right">{{ $cierre->CuentaCapital }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
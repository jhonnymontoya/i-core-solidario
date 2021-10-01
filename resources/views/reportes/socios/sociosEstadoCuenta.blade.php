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
			Estado de cuenta
		</h4>
		<h5>Fecha consulta {{ $fechaConsulta }}</h5>
	</div>
</div>
<br><br>
<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-body">
			<div class="col-md-12">
				<div class="row">
					<div class="col-md-2 col-2"><strong>Nombre:</strong></div>
					<div class="col-md-5 col-10">{{ $ter->tipoIdentificacion->codigo }} {{ $ter->nombre_completo }}</div>
				</div>

				<div class="row">
					<div class="col-md-2 col-2"><strong>Pagaduría:</strong></div>
					<div class="col-md-5 col-5">{{ $socio != null ?optional($socio->pagaduria)->nombre : '' }}</div>

					<div class="col-md-2 col-2"><strong>Afiliación:</strong></div>
					@if ($socio)
						<div class="col-md-3 col-3">{{ $socio->fecha_afiliacion }} ({{ $socio->fecha_afiliacion->diffForHumans() }})</div>
					@else
						<div class="col-md-3 col-3"></div>
					@endif
				</div>
				<?php
					$endeudamiento = 0;
					if($socio) {
						$endeudamiento = $socio->endeudamiento();
					}
				?>
				<div class="row">
					<div class="col-md-2 col-2"><strong>Estado:</strong></div>
					<div class="col-md-5 col-5">{{ optional($socio)->estado }}</div>

					<div class="col-md-2 col-2"><strong>Ingreso empresa:</strong></div>
					@if ($socio)
						<div class="col-md-3 col-3">{{ $socio->fecha_ingreso }} ({{ $socio->fecha_ingreso->diffForHumans() }})</div>
					@else
						<div class="col-md-3 col-3"></div>
					@endif
				</div>

				<div class="row">
					<div class="col-md-2 col-2"><strong>Sueldo:</strong></div>
					<div class="col-md-5 col-5">${{ number_format(optional($socio)->sueldo_mes) }}</div>

					<div class="col-md-2 col-2"><strong>Nacimiento:</strong></div>
					@if ($ter)
						<div class="col-md-3 col-3">{{ $ter->fecha_nacimiento }} ({{ $ter->fecha_nacimiento->diffForHumans() }})</div>
					@else
						<div class="col-md-3 col-3"></div>
					@endif
				</div>

				<div class="row">
					<div class="col-md-2 col-2"><strong>Email:</strong></div>
					<div class="col-md-5 col-5">{{ empty($contacto) ? '' : $contacto->email }}</div>

					<div class="col-md-2 col-2"><strong>Teléfono:</strong></div>
					<div class="col-md-3 col-3">{{ empty($contacto) ? '' : ($contacto->movil ?: $contacto->telefono) }}</div>
				</div>

				<div class="row">
					<div class="col-md-2 col-2"><strong>Cupo:</strong></div>
					<div class="col-md-5 col-5">${{ number_format($cupo) }}</div>

					<div class="col-md-2 col-2"><strong>Endeudamiento:</strong></div>
					<div class="col-md-3 col-3">{{ $endeudamiento }}%</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>AHORROS</strong></div>
		<div class="card-body">
			<div class="col-md-12 table-responsive">
				@if($ahorros->count())
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Modalidad</th>
								<th>Cuota mes</th>
								<th>Saldo</th>
								<th>Tasa E.A.</th>
							</tr>
						</thead>
						<tbody>
							@php
								$totalAhorros = 0;
								$totalCuota = 0;
							@endphp
							@foreach ($ahorros as $ahorro)
								@php
									$totalAhorros += $ahorro->saldo;
									$totalCuota += $ahorro->cuota;
								@endphp
								<tr>
									<td>{{ $ahorro->nombre_completo }}</td>
									<td class="text-right">${{ number_format(\App\Helpers\ConversionHelper::conversionValorPeriodicidad($ahorro->cuota, $ahorro->periodicidad, 'MENSUAL'), 0) }}</td>
									<td class="text-right">${{ number_format($ahorro->saldo, 0) }}</td>
									<td class="text-right">{{ number_format($ahorro->tasa, 2) }}%</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th class="text-right">Total ahorros</th>
								<th class="text-right">${{ number_format($totalCuota) }}</th>
								<th class="text-right">${{ number_format($totalAhorros) }}</th>
							</tr>
						</tfoot>
					</table>
				@else
					<h4>No hay registros de ahorros para mostrar</h4>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="card card-default">
		<div class="card-header with-border"><strong>CRÉDITOS</strong></div>
		<div class="card-body">
			<div class="col-md-12 table-responsive">
				@if($creditos->count())
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Obligación</th>
								<th>Modalidad</th>
								<th>Desembolso</th>
								<th>Valor inicial</th>
								<th>Tasa</th>
								<th>Cuota</th>
								<th>Saldo</th>
							</tr>
						</thead>
						<tbody>
							@php
								$totalCreditos = 0;
								$totalCuota = 0;
							@endphp
							@foreach ($creditos as $credito)
								@php
									$saldoObligacion = $credito->saldoObligacion($fechaConsulta);
									$totalCreditos += $saldoObligacion;
									$totalCuota += $credito->valor_cuota;
								@endphp
								<tr>
									<td>{{ $credito->numero_obligacion }}</td>
									<td>{{ $credito->modalidadCredito->nombre }}</td>
									<td>{{ $credito->fecha_desembolso }}</td>
									<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
									<td class="text-right">{{ number_format($credito->tasa, 2) }}%</td>
									<td class="text-right">${{ number_format($credito->valor_cuota, 0) }}</td>
									<td class="text-right">${{ number_format($saldoObligacion, 0) }}</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th colspan="5" class="text-right">Total créditos</th>
								<th class="text-right">${{ number_format($totalCuota) }}</th>
								<th class="text-right">${{ number_format($totalCreditos) }}</th>
							</tr>
						</tfoot>
					</table>
				@else
					<h4>No hay registros de créditos para mostrar</h4>
				@endif
			</div>
		</div>
	</div>
</div>

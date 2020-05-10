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
			Estudio de crédito
		</h4>
	</div>
</div>
<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>SOLICITUD</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 col-3"><strong>Nombre:</strong></div>
						<div class="col-md-9 col-9">{{ $ter->tipoIdentificacion->codigo }} {{ $ter->nombre_completo }}</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Valor solicitud:</strong></div>
						<div class="col-md-2 col-2">${{ number_format($solicitud->valor_credito) }}</div>

						<div class="col-md-3 col-3"><strong>Modalidad:</strong></div>
						<div class="col-md-4 col-4">{{ $solicitud->modalidadCredito->nombre }}</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Fecha solicitud:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->fecha_solicitud }}</div>

						<div class="col-md-3 col-3"><strong>Tasa M.V.:</strong></div>
						<div class="col-md-4 col-4">{{ number_format($solicitud->tasa, 2) }}%</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Radicado:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->id }}</div>

						<div class="col-md-3 col-3"><strong>Fecha aprobación:</strong></div>
						<div class="col-md-4 col-4">{{ $solicitud->fecha_aprobacion }}</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Estado:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->estado_solicitud }}</div>

						<div class="col-md-3 col-3"><strong>Fecha desembolso:</strong></div>
						<div class="col-md-4 col-4">{{ $solicitud->fecha_desembolso }}</div>
					</div>

				</div>
			</div>
		</div>
	</div>
</div>

{{-- RESÚMEN CONDICIONES --}}
@if ($solicitud->cumplimientoCondiciones->count())
<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>RESUMEN CONDICIONES</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12 table_responsive">
					<table class="table table-striped">
						<thead>
							<tr>
								<th>Condición</th>
								<th>Valor parámetro</th>
								<th>Valor solicitud</th>
								<th>Cumple</th>
							</tr>
						</thead>
						<tbody>
							@foreach($solicitud->cumplimientoCondiciones as $condicion)
								@if($condicion->condicion == 'Cupo')
									<tr>
										<td>{{ $condicion->condicion }}</td>
										<td>{{ number_format($condicion->valor_parametro, 0) }}</td>
										<td>{{ number_format($condicion->valor_solicitud, 0) }}</td>
										<td>
											<?php
												$cumple = $condicion->cumple_parametro;
												$aprobado = false;
												if(!$cumple)
												{
													$cumple = empty($condicion->es_aprobada) ? false : true;
													$aprobado = $cumple;
												}
												if($cumple) {
													if($aprobado) {
														echo "aprobado";
													}
													else {
														echo "Sí";
													}
												}
												else {
													echo "No";
												}
											?>
										</td>
									</tr>
								@else
									<tr>
										<td>{{ $condicion->condicion }}</td>
										<td>
											@if($condicion->condicion == 'Plazo')
												{{ number_format($condicion->valor_parametro, 0) }}M
											@elseif($condicion->condicion == 'Endeudamiento')
												{{ number_format($condicion->valor_parametro, 0) }}%
											@else
												{{ number_format($condicion->valor_parametro, 0) }}
											@endif
										</td>
										<td>
											@if($condicion->condicion == 'Plazo')
												{{ number_format($condicion->valor_solicitud, 0) }}M
											@elseif($condicion->condicion == 'Endeudamiento')
												{{ number_format($condicion->valor_solicitud, 0) }}%
											@else
												{{ number_format($condicion->valor_solicitud, 0) }}
											@endif
										</td>
										<td>
											<?php
												$cumple = $condicion->cumple_parametro;
												$aprobado = false;
												if(!$cumple)
												{
													$cumple = empty($condicion->es_aprobada) ? false : true;
													$aprobado = $cumple;
												}
												if($cumple) {
													if($aprobado) {
														echo "aprobado";
													}
													else {
														echo "Sí";
													}
												}
												else {
													echo "No";
												}
											?>
										</td>
									</tr>
								@endif
							@endforeach
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
{{-- FIN DE RESÚMEN CONDICIONES --}}


<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>AMORTIZACIÓN</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3 col-3"><strong>No. cuotas:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->plazo }}</div>

						<div class="col-md-3 col-3"><strong>Forma pago:</strong></div>
						<div class="col-md-4 col-4">{{ $solicitud->forma_pago }}</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Periodicidad:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->periodicidad }}</div>

						<div class="col-md-3 col-3"><strong>Valor cuota:</strong></div>
						<div class="col-md-4 col-4">${{ number_format($solicitud->valor_cuota, 0) }}</div>
					</div>

					<div class="row">
						<div class="col-md-3 col-3"><strong>Primer pago:</strong></div>
						<div class="col-md-2 col-2">{{ $solicitud->fecha_primer_pago }}</div>

						<div class="col-md-3 col-3"><strong>Tipo:</strong></div>
						<div class="col-md-4 col-4">{{ $solicitud->tipo_amortizacion }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

{{-- CRÉDITOS RECOGIDOS --}}
@if($creditosRecogidos->count())
<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>CRÉDITOS RECOGIDOS</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12 table_responsive">
					<table class="table table-striped table-hover">
						<thead>
							<tr>
								<th>Número obligación</th>
								<th class="text-center">Capital recogido</th>
								<th class="text-center">Interes por pagar</th>
								<th class="text-center">Total consolidación obligación</th>
							</tr>
						</thead>
						<tbody>
							@php
								$total = 0;
							@endphp
							@foreach ($creditosRecogidos as $creditoRecogido)
								@php
									$total += $creditoRecogido->total;
								@endphp
								<tr>
									<td>{{ $creditoRecogido->creditoConsolidado->numero_obligacion }}</td>
									<td class="text-right">${{ number_format($creditoRecogido->pago_capital) }}</td>
									<td class="text-right">${{ number_format($creditoRecogido->pago_intereses) }}</td>
									<td class="text-right">${{ number_format($creditoRecogido->total) }}</td>
								</tr>
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<th colspan="2"></th>
								<td>Total recogido:</td>
								<td class="text-right">${{ number_format($total) }}</td>
							</tr>
							<tr>
								<th colspan="2"></th>
								<th>Exedente para desembolso:</th>
								<th class="text-right">${{ number_format($solicitud->valor_credito - $total) }}</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
{{-- FIN DE CRÉDITOS RECOGIDOS --}}

<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>GARANTÍAS - CODEUDORES</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					<ul>
						@foreach ($codeudores['garantias'] as $key => $garantia)
							<li>
								<i class="fa {{ $garantia['cumplido'] ? 'fa-check' : 'fa-times' }}"></i>
								{{ $garantia['cantidad'] }} de {{ $garantia['total'] }} {{ $garantia['nombre'] }}
							</li>
						@endforeach
					</ul>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					@foreach ($codeudores['codeudores'] as $codeudor)
						<h5>
							<label>{{ $codeudor['numeroIdentificacion'] }} - {{ $codeudor['nombre'] }} - {{ $codeudor['nombreGarantia'] }}</label>
						</h5>
						<ul>
							<li><strong>Tipo condición:</strong> {{ $codeudor['tipoCondicion'] }}</li>
							<li><strong>Admite codeudor externo:</strong> {{ ($codeudor['admiteCodeudorExterno'] ? 'Sí' : 'No') }} <i class="fa fa-{{ ($codeudor['valorAdmiteCodeudorExterno'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Valida cupo codeudor:</strong> ${{ number_format($codeudor['cupoCodeudor']) }} <i class="fa fa-{{ ($codeudor['valorValidaCupoCodeudor'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Límite número codeudas:</strong> {{ $codeudor['limiteObligacionesCodeudor'] }} <i class="fa fa-{{ ($codeudor['valorTieneLimiteObligacionesCodeudor'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Límite saldo codeudas:</strong> ${{ number_format($codeudor['valorSaldoCodeudas']) }} <i class="fa fa-{{ ($codeudor['valorTieneLimiteSaldoCodeudas'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Requiere antigüedad (días):</strong> {{ number_format($codeudor['valorAntiguedadCodeudor']) }} <i class="fa fa-{{ ($codeudor['valorValidaAntiguedadCodeudor'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Calificación:</strong> {{ $codeudor['valorCalificacionCodeudor'] }} <i class="fa fa-{{ ($codeudor['valorValidaCalificacionCodeudor'] ? 'check' : 'times') }}"></i></li>
							<li><strong>Resultado:</strong> {{ $codeudor['resultado'] }}</li>
						</ul>
					@endforeach
				</div>
			</div>
		</div>
	</div>
</div>

{{--
<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>GARANTÍAS - REAL</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					Sin datos
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>GARANTÍAS - FONDO DE GARANTÍAS</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12">
					Sin datos
				</div>
			</div>
		</div>
	</div>
</div>
--}}

<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>CUPO</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-12 table_responsive">
					@if($modalidades->count() || $creditos->count())
						<table class="table table-striped table-hover">
							<thead>
								<tr>
									<th>Concepto</th>
									<th class="text-center">Saldo</th>
									<th class="text-center">Veces apalancamiento</th>
									<th class="text-center">Cupo</th>
								</tr>
							</thead>
							<tbody>
								@php
									$total = 0;
									$subTotal = 0;
								@endphp
								@foreach ($modalidades as $modalidad)
									@php
										$respuesta = DB::select('exec ahorros.sp_saldo_modalidad_ahorro ?, ?, ?', [$socio->id, $modalidad->id, \Carbon\Carbon::createFromFormat('d/m/Y', '31/12/2100')]);
										$saldo = $respuesta[0]->saldo;
										$total += $saldo * $modalidad->apalancamiento_cupo;
										$subTotal += $saldo;
										if($saldo == 0)continue;
									@endphp
									<tr>
										<td>{{ $modalidad->nombre }}</td>
										<td class="text-right">${{ number_format($saldo, 0) }}</td>
										<td class="text-right">{{ number_format($modalidad->apalancamiento_cupo, 2) }}</td>
										<td class="text-right">${{ number_format($saldo * $modalidad->apalancamiento_cupo, 0) }}</td>
									</tr>
								@endforeach
								@foreach ($sdats as $sdat)
									@php
										$saldo = $sdat->saldo;
										$total += $saldo * $sdat->apalancamiento_cupo;
										$subTotal += $saldo;
										if($saldo == 0)continue;
									@endphp
									<tr>
										<td>{{ $sdat->nombre }}</td>
										<td class="text-right">${{ number_format($saldo, 0) }}</td>
										<td class="text-right">{{ number_format($sdat->apalancamiento_cupo, 2) }}</td>
										<td class="text-right">${{ number_format($saldo * $sdat->apalancamiento_cupo, 0) }}</td>
									</tr>
								@endforeach
								@if ($subTotal > 0)
									<tr>
										<th class="text-left">Total ahorros</th>
										<th class="text-right">${{ number_format($subTotal, 0) }}</th>
									</tr>
								@endif
								@php
									$subTotal = 0;
								@endphp
								@foreach($creditos as $credito)
									@php
										$saldo = $credito->saldoObligacion('31/12/2100');
										$total -= $saldo;
										$subTotal += $saldo;
										if($saldo == 0)continue;
									@endphp
									<tr>
										<td>{{ Str::limit($credito->numero_obligacion . ' - ' . $credito->modalidadCredito->nombre, 40) }}</td>
										<td class="text-right">${{ number_format($saldo, 0) }}</td>
										<td class="text-right">-1.00</td>
										<td class="text-right">${{ number_format(-$saldo, 0) }}</td>
									</tr>
								@endforeach
								<tfoot>
									<tr>
										@if ($subTotal > 0)
											<th class="text-left">Total créditos</th>
											<th class="text-right">${{ number_format($subTotal, 0) }}</th>
											<th class="text-right">Cupo total disponible:</th>
										@else
											<th colspan="3" class="text-right">Cupo total disponible:</th>
										@endif
										<th class="text-right">${{ number_format($total, 0) }}</th>
									</tr>
								</tfoot>
							</tbody>
						</table>
					@else
						Sin datos
					@endif
				</div>
			</div>
		</div>
	</div>
</div>

<div class="container-fluid">
	<div class="card card-default card-outline">
		<div class="card-header with-border"><strong>ENDEUDAMIENTO MENSUAL</strong></div>
		<div class="card-body">
			<div class="row">
				<div class="col-md-8 table_responsive">
					@if ($endeudamientos->count())
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Concepto</th>
									<th class="text-center">Ingresos</th>
									<th class="text-center">Egresos</th>
								</tr>
							</thead>
							<tbody>
								@php
									$ingresos = 0;
									$egresos = 0;
								@endphp
								@foreach ($endeudamientos as $endeudamiento)
									@php
										$ingresos += $endeudamiento['ingresos'];
										$egresos += $endeudamiento['deducciones'];
									@endphp
									<tr>
										<td>{{ $endeudamiento['concepto'] }}</td>
										<td class="text-right">${{ number_format($endeudamiento['ingresos'], 0) }}</td>
										<td class="text-right">${{ number_format($endeudamiento['deducciones'], 0) }}</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th>Totales</th>
									<th class="text-right">${{ number_format($ingresos, 0) }}</th>
									<th class="text-right">${{ number_format($egresos, 0) }}</th>
								</tr>
							</tfoot>
						</table>
					@else
						Sin datos
					@endif
				</div>
				<div class="col-md-4 text-center">
						@php
							$porcentaje = ceil(($egresos * 100) / $ingresos);
							$color = $porcentaje > $porcentajeMaximoEndeudamiento ? '#f56954' : '#00a65a';
						@endphp
						<h3>Porcentaje de endeudamiento</h3><br>
						<input type="text" class="knob" value="{{ $porcentaje }}" data-width="125" data-height="125" data-fgColor="{{ $color }}" data-readonly="true">
				</div>
			</div>
		</div>
	</div>
</div>

{{-- DOCUMENTACIÓN --}}
@if ($solicitud->documentos->count())
<div class="row">
	<div class="col-md-12">
		<div class="card card-default card-outline">
			<div class="card-header with-border"><strong>DOCUMENTACIÓN</strong></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 table_responsive">
						<table class="table table-striped">
							<thead>
								<tr>
									<th>Documento</th>
									<th>Obligatorio</th>
									<th>Cumple</th>
								</tr>
							</thead>
							<tbody>
								@foreach($solicitud->documentos as $documento)
									<tr>
										<td>{{ $documento->documento }}</td>
										<td>{{ $documento->obligatorio ? 'Si' : 'Opcional' }}</td>
										<td>
											<?php
												$cumple = $documento->pivot->cumple;
												echo $cumple ? 'Sí' : 'No';
											?>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif
{{-- FIN DE DOCUMENTACIÓN --}}

{{-- OBSERVACIONES --}}
@if ($solicitud->observaciones)
	<div class="container-fluid">
		<div class="card card-default card-outline">
			<div class="card-header with-border"><strong>OBSERVACIONES</strong></div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12 table_responsive">
						<p>{{ $solicitud->observaciones }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
@endif
{{-- FIN DE OBSERVACIONES --}}

{{-- FIRMAS --}}
<div class="row">
	<div class="col-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Radicado</strong>
			</div>
			<div style="height: 50px;"></div>
			<div class="text-center" style="border-top:1px solid #000;">
				<strong>Usuario:</strong> {{ $solicitud->quien_radico ?? 'I-Core' }}
			</div>
		</div>
	</div>

	<div class="col-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Aprobado</strong>
			</div>
			<div style="height: 50px;"></div>
			<div class="text-center" style="border-top:1px solid #000;">
				<strong>Usuario:</strong> {{ $solicitud->quien_aprobo }}
			</div>
		</div>
	</div>

	<div class="col-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Desembolsado</strong>
			</div>
			<div style="height: 50px;"></div>
			<div class="text-center" style="border-top:1px solid #000;">
				<strong>Usuario:</strong> {{ $solicitud->quien_desembolso }}
			</div>
		</div>
	</div>
</div>
{{-- FIN DE FIRMAS --}}
<br>

@extends('layouts.consulta')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Consulta movimientos</h3>
				</div>
				<div class="card-body">
					<br>
					<div class="row">
						<div class="col-md-9 col-md-offset-1">
							<div class="row">
								<div class="col-md-5">
									<strong>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</strong>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12"><strong>Detalles de:</strong> {{ $credito->numero_obligacion }} {{ $credito->modalidadCredito->nombre }}</div>
							</div>
						</div>
						<div class="col-md-2">
							<a href="{{ url('consulta') }}" class="btn btn-outline-primary pull-right">Volver</a>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Valor inicial:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->valor_credito, 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Fecha solicitud:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->fecha_desembolso }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Tasa M.V.:</strong></div>
								<div class="col-md-6 text-left">{{ number_format($credito->tasa, 3) }}%</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Valor cuota:</strong></div>
								<?php
									$valorCuota = 0;
									$valorCuota = $credito->valor_cuota;
								?>
								<div class="col-md-6 text-left">${{ number_format($valorCuota, 0) }}</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Saldo capital:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->saldoObligacion(Request::get('fecha')), 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Saldo intereses:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->saldoInteresObligacion(Request::get('fecha')), 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Capital vencido:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->capitalVencido(Request::get('fecha')), 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Días vencidos:</strong></div>
								<div class="col-md-6 text-left">{{ number_format($credito->diasVencidos(Request::get('fecha')), 0) }}</div>
							</div>
						</div>
					</div>
					<br>
					<a id="verAmortizacion" class="btn btn-outline-info btn-sm">Plan de pagos</a>
					@if($credito->amortizaciones->count())
					<div id="amortizacion" style="display: none;" data-visible="false">
						<br>
						<div class="row">
							<div class="col-md-12">
								<h3>Amortización</h3>
							</div>
						</div>
						<div class="row" style="margin-left: 30px; margin-right: 30px;">
							<div class="col-md-3">
								<div class="row">
									<div class="col-md-8"><label>Tasa seguro cartera</label></div>
									<div class="col-md-4">{{ empty($credito->seguroCartera) ? 0 : number_format($credito->seguroCartera->tasa_mes, 4) }}%</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="row">
									<div class="col-md-8"><label>Porcentaje capital en extraordinarias</label></div>
									<div class="col-md-4">{{ number_format($credito->porcentajeCapitalEnExtraordinarias(), 2) }}%</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-6"><label>Tasa E.A.</label></div>
									<div class="col-md-6">
										<?php
											$tasaEA = ($credito->tasa / 100) + 1;
											$tasaEA = pow($tasaEA, 12) - 1;
											$tasaEA = number_format($tasaEA * 100, 2);
										?>
										{{ $tasaEA }}%
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="row" style="margin-left:20px; margin-right:20px;">
							<div class="col-md-12 table-responsive">
								<table id="tablaAmortizacion" class="table table-hover">
									<thead>
										<tr>
											<th>Cuota</th>
											<th>Naturaleza cuota</th>
											<th>Forma pago</th>
											<th>Fecha pago</th>
											<th class="text-center">Capital</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Seguro cartera</th>
											<th class="text-center">Total cuota</th>
											<th class="text-center">Nuevo saldo</th>
										</tr>
									</thead>
									<tbody>
										@foreach($credito->amortizaciones()->orderBy('numero_cuota', 'asc')->get() as $amortizacion)
											<tr>
												<td>{{ $amortizacion->numero_cuota }}</td>
												<td>{{ $amortizacion->naturaleza_cuota }}</td>
												<td>{{ $amortizacion->forma_pago }}</td>
												<td>{{ $amortizacion->fecha_cuota }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_capital, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_intereses, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_seguro_cartera, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->total_cuota, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->nuevo_saldo_capital, 0) }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endif
					<hr>
					<div class="row">
						<div class="col-md-10 col-md-offset-1 table-responsive">
							@if($movimientos->count())
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Fecha</th>
											<th>Concepto</th>
											<th>Detalle</th>
											<th class="text-center">Capital</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Total</th>
										</tr>
									</thead>
									<tbody>
										@foreach($movimientos as $movimiento)
											<tr>
												<td>{{ $movimiento->fecha_movimiento }}</td>
												<td>{{ $movimiento->concepto }}</td>
												<td>{{ $movimiento->detalle }}</td>
												<?php
													$valor = $movimiento->capital;
													$colorTexto = $valor < 0 ? 'text-danger' : '';
												?>
												<td class="text-right {{ $colorTexto }}">${{ number_format($valor, 0) }}</td>
												<?php
													$valor = $movimiento->intereses;
													$colorTexto = $valor < 0 ? 'text-danger' : '';
												?>
												<td class="text-right {{ $colorTexto }}">${{ number_format($valor, 0) }}</td>
												<?php
													$valor = $movimiento->total;
													$colorTexto = $valor < 0 ? 'text-danger' : '';
												?>
												<td class="text-right {{ $colorTexto }}">${{ number_format($valor, 0) }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							@else
								<strong>No existen registros para mostrar</strong>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<small>Se muestran los movimientos dentro de los últimos treinta y seis meses</small>
						</div>
					</div>
				</div>
				<div class="card-footer">
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#verAmortizacion").click(function(e){
			if($("#amortizacion").data("visible")){
				$(this).text('Plan de pagos');
				$("#amortizacion").data("visible", false);
				$("#amortizacion").hide();
			}
			else
			{
				$(this).text('Ocultar plan de pagos');
				$("#amortizacion").data("visible", true);
				$("#amortizacion").show();
			}
		});
	});
</script>
@endpush
@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Reliquidar créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Reliquidar créditos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

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
					<h3 class="card-title">Reliquidar créditos</h3>
				</div>
				{!! Form::model($credito, ['url' => ['reliquidarCredito', $credito], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
				{!! Form::hidden('fechaReliquidacion', $fecha) !!}
				<div class="card-body">
					<br>
					@if ($errors->has('fechaReliquidacion'))
						<div class="alert alert-danger alert-dismissible">
							<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
							<h4>Error!</h4>
							<p>{{ $errors->first('fechaReliquidacion') }}</p>
						</div>
					@endif
					<div class="row">
						<div class="col-md-9 col-md-offset-1">
							<div class="row">
								<div class="col-md-5">
									<strong>{{ $tercero->tipoIdentificacion->codigo }} {{ $tercero->nombre_completo }}</strong>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12"><strong>Detalles obligación:</strong> {{ $credito->numero_obligacion }}</div>
							</div>
							<div class="row">
								<div class="col-md-12"><strong>Fecha reliquidación:</strong> {{ $fecha }}</div>
							</div>
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
								<div class="col-md-6 text-right"><strong>Fecha desembolso:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->fecha_desembolso }}</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Tasa M.V.:</strong></div>
								<div class="col-md-6 text-left">{{ number_format($credito->tasa, 2) }}%</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Modalidad:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->modalidadCredito->nombre }}</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Valor cuota:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->valor_cuota, 0) }}</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Plazo:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->plazo }}</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Periodicidad:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->periodicidad }}</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Forma pago:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->forma_pago }}</div>
							</div>
						</div>
					</div>

					@php
						$altura = $credito->alturaObligacion($fecha);
					@endphp
					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Altura:</strong></div>
								<div class="col-md-6 text-left">{{ $altura }} / {{ $credito->plazo }}</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Inicio pagos:</strong></div>
								@php
									$fechaAmortizacionPrimerPago = '';
									$fechaAmortizacionUltimoPago = '';
									$amortizaciones = $credito->amortizaciones;
									if($amortizaciones->count()) {
										$fechaAmortizacionPrimerPago = $amortizaciones[0]->fecha_cuota;
										$fechaAmortizacionUltimoPago = $amortizaciones[$amortizaciones->count() - 1]->fecha_cuota;
									}
								@endphp
								<div class="col-md-6 text-left">{{ $fechaAmortizacionPrimerPago }}</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Fin pagos:</strong></div>
								<div class="col-md-6 text-left">{{ $fechaAmortizacionUltimoPago }}</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Amortización:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->tipo_amortizacion == 'FIJA' ? 'Fija compuesta' : 'Fija capital' }}</div>
							</div>
						</div>
					</div>
					@php
						$saldoCapital = $credito->saldoObligacion($fecha);
					@endphp
					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Saldo capital:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($saldoCapital, 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Saldo intereses:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->saldoInteresObligacion($fecha), 0) }}</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Seguro:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->saldoSeguroObligacion($fecha), 0) }}</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Último movimiento:</strong></div>
								@php
									$ultimoMovimiento = $credito->movimientosCapitalCredito()->orderBy('fecha_movimiento', 'desc')->first();
								@endphp
								<div class="col-md-6 text-left">{{ optional($ultimoMovimiento)->fecha_movimiento }}</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Capital vencido:</strong></div>
								<div class="col-md-6 text-left">${{ number_format($credito->capitalVencido($fecha), 0) }}</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Días vencidos:</strong></div>
								<div class="col-md-6 text-left">{{ number_format($credito->diasVencidos($fecha), 0) }}</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="row">
								<div class="col-md-6 text-right"><strong>Calificación:</strong></div>
								<div class="col-md-6 text-left">{{ $credito->calificacion_obligacion }}</div>
							</div>
						</div>
					</div>
					<br>

					@php
						$formaReliquidar = empty(old("freliquidar")) ? 1 : old("freliquidar");
						if($formaReliquidar < 1 || $formaReliquidar > 2)$formaReliquidar = 1;
						$formaReliquidar = $formaReliquidar == 1 ? true : false;
					@endphp
					<div class="row">
						<div class="col-md-12 text-center">
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-outline-primary {{ $formaReliquidar ? 'active' : ''}}">
									<input {{ $formaReliquidar ? 'checked="checked"' : ''}} name="freliquidar" type="radio" value="1">Por plazo
								</label>
								<label class="btn btn-outline-primary {{ !$formaReliquidar ? 'active' : ''}}">
									<input {{ !$formaReliquidar ? 'checked="checked"' : ''}} name="freliquidar" type="radio" value="2">Por cuota
								</label>
							</div>
						</div>
					</div>
					<br><br>

					<div class="row">
						<div class="col-md-12" id="fplazo" style="display: {{ ($formaReliquidar ? 'block' : 'none') }}">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group {{ ($errors->has('psaldo')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('psaldo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Saldo
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{!! Form::text('psaldo', $saldoCapital, ['class' => 'form-control text-right', 'data-maskMoney', 'readonly']) !!}
										</div>
										@if ($errors->has('psaldo'))
											<span class="help-block">{{ $errors->first('psaldo') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-1">
									<div class="form-group {{ ($errors->has('pplazo')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('pplazo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Plazo
										</label>
										{!! Form::number('pplazo', $credito->plazo - $altura, ['class' => 'form-control text-right', 'autofocus']) !!}
										@if ($errors->has('pplazo'))
											<span class="help-block">{{ $errors->first('pplazo') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('pperiodicidad')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('pperiodicidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad
										</label>
										{!! Form::select('pperiodicidad', $credito->modalidadCredito->getPeriodicidadesDePagoAdmitidas(), null, ['class' => 'form-control']) !!}
										@if ($errors->has('pperiodicidad'))
											<span class="help-block">{{ $errors->first('pperiodicidad') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('pproximoPago')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('pproximoPago'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Proximo pago
										</label>
										{!! Form::select('pproximoPago', $programaciones, null, ['class' => 'form-control text-right']) !!}
										@if ($errors->has('pproximoPago'))
											<span class="help-block">{{ $errors->first('pproximoPago') }}</span>
										@endif
									</div>
								</div>
								@if($credito->modalidadCredito->tipo_cuota == 'CAPITAL')
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('pproximoPagoIntereses')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('pproximoPagoIntereses'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Proximo pago intereses
										</label>
										{!! Form::select('pproximoPagoIntereses', $programaciones, null, ['class' => 'form-control text-right']) !!}
										@if ($errors->has('pproximoPagoIntereses'))
											<span class="help-block">{{ $errors->first('pproximoPagoIntereses') }}</span>
										@endif
									</div>
								</div>
								@endif
							</div>
						</div>
						<div class="col-md-12" id="fcuota" style="display: {{ (!$formaReliquidar ? 'block' : 'none') }}">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group {{ ($errors->has('csaldo')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('csaldo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Saldo
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{!! Form::text('csaldo', $saldoCapital, ['class' => 'form-control text-right', 'data-maskMoney', 'readonly']) !!}
										</div>
										@if ($errors->has('csaldo'))
											<span class="help-block">{{ $errors->first('csaldo') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group {{ ($errors->has('ccuota')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('ccuota'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Cuota
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{!! Form::text('ccuota', intval($credito->valor_cuota), ['class' => 'form-control text-right', 'data-maskMoney']) !!}
										</div>
										@if ($errors->has('ccuota'))
											<span class="help-block">{{ $errors->first('ccuota') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group {{ ($errors->has('cperiodicidad')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('cperiodicidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad
										</label>
										{!! Form::select('cperiodicidad', $credito->modalidadCredito->getPeriodicidadesDePagoAdmitidas(), null, ['class' => 'form-control']) !!}
										@if ($errors->has('cperiodicidad'))
											<span class="help-block">{{ $errors->first('cperiodicidad') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('cproximoPago')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('cproximoPago'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Proximo pago
										</label>
										{!! Form::select('cproximoPago', $programaciones, null, ['class' => 'form-control text-right']) !!}
										@if ($errors->has('cproximoPago'))
											<span class="help-block">{{ $errors->first('cproximoPago') }}</span>
										@endif
									</div>
								</div>
								@if($credito->modalidadCredito->tipo_cuota == 'CAPITAL')
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('cproximoPagoIntereses')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('cproximoPagoIntereses'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Proximo pago intereses
										</label>
										{!! Form::select('cproximoPagoIntereses', $programaciones, null, ['class' => 'form-control text-right']) !!}
										@if ($errors->has('cproximoPagoIntereses'))
											<span class="help-block">{{ $errors->first('cproximoPagoIntereses') }}</span>
										@endif
									</div>
								</div>
								@endif
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12">
							{!! Form::submit('Previsualizar amortización', ['class' => 'btn btn-outline-primary', 'name' => 'submit']) !!}
							{!! Form::submit('Reliquidar', ['class' => 'btn btn-outline-success', 'name' => 'submit']) !!}
							<a href="{{ url(sprintf('reliquidarCredito?tercero=%s&fecha=%s', $credito->tercero_id, $fecha)) }}" class="btn btn-outline-danger pull-right">Cancelar</a>
						</div>
					</div>

					@if (!empty($amortizacion))
						@if ($amortizacion->count())
							<br><br>
							<div class="row">
								<div class="col-md-10 col-md-offset-1 table-responsive">
									<table class="table table-hover table-stripped">
										<thead>
											<tr>
												<th>Cuota</th>
												<th>Naturaleza</th>
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
											@foreach ($amortizacion as $item)
												<tr>
													<td>{{ $item->numero_cuota }}</td>
													<td>{{ $item->naturaleza_cuota }}</td>
													<td>{{ $item->forma_pago }}</td>
													<td>{{ $item->fecha_cuota }}</td>
													<td class="text-right">${{ number_format($item->abono_capital) }}</td>
													<td class="text-right">${{ number_format($item->abono_intereses) }}</td>
													<td class="text-right">${{ number_format($item->abono_seguro_cartera) }}</td>
													<td class="text-right">${{ number_format($item->total_cuota) }}</td>
													<td class="text-right">${{ number_format($item->nuevo_saldo_capital) }}</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif
					@endif
				</div>
				<div class="card-footer">
				</div>
				{!! Form::close() !!}
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
	$(window).load(function(){
		$("input[name='psaldo']").maskMoney('mask');
		$("input[name='csaldo']").maskMoney('mask');
		$("input[name='ccuota']").maskMoney('mask');
	});
	$(function(){
		$("input[name='freliquidar']").on("change", function(e){
			$valor = $("input[name='freliquidar']:checked").val();
			if($valor == 2) {
				$("#fplazo").hide();
				$("#fcuota").show(150);
			}
			else {
				$("#fplazo").show(150);
				$("#fcuota").hide();
			}
		});
	});
</script>
@endpush
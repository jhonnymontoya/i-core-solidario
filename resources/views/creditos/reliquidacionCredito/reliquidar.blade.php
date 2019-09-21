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
					<?php
						$altura = $credito->alturaObligacion($fecha);
						$fechaAmortizacionPrimerPago = '';
						$fechaAmortizacionUltimoPago = '';
						$amortizaciones = $credito->amortizaciones;
						if($amortizaciones->count()) {
							$fechaAmortizacionPrimerPago = $amortizaciones[0]->fecha_cuota;
							$fechaAmortizacionUltimoPago = $amortizaciones[$amortizaciones->count() - 1]->fecha_cuota;
						}
						$saldoCapital = $credito->saldoObligacion($fecha);
						$ultimoMovimiento = $credito->movimientosCapitalCredito()->orderBy('fecha_movimiento', 'desc')->first();
						$formaReliquidar = empty(old("freliquidar")) ? 1 : old("freliquidar");
						if($formaReliquidar < 1 || $formaReliquidar > 2)$formaReliquidar = 1;
						$formaReliquidar = $formaReliquidar == 1 ? true : false;
					?>
					<div class="row">
						<div class="col-md-3">
							<dl>
								<dt>Valor inicial</dt>
								<dd>${{ number_format($credito->valor_credito, 0) }}</dd>

								<dt>Valor cuota</dt>
								<dd>${{ number_format($credito->valor_cuota, 0) }}</dd>

								<dt>Altura</dt>
								<dd>{{ $altura }} / {{ $credito->plazo }}</dd>

								<dt>Saldo capital</dt>
								<dd>${{ number_format($saldoCapital, 0) }}</dd>

								<dt>Capital vencido</dt>
								<dd>${{ number_format($credito->capitalVencido($fecha), 0) }}</dd>
							</dl>
						</div>

						<div class="col-md-3">
							<dl>
								<dt>Fecha desembolso</dt>
								<dd>{{ $credito->fecha_desembolso }}</dd>

								<dt>Plazo</dt>
								<dd>{{ $credito->plazo }}</dd>

								<dt>Inicio pagos</dt>
								<dd>{{ $fechaAmortizacionPrimerPago }}</dd>

								<dt>Saldo intereses</dt>
								<dd>${{ number_format($credito->saldoInteresObligacion($fecha), 0) }}</dd>

								<dt>Días vencidos</dt>
								<dd>{{ number_format($credito->diasVencidos($fecha), 0) }}</dd>
							</dl>
						</div>

						<div class="col-md-3">
							<dl>
								<dt>Tasa M.V.</dt>
								<dd>{{ number_format($credito->tasa, 2) }}%</dd>

								<dt>Periodicidad</dt>
								<dd>{{ $credito->periodicidad }}</dd>

								<dt>Fin pagos</dt>
								<dd>{{ $fechaAmortizacionUltimoPago }}</dd>

								<dt>Seguro</dt>
								<dd>${{ number_format($credito->saldoSeguroObligacion($fecha), 0) }}</dd>

								<dt>Calificación</dt>
								<dd>{{ $credito->calificacion_obligacion }}</dd>
							</dl>
						</div>

						<div class="col-md-3">
							<dl>
								<dt>Modalidad.</dt>
								<dd>{{ $credito->modalidadCredito->nombre }}</dd>

								<dt>Forma pago</dt>
								<dd>{{ $credito->forma_pago }}</dd>

								<dt>Amortización</dt>
								<dd>{{ $credito->tipo_amortizacion == 'FIJA' ? 'Fija compuesta' : 'Fija capital' }}</dd>

								<dt>Último movimiento</dt>
								<dd>{{ optional($ultimoMovimiento)->fecha_movimiento }}</dd>
							</dl>
						</div>
					</div>

					<br>

					<div class="row">
						<div class="col-md-12 text-center">
							<div class="btn-group btn-group-toggle" data-toggle="buttons">
								<label class="btn btn-primary {{ $formaReliquidar ? 'active' : '' }}">
									<input {{ $formaReliquidar ? 'checked="checked"' : ''}} name="freliquidar" type="radio" value="1">Por plazo
								</label>
								<label class="btn btn-primary {{ !$formaReliquidar ? 'active' : ''}}">
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
									<div class="form-group">
										@php
											$valid = $errors->has('psaldo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Saldo</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('psaldo', $saldoCapital, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Saldo', 'data-maskMoney', 'readonly']) !!}
											@if ($errors->has('psaldo'))
												<div class="invalid-feedback">{{ $errors->first('psaldo') }}</div>
											@endif
										</div>
									</div>
								</div>

								<div class="col-md-1">
									<div class="form-group">
										@php
											$valid = $errors->has('pplazo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Plazo</label>
										{!! Form::number('pplazo', $credito->plazo - $altura, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Plazo']) !!}
										@if ($errors->has('pplazo'))
											<div class="invalid-feedback">{{ $errors->first('pplazo') }}</div>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('pperiodicidad') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad</label>
										{!! Form::select('pperiodicidad', $credito->modalidadCredito->getPeriodicidadesDePagoAdmitidas(), null, ['class' => [$valid, 'form-control']]) !!}
										@if ($errors->has('pperiodicidad'))
											<div class="invalid-feedback">{{ $errors->first('pperiodicidad') }}</div>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('pproximoPago') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Próximo pago</label>
										{!! Form::select('pproximoPago', $programaciones, null, ['class' => [$valid, 'form-control']]) !!}
										@if ($errors->has('pproximoPago'))
											<div class="invalid-feedback">{{ $errors->first('pproximoPago') }}</div>
										@endif
									</div>
								</div>
								@if($credito->modalidadCredito->tipo_cuota == 'CAPITAL')
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('pproximoPagoIntereses') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Próximo pago intereses</label>
										{!! Form::select('pproximoPagoIntereses', $programaciones, null, ['class' => [$valid, 'form-control']]) !!}
										@if ($errors->has('pproximoPagoIntereses'))
											<div class="invalid-feedback">{{ $errors->first('pproximoPagoIntereses') }}</div>
										@endif
									</div>
								</div>
								@endif
							</div>
						</div>
						<div class="col-md-12" id="fcuota" style="display: {{ (!$formaReliquidar ? 'block' : 'none') }}">
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										@php
											$valid = $errors->has('csaldo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Saldo</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('csaldo', $saldoCapital, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Saldo', 'data-maskMoney', 'readonly']) !!}
											@if ($errors->has('csaldo'))
												<div class="invalid-feedback">{{ $errors->first('csaldo') }}</div>
											@endif
										</div>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										@php
											$valid = $errors->has('ccuota') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Cuota</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('ccuota', intval($credito->valor_cuota), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cuota', 'data-maskMoney']) !!}
											@if ($errors->has('ccuota'))
												<div class="invalid-feedback">{{ $errors->first('ccuota') }}</div>
											@endif
										</div>
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										@php
											$valid = $errors->has('cperiodicidad') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad</label>
										{!! Form::select('cperiodicidad', $credito->modalidadCredito->getPeriodicidadesDePagoAdmitidas(), null, ['class' => [$valid, 'form-control']]) !!}
										@if ($errors->has('cperiodicidad'))
											<div class="invalid-feedback">{{ $errors->first('cperiodicidad') }}</div>
										@endif
									</div>
								</div>

								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('cproximoPago') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Próximo pago</label>
										{!! Form::select('cproximoPago', $programaciones, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('cproximoPago'))
											<div class="invalid-feedback">{{ $errors->first('cproximoPago') }}</div>
										@endif
									</div>
								</div>
								@if($credito->modalidadCredito->tipo_cuota == 'CAPITAL')
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('cproximoPagoIntereses') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Próximo pago intereses</label>
										{!! Form::select('cproximoPagoIntereses', $programaciones, null, ['class' => [$valid, 'form-control']]) !!}
										@if ($errors->has('cproximoPagoIntereses'))
											<div class="invalid-feedback">{{ $errors->first('cproximoPagoIntereses') }}</div>
										@endif
									</div>
								</div>
								@endif
							</div>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12 text-right">
							{!! Form::submit('Previsualizar amortización', ['class' => 'btn btn-outline-primary', 'name' => 'submit']) !!}
							{!! Form::submit('Reliquidar', ['class' => 'btn btn-outline-success', 'name' => 'submit']) !!}
							<a href="{{ url(sprintf('reliquidarCredito?tercero=%s&fecha=%s', $credito->tercero_id, $fecha)) }}" class="btn btn-outline-danger pull-right">Cancelar</a>
						</div>
					</div>

					@if (!empty($amortizacion))
						@if ($amortizacion->count())
							<br><br>
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-hover table-striped">
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
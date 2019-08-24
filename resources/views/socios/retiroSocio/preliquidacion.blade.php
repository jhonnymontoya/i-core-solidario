@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Proceso de retiros
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Proceso de retiros</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif

		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Cálculo liquidación de retiro</h3>
				</div>
				<div class="card-body">
					{!! Form::open(['url' => 'retiroSocio/preliquidacion', 'method' => 'get', 'role' => 'form']) !!}
					{!! Form::hidden("preliquidar", true) !!}
					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('socio_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('socio_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Socio
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-male"></i></span>
									{!! Form::select('socio_id', [], null, ['class' => 'form-control select2', 'tabIndex' => '6']) !!}
								</div>
								@if ($errors->has('socio_id'))
									<span class="help-block">{{ $errors->first('socio_id') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('fechaMovimiento')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('fechaMovimiento'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha movimiento
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									{!! Form::text('fechaMovimiento', Request::has('fechaMovimiento') ? Request::get('fechaMovimiento') : date('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('fechaMovimiento'))
									<span class="help-block">{{ $errors->first('fechaMovimiento') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('fechaSaldo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('fechaSaldo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha saldo
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									{!! Form::text('fechaSaldo', Request::has('fechaSaldo') ? Request::get('fechaSaldo') : date('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('fechaSaldo'))
									<span class="help-block">{{ $errors->first('fechaSaldo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								{!! Form::submit('Preliquidar', ['class' => 'btn btn-outline-success']) !!}
							</div>
						</div>
					</div>
					{!! Form::close() !!}

					@if($preliquidacion->count())
						<br>
						<div class="row">
							<div class="col-md-3">
								<label>Socio</label><br>
								{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->numero_identificacion }} {{ $socio->tercero->nombre }}
							</div>
							<div class="col-md-2">
								<label>Fecha afiliación</label><br>
								{{ $socio->fecha_afiliacion }}
							</div>
							<div class="col-md-1">
								<?php
									$label = "default";
									switch ($socio->estado) {
										case 'ACTIVO':
											$label = 'green';
											break;
										case 'NOVEDAD':
											$label = 'orange';
											break;
										case 'RETIRO':
											$label = 'maroon';
											break;
										case 'LIQUIDADO':
											$label = 'red';
											break;
										case 'PROCESO':
											$label = 'light-blue';
											break;
									}
								?>
								<label>Estado</label><br>
								<span class="label bg-{{ $label }}">{{ $socio->estado }}</span>
							</div>
							<div class="col-md-2">
								<?php
									$retiro = $socio->sociosRetiros->first();
									$fechaRetiro = '-';
									if(!empty($retiro))
									{
										$fechaRetiro = $retiro->fecha_solicitud_retiro;
									}
								?>
								<label>Fecha retiro</label><br>
								{{ $fechaRetiro }}
							</div>
							<div class="col-md-2">
								<label>Fecha movimiento</label><br>
								{{ $fechaMovimiento }}
							</div>
							<div class="col-md-2">
								<label>Fecha saldos</label><br>
								{{ $fechaSaldo }}
							</div>
						</div>
						<div class="row">
							<div class="col-md-5 table-responsive">
								<h3>Saldos a favor</h3>
								<table class="table table-bordered table-striped table-hover">
									<thead>
										<tr>
											<th>Nombre concepto</th>
											<th class="text-center">Saldo</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$ahorros = 0;
										?>
										@foreach($preliquidacion as $pre)
											@if($pre->tipo == 'AHORRO')
												<?php
													$ahorros += $pre->saldo + $pre->interes;
												?>
												<tr>
													<td>{{ $pre->nombre }}</td>
													<td class="text-right">${{ number_format($pre->saldo, 0) }}</td>
													<td class="text-right">${{ number_format($pre->interes, 0) }}</td>
													<td class="text-right">${{ number_format($pre->saldo + $pre->interes, 0) }}</td>
												</tr>
											@endif
										@endforeach
									</tbody>
								</table>
							</div>

							<div class="col-md-7 table-responsive">
								<h3>Saldos en contra</h3>
								<table class="table table-bordered table-striped table-hover">
									<thead>
										<tr>
											<th>Numero obligación</th>
											<th class="text-center">Saldo</th>
											<th class="text-center">Interes</th>
											<th class="text-center">Seguros</th>
											<th class="text-center">Total</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$creditos = 0;
										?>
										@foreach($preliquidacion as $pre)
											@if($pre->tipo == 'CRÉDITO' || $pre->tipo == 'GMF')
												<?php
													$creditos += $pre->saldo + $pre->interes + $pre->seguro;
												?>
												<tr>
													<td>{{ $pre->nombre }}</td>
													<td class="text-right">${{ number_format($pre->saldo, 0) }}</td>
													<td class="text-right">${{ number_format($pre->interes, 0) }}</td>
													<td class="text-right">${{ number_format($pre->seguro, 0) }}</td>
													<td class="text-right">${{ number_format($pre->saldo + $pre->interes + $pre->seguro, 0) }}</td>
												</tr>
											@endif
										@endforeach
									</tbody>
								</table>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3 table-responsive">
								<table class="table">
									<tbody>
										<tr>
											<th>Total saldo a favor</th>
											<td class="text-right">${{ number_format($ahorros, 0) }}</td>
										</tr>
										<tr>
											<th>Total saldo en contra</th>
											<td class="text-right">${{ number_format($creditos, 0) }}</td>
										</tr>
										<tr><td></td></tr>
										<tr>
											<th>Total liquidación</th>
											<td class="text-right">
												<?php
													$liquidacion = $ahorros - $creditos;
												?>
												<strong class="{{ $liquidacion < 0 ? 'text-danger' : '' }}">${{ number_format($liquidacion, 0) }}</strong>
											</td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						@if($socio->logLiquidacionesRetiros->count())
							<br>
							<div class="row">
								<div class="col-md-6">
									<label class="text-danger">Mensajes de alerta</label><br>
									<ul>
										@foreach($socio->logLiquidacionesRetiros as $log)
											<li>{{ $log->mensaje }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						@endif
						<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="tituloConfirmacion">Liquidación de usuario</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-10 col-md-offset-1">
												<div class="alert alert-warning">
													<h4>
														<i class="fa fa-warning"></i>&nbsp;Alerta!
													</h4>
													Confirme los datos antes de ejecutar el proceso de liquidación
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-10 col-md-offset-1">
												<dl class="dl-horizontal">
													<dt>Socio:</dt>
													<dd>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->numero_identificacion }} {{ $socio->tercero->nombre }}</dd>
													<dt>Fecha movimiento:</dt>
													<dd>{{ $fechaMovimiento }}</dd>
													<dt>Fecha saldos:</dt>
													<dd>{{ $fechaSaldo }}</dd>
												</dl>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<a class="btn btn-outline-success" id="continuar">Liquidar</a>
										<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
									</div>
								</div>
							</div>
						</div>
					@endif
				</div>
				<div class="card-footer">
					@if($preliquidacion->count())
						{!! Form::model($socio, ['route' => ['retiroSocioLiquidar', $socio], 'method' => 'put', 'role' => 'form', 'id' => 'formProcesar']) !!}
						{!! Form::hidden('fecha_movimiento', $fechaMovimiento) !!}
						{!! Form::hidden('fecha_saldo', $fechaSaldo) !!}
						{!! Form::close() !!}
						<button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#confirmacion">Liquidar</button>
					@endif
					<a href="{{ url('retiroSocio') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$("select[name='socio_id']").select2({
			allowClear: true,
			placeholder: "Seleccione un socio",
			ajax: {
				url: '{{ url('socio/getSocioConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});
		@if(!empty(Request::has('socio_id')))
			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ Request::get('socio_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio_id']"));
					$("select[name='socio_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("input[name='fechaMovimiento']").on('keyup keypress change', function(e){
				$("input[name='fechaSaldo']").val($(this).val());
		});

		$("#continuar").click(function(){
			$("#continuar").addClass("disabled");
			$("#formProcesar").submit();
		});
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		{!! Form::model($solicitud, ['url' => ['solicitudCredito', $solicitud, 'procesar'], 'method' => 'put', 'role' => 'form', 'id' => 'formProcesar']) !!}
		{!! Form::hidden("metodo", "tesoreria") !!}
		<div class="container-fluid">
			<div class="card card-warning card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Desembolsar solicitud de crédito</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="alert alert-success">
								<h4>
									<i class="fa fa-info-circle"></i>&nbsp;Confirmación desembolso
								</h4>
								Confirmar la liquidación y las opciones de desembolso de la solicitud de crédito
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Solicitante:</dt>
								<dd>{{ $solicitud->tercero->tipoIdentificacion->codigo }} {{ $solicitud->tercero->numero_identificacion }} - {{ $solicitud->tercero->nombre_corto }}</dd>
								<dt>Fecha de aprobación:</dt>
								<dd>{{ $solicitud->fecha_aprobacion }} ({{ $solicitud->fecha_aprobacion->diffForHumans() }})</dd>
								<dt>Modalidad de crédito:</dt>
								<dd>{{ $solicitud->modalidadCredito->codigo }} - {{ $solicitud->modalidadCredito->nombre }}</dd>
								<dt>Valor aprobado:</dt>
								<dd>${{ number_format($solicitud->valor_credito) }}</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Fecha de solicitud:</dt>
								<dd>{{ $solicitud->fecha_solicitud }} ({{ $solicitud->fecha_solicitud->diffForHumans() }})</dd>
								<dt>Fecha de desembolso:</dt>
								<dd>{{ $solicitud->fecha_desembolso }}</dd>
								<dt>Valor solicitado:</dt>
								<dd>${{ number_format($solicitud->valor_solicitud) }}</dd>
								<dt>Cuotas:</dt>
								<dd>{{ $solicitud->plazo }} ({{ $solicitud->periodicidad }})</dd>
							</dl>
						</div>
					</div>

					<h4>Liquidación</h4>
					<div class="row">
						<div class="col-md-6">
							<dl class="dl-horizontal">
								<dt>Valor final crédito:</dt>
								<dd>${{ number_format($liquidacion->valorFinalCredito) }}</dd>
								<dt>Cobro administrativo:</dt>
								<dd>{{ !is_null($liquidacion->nombreCobro) ? $liquidacion->nombreCobro : 'no aplica' }}</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl class="dl-horizontal">
								<dt>Créditos consolidados:</dt>
								<dd>${{ number_format($liquidacion->valorCreditoRecogidos) }}</dd>
								<dt>Valor cobro:</dt>
								<dd>${{ number_format($liquidacion->valorCobro) }}</dd>
							</dl>
						</div>
					</div>
					<h4>Total desembolso:</h4>
					<strong><h4>${{ number_format($liquidacion->desembolso) }}</h4></strong>
					<br>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							@if($solicitud->tieneInconsistencias())
								<h4 class="text-danger"><i class="fa fa-times-circle"></i> La solicitud presenta inconsistencias por corregir</h3>
							@else
								<h4 class="text-success"><i class="fa fa-check-circle"></i> La solicitud no presenta inconsistencias</h4>
							@endif
						</div>
					</div>
					<br>
					@if($solicitud->tieneInconsistencias())
						<div class="row">
							<div class="col-md-10 col-md-offset-1 table-responsive">
								<table class="tabl table-striped table-hover">
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
											@if(!$condicion->cumple)
												@if($condicion->condicion == 'Cupo')
													<tr>
														<td>{{ $condicion->condicion }}</td>
														<td>
															{{ number_format($condicion->valor_parametro, 0) }}
														</td>
														<td>{{ number_format($condicion->valor_solicitud, 0) }}</td>
														<td>
															<?php
																$cumple = $condicion->cumple_parametro;
																$aprobado = false;
																if(!$cumple) {
																	$cumple = empty($condicion->es_aprobada) ? false : true;
																	$aprobado = $cumple;
																}
															?>
															<span class="badge badge-pill badge-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																<?php
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
															</span>
														</td>
													</tr>
												@elseif($condicion->condicion != 'Documentación')
													<tr>
														<td>{{ $condicion->condicion }}</td>
														<td>
															@if(empty($condicion->valor_parametro))
																<i data-toggle="tooltip" title="Modalidad inconsistente, revisar parámetros" class="fa fa-exclamation-triangle" style="color:#FF851b;"></i>
															@else
																{{ number_format($condicion->valor_parametro, 0) }}
															@endif
														</td>
														<td>{{ number_format($condicion->valor_solicitud, 0) }}</td>
														<td>
															<?php
																$cumple = $condicion->cumple_parametro;
																$aprobado = false;
																if(!$cumple) {
																	$cumple = empty($condicion->es_aprobada) ? false : true;
																	$aprobado = $cumple;
																}
															?>
															<span class="badge badge-pill badge-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																<?php
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
															</span>
														</td>
													</tr>
												@else
													<tr>
														<td>{{ $condicion->condicion }}</td>
														<td>{{ $condicion->valor_parametro }}</td>
														<td>{{ $condicion->valor_solicitud }}</td>
														<td>
															<?php
																$cumple = $condicion->cumple_parametro;
																$aprobado = false;
																if(!$cumple) {
																	$cumple = empty($condicion->es_aprobada) ? false : true;
																	$aprobado = $cumple;
																}
															?>
															<span class="badge badge-pill badge-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																<?php
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
															</span>
														</td>
													</tr>
												@endif
											@endif
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@endif

					@if(!$solicitud->tieneInconsistencias())
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<a href="#" id="desembolsoPorTesoreria" class="btn btn-outline-primary">Desembolso por tesorería</a>
								<a href="#" id="desembolsoContable" class="btn btn-outline-primary">Desembolso contable</a>
								<a href="#" id="desembolsoATercero" class="btn btn-outline-primary">Desembolso a tercero</a>
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<h4 id="desembolso">Desembolso por tesorería</h4>
							</div>
						</div>
						<div class="row">
							<div class="col-md-5 col-md-offset-1">
								<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuenta'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Selección cuenta
									</label>
									{!! Form::select('cuenta', [], null, ['class' => 'form-control', 'placeholder' => 'Seleccione cuenta', 'autocomplete' => 'off']) !!}
									@if ($errors->has('cuenta'))
										<span class="help-block">{{ $errors->first('cuenta') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-5 tercero">
								<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tercero'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Selección tercero
									</label>
									{!! Form::select('tercero', [], null, ['class' => 'form-control', 'placeholder' => 'Seleccione tercero', 'autocomplete' => 'off']) !!}
									@if ($errors->has('tercero'))
										<span class="help-block">{{ $errors->first('tercero') }}</span>
									@endif
								</div>
							</div>
						</div>
					@endif
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer text-right">
					@if(!$solicitud->tieneInconsistencias())
					<a href="#" class="btn btn-outline-success" id="procesar">Procesar</a>
					@endif
					<a href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" class="btn btn-outline-danger pull-right" tabindex="2">Volver</a>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){

		function limpiar() {
			$("select[name='cuenta']").val(null).trigger("change");
			$("select[name='tercero']").val(null).trigger("change");
			$(".tercero").hide();
		}

		$(document).ready(function(){
			limpiar();
		});

		$("#desembolsoPorTesoreria").click(function(e){
			e.preventDefault();
			$("input[name='metodo']").val("tesoreria");
			$("select[name='cuenta']").select2("destroy");
			$("#desembolso").text("Desembolso por tesorería");
			limpiar();
			$("select[name='cuenta']").select2({
				allowClear: true,
				placeholder: "Seleccione una opción",
				ajax: {
					url: '{{ url('cuentaContable/getCuentaConParametros') }}',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							modulo: '1',
							estado: '1',
							tipoCuenta: 'AUXILIAR'
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
		});

		$("#desembolsoContable").click(function(e){
			e.preventDefault();
			$("input[name='metodo']").val("contable");
			$("select[name='cuenta']").select2("destroy");
			$("#desembolso").text("Desembolso contable");
			limpiar();
			$("select[name='cuenta']").select2({
				allowClear: true,
				placeholder: "Seleccione una opción",
				ajax: {
					url: '{{ url('cuentaContable/getCuentaConParametros') }}',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							modulo: '1,2',
							estado: '1',
							tipoCuenta: 'AUXILIAR'
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
		});

		$("#desembolsoATercero").click(function(e){
			e.preventDefault();
			$("input[name='metodo']").val("tercero");
			$("select[name='cuenta']").select2("destroy");
			$("#desembolso").text("Desembolso a tercero");
			limpiar();
			$(".tercero").show();
			$("select[name='cuenta']").select2({
				allowClear: true,
				placeholder: "Seleccione una opción",
				ajax: {
					url: '{{ url('cuentaContable/getCuentaConParametros') }}',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							modulo: '1,2',
							estado: '1',
							tipoCuenta: 'AUXILIAR'
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
		});

		$("select[name='cuenta']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						modulo: '1',
						estado: '1',
						tipoCuenta: 'AUXILIAR'
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

		@if(!empty(old('cuenta')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta']"));
					$("select[name='cuenta']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='tercero']").select2({
			allowClear: true,
			placeholder: "Seleccione un tercero",
			ajax: {
				url: "{{ url('tercero/getTerceroConParametros') }}",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						estado: 'ACTIVO'
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

		@if(!empty(old('tercero')))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ old('tercero') }} }}).done(function(data){
				if(data.total_count == 1)  {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tercero']"));
					$("select[name='tercero']").val(element.id).trigger("change");
				}
			});
		@endif

		$("#procesar").click(function(e){
			e.preventDefault();
			$("#procesar").addClass("disabled");
			$("#formProcesar").submit();
		});
	});
</script>
@endpush

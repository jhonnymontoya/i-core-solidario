@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Solicitudes de crédito
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Solicitudes de crédito</li>
		</ol>
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
		<div class="row">
			<div class="col-md-12">
				<div class="card card-warning">
					<div class="card-header with-border">
						<h3 class="card-title">Desembolsar solicitud de crédito</h3>
					</div>
					{{-- INICIO card BODY --}}
					<div class="card-body">
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-success">
									<h4>
										<i class="fa fa-info-circle"></i>&nbsp;Confirmación desembolso
									</h4>
									Confirmar la liquidación y las opciones de desembolso de la solicitud de crédito
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-5 col-md-offset-1">
								<dl class="dl-horizontal">
									<dt>Solicitante:</dt>
									<dd>{{ $solicitud->tercero->tipoIdentificacion->codigo }} {{ $solicitud->tercero->numero_identificacion }} - {{ $solicitud->tercero->nombre_corto }}</dd>
									<dt>Fecha de solicitud:</dt>
									<dd>{{ $solicitud->fecha_solicitud }} ({{ $solicitud->fecha_solicitud->diffForHumans() }})</dd>
									<dt>Fecha de aprobación:</dt>
									<dd>{{ $solicitud->fecha_aprobacion }} ({{ $solicitud->fecha_aprobacion->diffForHumans() }})</dd>
									<dt>Fecha de desembolso:</dt>
									<dd><strong>{{ $solicitud->fecha_desembolso }}</strong></dd>
									<dt>Modalidad de crédito:</dt>
									<dd>{{ $solicitud->modalidadCredito->codigo }} - {{ $solicitud->modalidadCredito->nombre }}</dd>
									<dt>Valor solicitado:</dt>
									<dd>${{ number_format($solicitud->valor_solicitud) }}</dd>
									<dt>Valor aprobado:</dt>
									<dd>${{ number_format($solicitud->valor_credito) }}</dd>
									<dt>Cuotas:</dt>
									<dd>{{ $solicitud->plazo }} ({{ $solicitud->periodicidad }})</dd>
								</dl>
							</div>
						</div>

						<div class="row">
							<div class="col-md-5 col-md-offset-1">
								<dl class="dl-horizontal">
									<h4 class="text-center">Liquidación</h4 class="text-center">
									<dt>Valor final crédito:</dt>
									<dd>${{ number_format($liquidacion->valorFinalCredito) }}</dd>
									<dt>Créditos consolidados:</dt>
									<dd>${{ number_format($liquidacion->valorCreditoRecogidos) }}</dd>
									<dt>Cobro administrativo:</dt>
									<dd>{{ !is_null($liquidacion->nombreCobro) ? $liquidacion->nombreCobro : 'no aplica' }}</dd>
									<dt>Valor cobro:</dt>
									<dd>${{ number_format($liquidacion->valorCobro) }}</dd>
									<dt><h4>Total desembolso:</h4></dt>
									<dd><strong><h4>${{ number_format($liquidacion->desembolso) }}</h4></strong></dd>
								</dl>
							</div>
						</div>

						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								@if($solicitud->tieneInconsistencias())
									<h4 class="text-danger"><i class="fa fa-times-circle"></i> La solicitud presenta inconsistencias por corregir</h3>
								@else
									<h4 class="text-success"><i class="fa fa-check-circle"></i> La solicitud no presenta inconsistencias</h4>
								@endif
							</div>
						</div>

						@if($solicitud->tieneInconsistencias())
							<div class="row">
								<div class="col-md-10 col-md-offset-1 table-responsive">
									<table class="table table-hover">
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
																	if(!$cumple)
																	{
																		$cumple = empty($condicion->es_aprobada) ? false : true;
																		$aprobado = $cumple;
																	}
																?>
																<span class="label label-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																	<?php
																		if($cumple)
																		{
																			if($aprobado)
																			{
																				echo "aprobado";
																			}
																			else
																			{
																				echo "Sí";
																			}
																		}
																		else
																		{
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
																	<i data-toggle="tooltip" title="Modalidad inconsistente, revisar parámetros" class="fa fa-warning" style="color:#FF851b;"></i>
																@else
																	{{ number_format($condicion->valor_parametro, 0) }}
																@endif
															</td>
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
																?>
																<span class="label label-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																	<?php
																		if($cumple)
																		{
																			if($aprobado)
																			{
																				echo "aprobado";
																			}
																			else
																			{
																				echo "Sí";
																			}
																		}
																		else
																		{
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
																	if(!$cumple)
																	{
																		$cumple = empty($condicion->es_aprobada) ? false : true;
																		$aprobado = $cumple;
																	}
																?>
																<span class="label label-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																	<?php
																		if($cumple)
																		{
																			if($aprobado)
																			{
																				echo "aprobado";
																			}
																			else
																			{
																				echo "Sí";
																			}
																		}
																		else
																		{
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
									<a id="desembolsoPorTesoreria" class="btn btn-primary">Desembolso por tesorería</a>
									<a id="desembolsoContable" class="btn btn-primary">Desembolso contable</a>
									<a id="desembolsoATercero" class="btn btn-primary">Desembolso a tercero</a>
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
					<div class="card-footer">
						@if(!$solicitud->tieneInconsistencias())
						<a class="btn btn-success" id="procesar">Procesar</a>
						{{--{!! Form::submit('Procesar', ['class' => 'btn btn-success', 'tabindex' => '1']) !!}--}}
						@endif
						<a href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" class="btn btn-danger pull-right" tabindex="2">Volver</a>
					</div>
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

		function limpiar()
		{
			$("select[name='cuenta']").val(null).trigger("change");
			$("select[name='tercero']").val(null).trigger("change");
			$(".tercero").hide();
		}

		$(document).ready(function(){
			limpiar();
		});

		$("#desembolsoPorTesoreria").click(function(){
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

		$("#desembolsoContable").click(function(){
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

		$("#desembolsoATercero").click(function(){
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

		$("#procesar").click(function(){
			$("#procesar").addClass("disabled");
			$("#formProcesar").submit();
		});
	});
</script>
@endpush

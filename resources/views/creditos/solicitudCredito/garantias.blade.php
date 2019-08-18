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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar garantías</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">
										Modalidad de crédito
									</label>
									{!! Form::text('modalidad', $solicitud->modalidadCredito->codigo . ' - ' . $solicitud->modalidadCredito->nombre, ['class' => 'form-control', 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off', 'readonly']) !!}
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">
										Solicitante
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-male"></i></span>
										@php
											$nombreMostar = $solicitud->tercero->tipoIdentificacion->codigo . ' ' . $solicitud->tercero->numero_identificacion . ' - ' . $solicitud->tercero->nombre_corto;
										@endphp
										<a href="{{ url('socio/consulta') }}?socio={{ $solicitud->tercero->socio->id }}&fecha={{ $solicitud->fecha_solicitud }}" target="_blank" class="form-control" style="background-color: #eee;" >{{ $nombreMostar }} <small><i class="fa fa-external-link"></i></small></a>
									</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label class="control-label">
										Fecha solicitud
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::text('fecha_solicitud', $solicitud->fecha_solicitud, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'readonly']) !!}
									</div>
								</div>
							</div>
						</div>
						{{-- INICIO FILA --}}
						<div class="row">
							<div class="col-md-12">
								<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
							</div>
						</div>
						{{-- FIN FILA --}}
						<hr>

						<div class="row form-horizontal">
							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-6 control-label">
										Valor solicitud
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('valor_credito', $solicitud->valor_credito, ['class' => 'form-control text-right', 'autofocus', 'data-maskMoney', 'readonly']) !!}
									</div>
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									<label class="col-md-6 control-label">
										Tasa M.V.
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon">%</span>
										{!! Form::text('tasa', number_format($solicitud->tasa, 2), ['class' => 'form-control', 'readonly']) !!}
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<?php
									switch($solicitud->estado_solicitud)
									{
										case 'RADICADO':
											?>
											<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
											<?php
											break;
										case 'APROBADO':
											?>
											<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
											<?php
											break;
										
										default:
											break;
									}
								?>
							</div>
						</div>
						<hr>

						<ul class="nav nav-tabs" role="tablist">
							<li role="presentation" class="active">
								<a href="#codeudores" aria-controls="codeudores" role="tab" data-toggle="tab">Codeudores</a>
							</li>
							<li role="presentation" class="disabled">
								<a>Real</a>
							</li>
							<li role="presentation" class="disabled">
								<a>Fondo garantias</a>
							</li>
						</ul>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active" id="codeudores">
								<br>
								<div class="row">
									<div class="col-md-12">
										<ul>
											@foreach ($data['garantias'] as $key => $garantia)
												<li class="{{ $garantia['cumplido'] ? 'text-success' : 'text-danger' }}">
													<i class="fa {{ $garantia['cumplido'] ? 'fa-check' : 'fa-times' }}"></i>
													Agregado {{ $garantia['cantidad'] }} de {{ $garantia['total'] }} {{ $garantia['nombre'] }}
												</li>
											@endforeach
										</ul>
									</div>
								</div>
								<hr>
								{!! Form::open(['route' => ['solicitudCreditoPostCodeudor', $solicitud->id], 'method' => 'post', 'role' => 'form']) !!}
								<div class="row">
									<div class="col-md-4">
										<div class="form-group {{ ($errors->has('tipo')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('tipo'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Tipo
											</label>
											{!! Form::select('tipo', $tiposCodeudores, null, ['class' => 'form-control', 'placeholder' => 'NO REQUERIDO']) !!}
											@if ($errors->has('tipo'))
												<span class="help-block">{{ $errors->first('tipo') }}</span>
											@endif
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group {{ ($errors->has('codeudor')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('codeudor'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Codeudor
											</label>
											{!! Form::select('codeudor', [], null, ['class' => 'form-control select2', 'placeholder' => 'NO REQUERIDO']) !!}
											@if ($errors->has('codeudor'))
												<span class="help-block">{{ $errors->first('codeudor') }}</span>
											@endif
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label">&nbsp;</label>
											<br>
											{!! Form::submit('Agregar', ['class' => 'btn btn-success']) !!}
										</div>
									</div>
								</div>
								{!! Form::close() !!}

								<div class="row">
									<div class="col-md-12">
										<div class="panel-group" id="codeudores">
											@php
												$id = 0;
											@endphp
											@foreach ($data['codeudores'] as $codeudor)
												<div class="panel panel-default">
													<div class="panel-heading" role="tab" id="codeudor{{++$id}}">
														<h4 class="panel-title">
															<a role="button" data-toggle="collapse" data-parent="#accordion" href="#cod{{$id}}" aria-expanded="false" aria-controls="cod{{$id}}">
																{{ $codeudor['numeroIdentificacion'] }} - {{ $codeudor['nombre'] }} - {{ $codeudor['nombreGarantia'] }}
															</a>
															<a href="{{ route('solicitudCreditoDeleteCodeudor', [$solicitud->id, $codeudor['id']]) }}" class="btn btn-danger btn-xs pull-right"><font color="#fff"><i class="fa fa-trash"></i></font></a>
														</h4>
													</div>
													<div id="cod{{$id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="codeudor{{++$id}}">
														<div class="panel-body">
															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Tipo condición:</label>
																</div>
																<div class="col-md-9">
																	{{ $codeudor['tipoCondicion'] }}
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Admite codeudor externo:</label>
																</div>
																<div class="col-md-9">
																	{{ $codeudor['admiteCodeudorExterno'] ? 'Sí' : 'No' }}
																	<i class="fa fa-{{ $codeudor['valorAdmiteCodeudorExterno'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Valida cupo codeudor:</label>
																</div>
																<div class="col-md-9">
																	${{ number_format($codeudor['cupoCodeudor']) }}
																	<i class="fa fa-{{ $codeudor['valorValidaCupoCodeudor'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Límite número codeudas:</label>
																</div>
																<div class="col-md-9">
																	{{ $codeudor['limiteObligacionesCodeudor'] }}
																	<i class="fa fa-{{ $codeudor['valorTieneLimiteObligacionesCodeudor'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Límite saldo codeudas:</label>
																</div>
																<div class="col-md-9">
																	${{ number_format($codeudor['valorSaldoCodeudas']) }}
																	<i class="fa fa-{{ $codeudor['valorTieneLimiteSaldoCodeudas'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Requiere antigüedad (días):</label>
																</div>
																<div class="col-md-9">
																	{{ number_format($codeudor['valorAntiguedadCodeudor']) }}
																	<i class="fa fa-{{ $codeudor['valorValidaAntiguedadCodeudor'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Calificación:</label>
																</div>
																<div class="col-md-9">
																	{{ $codeudor['valorCalificacionCodeudor'] }}
																	<i class="fa fa-{{ $codeudor['valorValidaCalificacionCodeudor'] ? 'check' : 'times' }}"></i>
																</div>
															</div>

															<div class="row">
																<div class="col-md-3 text-right">
																	<label>Resultado:</label>
																</div>
																<div class="col-md-9">
																	@php
																		$color = '';
																		switch($codeudor['resultado']) {
																			case 'No requerido':
																				$color = 'info';
																				break;
																			case 'Aceptado':
																				$color = 'success';
																				break;
																			case 'No aceptado':
																				$color = 'warning';
																				break;
																			default:
																				break;
																		}
																	@endphp
																	<span class="label label-{{ $color }}">{{ $codeudor['resultado'] }}</span>
																</div>
															</div>
														</div>
													</div>
												</div>
											@endforeach
										</div>
									</div>
								</div>
							</div>
							<div role="tabpanel" class="tab-pane fade in active" id="real">
							</div>
							<div role="tabpanel" class="tab-pane fade in active" id="aval">
							</div>
						</div>
					</div>

					<div class="card-footer">
						<?php
							switch($solicitud->estado_solicitud)
							{
								case 'RADICADO':
									?>
									<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
									<?php
									break;
								case 'APROBADO':
									?>
									<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
									<?php
									break;
								
								default:
									break;
							}
						?>
					</div>
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
		$(window).load(function(){
			$("input[name='valor_credito']").maskMoney('mask');
		});

		$("select[name='codeudor']").select2({
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
						estado: 'ACTIVO',
						tipo: 'NATURAL'
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

		@if(!empty(old('codeudor')))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ old('codeudor') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='codeudor']"));
					$("select[name='codeudor']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

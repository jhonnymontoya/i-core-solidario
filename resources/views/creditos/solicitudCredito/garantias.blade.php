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

		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar garantías</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Modalidad de crédito</label>
								{!! Form::text('modalidad', $solicitud->modalidadCredito->codigo . ' - ' . $solicitud->modalidadCredito->nombre, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'Modalidad de crédito', 'readonly']) !!}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Solicitante</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-male"></i>
										</span>
									</div>
									@php
										$nombreMostar = $solicitud->tercero->tipoIdentificacion->codigo . ' ' . $solicitud->tercero->numero_identificacion . ' - ' . $solicitud->tercero->nombre_corto;
									@endphp
									<a href="{{ url('socio/consulta') }}?socio={{ $solicitud->tercero->socio->id }}&fecha={{ $solicitud->fecha_solicitud }}" target="_blank" class="form-control" style="background-color: #eee;" >{{ $nombreMostar }} <small><i class="fas fa-external-link-alt"></i></small></a>
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Fecha solicitud</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_solicitud', $solicitud->fecha_solicitud, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
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

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('valor_credito') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor solicitud</label>
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text">$</span></div>
									{!! Form::text('valor_credito', $solicitud->valor_credito, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Valor solicitud', 'data-maskMoney', 'readonly']) !!}
									@if ($errors->has('valor_credito'))
										<div class="invalid-feedback">{{ $errors->first('valor_credito') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Tasa M.V.</label>
								<div class="input-group">
									{!! Form::text('tasa', number_format($solicitud->tasa, 2), ['class' => ['form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Tasa M.V.', 'readonly']) !!}
									<div class="input-group-append"><span class="input-group-text">%</span></div>
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12 text-right">
							<?php
								switch($solicitud->estado_solicitud) {
									case 'BORRADOR':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									case 'RADICADO':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									case 'APROBADO':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									
									default:
										break;
								}
							?>
						</div>
					</div>
					<hr>

					<ul class="nav nav-pills mb-3" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#codeudores" role="tab" aria-selected="true">Codeudores</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled" data-toggle="pill" href="#" role="tab" aria-selected="false">Real</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled" data-toggle="pill" href="#" role="tab" aria-selected="false">Fondo garantias</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane fade show active" id="codeudores" role="tabpanel">
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
									<div class="form-group">
										@php
											$valid = $errors->has('tipo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tipo</label>
										{!! Form::select('tipo', $tiposCodeudores, null, ['class' => [$valid, 'form-control'], 'placeholder' => 'NO REQUERIDO']) !!}
										@if ($errors->has('tipo'))
											<div class="invalid-feedback">{{ $errors->first('tipo') }}</div>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										@php
											$valid = $errors->has('codeudor') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Codeudor</label>
										{!! Form::select('codeudor', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'NO REQUERIDO']) !!}
										@if ($errors->has('codeudor'))
											<div class="invalid-feedback">{{ $errors->first('codeudor') }}</div>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">&nbsp;</label>
										<br>
										{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success']) !!}
									</div>
								</div>
							</div>
							{!! Form::close() !!}

							<div class="row">
								<div class="col-md-12">
									<div class="accordion" id="codeudores">
										@php
											$id = 0;
										@endphp
										@foreach ($data['codeudores'] as $codeudor)
											<div class="card">
												<div class="card-header" id="codeudor{{++$id}}">
													<h2 class="mb-0">
														<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#cod{{$id}}" aria-expanded="false" aria-controls="cod{{$id}}">
															{{ $codeudor['numeroIdentificacion'] }} - {{ $codeudor['nombre'] }} - {{ $codeudor['nombreGarantia'] }}
														</button>
														<a href="{{ route('solicitudCreditoDeleteCodeudor', [$solicitud->id, $codeudor['id']]) }}" class="btn btn-outline-danger btn-sm float-right"><i class="far fa-trash-alt"></i></a>
													</h2>
												</div>

												<div id="cod{{$id}}" class="collapse" aria-labelledby="codeudor{{$id}}" data-parent="#codeudores">
													<div class="card-body">
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
																<span class="badge badge-pill badge-{{ $color }}">{{ $codeudor['resultado'] }}</span>
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
						<div class="tab-pane fade" id="real" role="tabpanel"></div>
						<div class="tab-pane fade" id="aval" role="tabpanel"></div>
					</div>
				</div>

				<div class="card-footer text-right">
					<?php
						switch($solicitud->estado_solicitud) {
							case 'RADICADO':
								?>
								<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;
							case 'APROBADO':
								?>
								<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;							
							default:
								break;
						}
					?>
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.disabled {
		cursor: not-allowed;
	}
</style>
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
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='codeudor']"));
					$("select[name='codeudor']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

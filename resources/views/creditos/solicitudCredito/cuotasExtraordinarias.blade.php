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
						<h3 class="card-title">Cuotas extraordinarias</h3>
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
								<div class="form-group {{ ($errors->has('valor_credito')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('valor_credito'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Valor solicitud
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('valor_credito', $solicitud->valor_credito, ['class' => 'form-control text-right', 'data-maskMoney', 'readonly']) !!}
									</div>
									@if ($errors->has('valor_credito'))
										<span class="help-block">{{ $errors->first('valor_credito') }}</span>
									@endif
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
										case 'BORRADOR':
											?>
											<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
											<?php
											break;
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
						<div class="row">
							<div class="col-md-12">
								<a class="btn btn-primary" data-toggle="modal" data-target="#agregarCuota">Agregar cuota</a>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12">
								<h4>{{ $cuotas->count() }} cuotas extraordinarias</h4>
							</div>
						</div>
						@if ($cuotas->count())
							<br>
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th class="text-center">Número cuotas</th>
												<th class="text-center">Valor cuota</th>
												<th>Forma pago</th>
												<th>Periodicidad</th>
												<th>Fecha primera cuota</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach ($cuotas as $cuota)
												<tr>
													<td class="text-right">{{ $cuota->numero_cuotas }}</td>
													<td class="text-right">${{ number_format($cuota->valor_cuota) }}</td>
													<td>{{ $cuota->forma_pago }}</td>
													<td>{{ $cuota->periodicidad }}</td>
													<td>{{ $cuota->inicio_descuento }}</td>
													<td>
														<a href="{{ route('solicitudCredito.delete.cuotasExtraordinarias', [$solicitud->id, $cuota->id]) }}" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif
					</div>

					<div class="card-footer">
						<?php
							switch($solicitud->estado_solicitud)
							{
								case 'BORRADOR':
									?>
									<a class="btn btn-danger pull-right" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
									<?php
									break;
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
<div class="modal fade" id="agregarCuota" tabindex="-1" role="dialog" aria-labelledby="titulo">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="titulo"><strong>Datos de cuota extraordinaria</strong></h4>
			</div>
			{!! Form::open(['route' => ['solicitudCredito.put.cuotasExtraordinarias', $solicitud], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('numero_cuotas')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('numero_cuotas'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Número cuotas
							</label>
							{!! Form::text('numero_cuotas', null, ['class' => 'form-control text-right', 'autofocus']) !!}
							@if ($errors->has('numero_cuotas'))
								<span class="help-block">{{ $errors->first('numero_cuotas') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('valor_cuota')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valor_cuota'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Valor cuota
							</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('valor_cuota', null, ['class' => 'form-control text-right', 'autofocus', 'data-maskMoney']) !!}
							</div>
							@if ($errors->has('valor_cuota'))
								<span class="help-block">{{ $errors->first('valor_cuota') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('forma_pago')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('forma_pago'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Forma de pago
							</label>
							{!! Form::select('forma_pago', ["NOMINA" => "Nómina", "CAJA" => "Caja", "PRIMA" => "Prima"], null, ['class' => 'form-control']) !!}
							@if ($errors->has('forma_pago'))
								<span class="help-block">{{ $errors->first('forma_pago') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('periodicidad')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('periodicidad'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Periodicidad
							</label><br>
							{!! Form::select('periodicidad', $periodicidades, null, ['class' => 'form-control']) !!}
							@if ($errors->has('periodicidad'))
								<span class="help-block">{{ $errors->first('periodicidad') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('inicio_descuento')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('inicio_descuento'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha primera cuota
							</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
								{!! Form::select('inicio_descuento', $programaciones, null, ['class' => 'form-control pull-right']) !!}
							</div>
							@if ($errors->has('inicio_descuento'))
								<span class="help-block">{{ $errors->first('inicio_descuento') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! Form::submit('Agregar', ['class' => 'btn btn-success']) !!}
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(window).load(function(){
			$("input[name='valor_credito']").maskMoney('mask');
			@if ($errors->count())
				$('#agregarCuota').modal('toggle');
				$("input[name='valor_cuota']").maskMoney('mask');
			@endif
		});
	});
</script>
@endpush

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
					<h3 class="card-title">Cuotas extraordinarias</h3>
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
					<div class="row">
						<div class="col-md-12">
							<a href="#" class="btn btn-outline-primary" data-toggle="modal" data-target="#agregarCuota">Agregar cuota</a>
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
								<table class="table table-striped table-hover">
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
													<a href="{{ route('solicitudCredito.delete.cuotasExtraordinarias', [$solicitud->id, $cuota->id]) }}" class="btn btn-outline-danger btn-sm"><i class="far fa-trash-alt"></i></a>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@endif
				</div>

				<div class="card-footer text-right">
					<?php
						switch($solicitud->estado_solicitud) {
							case 'BORRADOR':
								?>
								<a class="btn btn-outline-danger" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;
							case 'RADICADO':
								?>
								<a class="btn btn-outline-danger" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;
							case 'APROBADO':
								?>
								<a class="btn btn-outline-danger" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
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
<div class="modal fade" id="agregarCuota" tabindex="-1" role="dialog" aria-labelledby="titulo">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="titulo">Datos de cuota extraordinaria</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			{!! Form::open(['route' => ['solicitudCredito.put.cuotasExtraordinarias', $solicitud], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							@php
								$valid = $errors->has('numero_cuotas') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Número cuotas</label>
							{!! Form::text('numero_cuotas', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número cuotas']) !!}
							@if ($errors->has('numero_cuotas'))
								<div class="invalid-feedback">{{ $errors->first('numero_cuotas') }}</div>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							@php
								$valid = $errors->has('valor_cuota') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Valor cuota</label>
							<div class="input-group">
								<div class="input-group-prepend"><span class="input-group-text">$</span></div>
								{!! Form::text('valor_cuota', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Valor cuota', 'data-maskMoney']) !!}
								@if ($errors->has('valor_cuota'))
									<div class="invalid-feedback">{{ $errors->first('valor_cuota') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							@php
								$valid = $errors->has('forma_pago') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Forma de pago</label>
							{!! Form::select('forma_pago', ["NOMINA" => "Nómina", "CAJA" => "Caja", "PRIMA" => "Prima"], null, ['class' => [$valid, 'form-control']]) !!}
							@if ($errors->has('forma_pago'))
								<div class="invalid-feedback">{{ $errors->first('forma_pago') }}</div>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							@php
								$valid = $errors->has('periodicidad') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Periodicidad</label>
							{!! Form::select('periodicidad', $periodicidades, null, ['class' => [$valid, 'form-control']]) !!}
							@if ($errors->has('periodicidad'))
								<div class="invalid-feedback">{{ $errors->first('periodicidad') }}</div>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group">
							@php
								$valid = $errors->has('inicio_descuento') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Fecha primera cuota</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">
										<i class="fa fa-calendar"></i>
									</span>
								</div>
								{!! Form::select('inicio_descuento', $programaciones, null, ['class' => [$valid, 'form-control']]) !!}
								@if ($errors->has('inicio_descuento'))
									<div class="invalid-feedback">{{ $errors->first('inicio_descuento') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success']) !!}
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
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

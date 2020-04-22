@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Socios
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Socios</li>
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
		@php
			$fecha = Request::has('fecha') ? Request::get('fecha') : date('d/m/Y');
			$fecha = empty($fecha) ? date('d/m/Y') : $fecha;
		@endphp
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">
						Consulta
						@if ($socio)
							<a class="btn btn-sm btn-outline-primary float-right" href="{{ route('reportesReporte', 6) }}?numeroIdentificacion={{ $socio->tercero->numero_identificacion }}&fechaConsulta={{ implode('/', array_reverse(explode('/', $fecha))) }}" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
						@endif
					</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('socio', 'fecha'), ['url' => 'socio/consulta', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Seleccione socio</label>
								{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha consulta</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha', $fecha, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha'))
										<div class="invalid-feedback">{{ $errors->first('fecha') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<div class="input-group">
									<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
								</div>
							</div>
						</div>
					</div>
					{!! Form::close() !!}

					@if($socio)
						<br>
						<h4>Datos básicos</h4>
						<br>
						<div class="row">
							<div class="col-md-9">
								<div class="row">
									<div class="col-md-7">
										<dl>
											<dt>Nombre</dt>
											<dd>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</dd>
										</dl>
									</div>

									<div class="col-md-5">
										<dl>
											<dt>Pagaduría</dt>
											<dd>{{ empty($socio->pagaduria) ? '' : $socio->pagaduria->nombre }}</dd>
										</dl>
									</div>
								</div>

								<?php
									$antiguedad = 'No aplica';
									if($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD')
									{
										$antiguedad = $socio->fecha_antiguedad != null? $socio->fecha_antiguedad->diffForHumans() : 'Sin antigüedad';
									}
								?>
								<div class="row">
									<div class="col-md-7">
										<dl>
											<dt>Antigüedad</dt>
											<dd>{{ $antiguedad }}</dd>
										</dl>
									</div>
									<div class="col-md-5">
										<dl>
											<dt>Fecha nacimiento</dt>
											<dd>{{ empty($socio->tercero->fecha_nacimiento) ? '' : $socio->tercero->fecha_nacimiento }}</dd>
										</dl>
									</div>
								</div>

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
								<div class="row">
									<div class="col-md-7">
										<dl>
											<dt>Fecha afiliación</dt>
											<dd>{{ $socio->fecha_afiliacion }}</dd>
										</dl>
									</div>

									<div class="col-md-5">
										<dl>
											<dt>Estado</dt>
											<dd><span class="badge badge-pill bg-{{ $label }}">{{ $socio->estado }}</span></dd>
										</dl>
									</div>
								</div>

								<?php
									$contacto = $socio->tercero->getContacto();
									if($contacto) {
										$mailLink = "<a href='mailto:$contacto->email' class='link'>$contacto->email</a>";
									}
								?>
								<div class="row">
									<div class="col-md-7">
										<dl>
											<dt>Email</dt>
											<dd>{!! empty($contacto) ? 'Sin información' : $mailLink !!}</dd>
										</dl>
									</div>

									<div class="col-md-5">
										<dl>
											<dt>Ingreso empresa</dt>
											<dd>{{ $socio->fecha_ingreso }}</dd>
										</dl>
									</div>
								</div>

								<div class="row">
									<div class="col-md-7">
										<dl>
											<dt>Teléfono</dt>
											<dd>{{ empty($contacto) ? 'Sin información' : ($contacto->movil ?: $contacto->telefono) }}</dd>
										</dl>
									</div>

									<div class="col-md-5">
										<dl>
											<dt>Sueldo</dt>
											<dd>${{ number_format($socio->sueldo_mes) }}</dd>
										</dl>
									</div>
								</div>

								<div class="row">
									<?php
										$label = "bg-";
										$porcentaje = $socio->endeudamiento();
										if($porcentaje <= $porcentajeMaximoEndeudamientoPermitido) {
											$label .= 'green';
										}
										else {
											$label .= 'red';
										}
									?>
									<div class="col-md-7">
										<dl>
											<dt>Endeudamiento</dt>
											<dd><span class="badge badge-pill {{ $label }}">{{ number_format($porcentaje, 2) }}%</span></dd>
										</dl>
									</div>

									@if ($socio->estado == 'RETIRO' || $socio->estado == 'LIQUIDADO' )
										<div class="col-md-5">
											<dl>
												<dt>Fecha retiro</dt>
												<dd>{{ $socio->fecha_retiro }}</dd>
											</dl>
										</div>
									@endif
								</div>

								<br>
								<div class="row">
									<div class="col-md-6">
										@php
											$saldo = $socio->tercero->cupoDisponible($fecha);
										@endphp
										<h4 class="text-{{ $saldo <= 0 ? 'danger' : 'primary' }}"><strong>Cupo disponible: ${{ number_format($saldo) }}</strong></h4>
									</div>
									<div class="col-md-6">
										<div class="row">
											<div class="col-md-6"><strong>Último periodo aplicado:</strong></div>
											<div class="col-md-6">
												<?php
													if(!is_null($recaudoAplicado)) {
														?>
														<span class="badge badge-pill badge-success">{{ $recaudoAplicado->numero_periodo }}</span> {{ $recaudoAplicado->fecha_recaudo }}
														<?php
													}
												?>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="widget-user-image">
									<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/asociados/' . $socio->obtenerAvatar()) }}" alt="{{ $socio->nombre_corto }}" />
								</div>
							</div>
						</div>
					@endif
				</div>
			</div>
		</div>
	</section>

	@if($socio)
	<section class="content">
		<div class="container-fluid">
			<div class="row d-flex">

				<div class="col-4">
					<a href="{{ route('socio.consulta.ahorros.lista', $socio->id) }}?fecha={{ $fecha }}">
						<div class="info-box bg-success">
							<span class="info-box-icon"><i class="fas fa-dollar-sign"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Ahorros</span>
								<span class="info-box-number">${{ number_format($ahorros->saldo) }}</span>
								<div class="progress">
									<div class="progress-bar" style="width: {{ $ahorros->variacionAhorro }}%"></div>
								</div>
								<span class="progress-description">
									Incrementó {{ $ahorros->variacionAhorro }}% en 30 Días
								</span>
							</div>
						</div>
					</a>
				</div>

				<div class="col-4">
					<a href="{{ route('socio.consulta.creditos.lista', $socio->id) }}?fecha={{ $fecha }}">
						<div class="info-box bg-warning">
							<span class="info-box-icon"><i class="fas fa-money-bill"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Créditos</span>
								<span class="info-box-number">${{ number_format($creditos->saldo) }}</span>
								<div class="progress">
									<div class="progress-bar" style="width: {{ $creditos->porcentajePago }}%"></div>
								</div>
								<span class="progress-description">
									Pago {{ $creditos->porcentajePago }}% del total de créditos
								</span>
							</div>
						</div>
					</a>
				</div>

				<div class="col-4">
					<a href="{{ route('socio.consulta.recaudos.lista', $socio->id) }}?fecha={{ $fecha }}">
						<div class="info-box bg-primary">
							<span class="info-box-icon"><i class="fa fa-calendar"></i></span>
							<div class="info-box-content">
								<span class="info-box-text">Recaudos nómina</span>
								<span class="info-box-number">${{ number_format($recaudos->aplicado) }}</span>
								<div style="height: 12px;">&nbsp;</div>
								<span class="progress-description">
									Total aplicado en {{ $recaudoAplicado->fecha_recaudo ?? '00/00/0000' }}
								</span>
							</div>
						</div>
					</a>
				</div>

				<div class="col-4">
					<div class="info-box bg-default">
						<span class="info-box-icon"><i class="fas fa-hammer"></i></span>
						<div class="info-box-content">
							<span class="info-box-text">Herramientas</span>
							<span class="info-box-number"><a href="{{ route('socio.consulta.documentacion', $socio->id) }}?fecha={{ $fecha }}" class="link">Documentación</a></span>
							<span class="info-box-number"><a href="{{ route('socio.consulta.simulador', $socio->id) }}?fecha={{ $fecha }}" class="link">Simulador</a></span>
							<div style="height: 12px;">&nbsp;</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</section>
	@endif
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.profile-user-img {
		width: 250px;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='socio']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
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

		@if(Request::has('socio') && !empty(Request::get('socio')))
			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ Request::get('socio') }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio']"));
					$("select[name='socio']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

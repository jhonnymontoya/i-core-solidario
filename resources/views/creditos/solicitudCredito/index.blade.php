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
				@if (Session::has('codigoComprobante'))
					<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ Session::get('codigoComprobante') }}&numeroComprobante={{ Session::get('numeroComprobante') }}" title="Imprimir comprobante" target="_blank">
						{{ Session::get('message') }}
					</a>
					<i class="fas fa-external-link-alt"></i>
				@else
					{{ Session::get('message') }}
				@endif
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('solicitudCredito/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $solicitudes->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Solicitudes de crédito</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'inicio', 'fin', 'modalidad', 'canal', 'estado'), ['url' => '/solicitudCredito', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-3">
							<div class="input-daterange input-group" id="fecha">
								{!! Form::text('inicio', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off']); !!}
								<span class="input-group-addon">a</span>
								{!! Form::text('fin', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off']); !!}
							</div>
						</div>
						<div class="col-3">
							{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Modalidad', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col">
							{!! Form::select('canal', $canales, null, ['class' => 'form-control select2', 'placeholder' => 'Canal', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col">
							{!! Form::select('estado', $estados, null, ['class' => 'form-control select2', 'placeholder' => 'Estado', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-1">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
					<br>
					@if(!$solicitudes->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron solicitudes de crédito <a href="{{ url('solicitudCredito/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Cliente</th>
										<th>Modalidad</th>
										<th>Radicado</th>
										<th>Obligación</th>
										<th>Fecha</th>
										<th>Valor</th>
										<th>Tasa M.V.</th>
										<th>Canal</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($solicitudes as $solicitud)
										<tr>
											<?php
												$link = "";
												switch($solicitud->estado_solicitud) {
													case 'BORRADOR':
														$link = route('solicitudCreditoEdit', $solicitud);
														break;
													case 'RADICADO':
														$link = route('solicitudCreditoAprobar', $solicitud);
														break;
													case 'APROBADO':
														$link = route('solicitudCreditoDesembolsar', $solicitud);
														break;
													case 'DESEMBOLSADO':
													case 'SALDADO':
														$socio = optional($solicitud->tercero->socio)->id;
														if($socio) {
															$link = route('socioConsultaCreditos', [$solicitud, 'fecha=' . date("d/m/Y"), 'socio=' . $socio]);
														}
														break;
												}
												$nombre = $solicitud->tercero->tipoIdentificacion->codigo . ' ' . $solicitud->tercero->numero_identificacion . ' - ' . $solicitud->tercero->nombre_corto;
											?>
											<td><a href="{{ $link }}">{{ $nombre }}</a></td>
											<td>{{ $solicitud->modalidadCredito->codigo }} - {{ $solicitud->modalidadCredito->nombre }}</td>
											<td>{{ $solicitud->id }}</td>
											<td>{{ empty($solicitud->numero_obligacion) ? '-' : $solicitud->numero_obligacion }}</td>
											<td>
												<?php
													switch($solicitud->estado_solicitud) {
														case 'BORRADOR':
														case 'RADICADO':
														case 'RECHAZADO':
														case 'ANULADO':
															echo $solicitud->fecha_solicitud;
															break;
														case 'APROBADO':
															echo $solicitud->fecha_aprobacion;
															break;
														case 'DESEMBOLSADO':
															echo $solicitud->fecha_desembolso;
															break;
														case 'SALDADO':
															echo $solicitud->fecha_cancelacion;
															break;
														default:
															echo $solicitud->fecha_solicitud;
															break;
													}
												?>
											</td>
											<td class="text-right">${{ number_format($solicitud->valor_credito) }}</td>
											<td>{{ number_format($solicitud->tasa, 2) }}%</td>
											<td>{{ Str::title($solicitud->canal) }}</td>
											<td>
												<?php
													$label = 'secondary';
													switch($solicitud->estado_solicitud) {
														case 'BORRADOR':
														case 'RADICADO':
															$label = 'secondary';
															break;
														case 'RECHAZADO':
														case 'ANULADO':
															$label = 'danger';
															break;
														case 'APROBADO':
															$label = 'info';
															break;
														case 'DESEMBOLSADO':
															$label = 'primary';
															break;
														case 'SALDADO':
															$label = 'success';
															break;
														default:
															$label = 'secondary';
															break;
													}
												?>
												<span class="badge badge-pill badge-{{ $label }}">{{ $solicitud->estado_solicitud }}</span>
											</td>
											<td>
												<?php
													switch($solicitud->estado_solicitud) {
														case 'BORRADOR':
															?>
															<a class="btn btn-outline-info btn-sm" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Editar"><i class="fa fa-edit"></i></a>
															<a class="btn btn-outline-warning btn-sm" href="{{ route('solicitudCreditoAnular', $solicitud) }}" title="Anular"><i class="far fa-times-circle"></i></a>
															<?php
															break;
														case 'RADICADO':
															?>
															<a class="btn btn-outline-success btn-sm" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Aprobar"><i class="far fa-thumbs-up"></i></a>
															<a class="btn btn-outline-danger btn-sm" href="{{ route('solicitudCreditoRechazar', $solicitud) }}" title="Rechazar"><i class="far fa-thumbs-down"></i></a>
															<a class="btn btn-outline-warning btn-sm" href="{{ route('solicitudCreditoAnular', $solicitud) }}" title="Anular"><i class="far fa-times-circle"></i></a>
															<a class="btn btn-outline-secondary btn-sm" href="{{ route('reportesReporte', 8) }}?numeroRadicado={{ $solicitud->id }}" title="Estudio" target="_blank"><i class="fa fa-print"></i></a>
															<?php
															break;
														case 'APROBADO':
															?>
															<a class="btn btn-outline-success btn-sm" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Desembolsar"><i class="fas fa-money-bill"></i></a>
															<a class="btn btn-outline-warning btn-sm" href="{{ route('solicitudCreditoAnular', $solicitud) }}" title="Anular"><i class="far fa-times-circle"></i></a>
															<a class="btn btn-outline-secondary btn-sm" href="{{ route('reportesReporte', 8) }}?numeroRadicado={{ $solicitud->id }}" title="Estudio" target="_blank"><i class="fa fa-print"></i></a>
															<?php
															break;

														default:
															break;
													}
												?>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $solicitudes->appends(Request::only('name', 'inicio', 'fin', 'modalidad', 'canal', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $solicitudes->total()?'primary':'danger' }}">
						{{ $solicitudes->total() }}
					</span>&nbsp;elementos.
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
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('solicitudCredito/create') }}");
		$('#fecha').datepicker({
			format: "dd/mm/yyyy",
			weekStart: 0,
			todayBtn: "linked",
			clearBtn: true,
			language: "es",
			multidate: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true,
			todayHighlight: true
		});
		$('.select2').select2();
	});
</script>
@endpush

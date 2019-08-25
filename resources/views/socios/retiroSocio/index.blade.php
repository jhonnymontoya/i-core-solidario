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
		@if ($errors && $errors->has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ $errors->first('error') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				<a href="{{ url('retiroSocio/create') }}" class="btn btn-outline-primary">Ingresar solicitud</a>
				<a href="{{ url('retiroSocio/preliquidacion') }}" class="btn btn-outline-warning">Liquidación</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $solicitudesRetiros->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Retiros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'estado'), ['url' => '/retiroSocio', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-6 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-4 col-sm-12">
							{!! Form::select('estado', $estados, null, ['class' => 'form-control select2', 'placeholder' => 'Estado', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$solicitudesRetiros->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron registros de retiros <a href="{{ url('retiroSocio/create') }}" class="btn btn-outline-primary btn-sm">Ingresar solicitud</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Tipo</th>
										<th>Causal</th>
										<th>Fecha solicitud</th>
										<th>Fecha liquidación</th>
										<th>Etapa</th>
										<th></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
									foreach($solicitudesRetiros as $solicitud) {
										$tercero = $solicitud->socio->tercero;
										$nombre = "%s %s %s";
										$fecha = "%s (%s)";
										$fechaLiquidacion = "%s (%s)";
										$nombre = sprintf($nombre, $tercero->tipoIdentificacion->codigo, number_format($tercero->numero_identificacion), $tercero->nombre_corto);
										$fecha = sprintf($fecha, $solicitud->fecha_solicitud_retiro, $solicitud->fecha_solicitud_retiro->diffForHumans());
										if(!is_null($solicitud->fecha_liquidacion)) {
											$fechaLiquidacion = sprintf($fechaLiquidacion, $solicitud->fecha_liquidacion, $solicitud->fecha_liquidacion->diffForHumans());
										}
										else {
											$fechaLiquidacion = "";
										}
									?>
										<tr>
											<td>{{ $nombre }}</td>
											<td>{{ $solicitud->causaRetiro->tipo_causa_retiro }}</td>
											<td>{{ $solicitud->causaRetiro->nombre }}</td>
											<td>{{ $solicitud->fecha_solicitud_retiro }}</td>
											<td>
												{{ empty($solicitud->fecha_liquidacion) ? '-' : $solicitud->fecha_liquidacion }}
											</td>
											<td>
												<?php
													$estado = '';
													switch ($solicitud->socio->estado) {
														case 'ACTIVO':
															$estado = 'badge-success';
															break;
														case 'RETIRO':
															$estado = 'badge-warning';
															break;

														case 'LIQUIDADO':
															$estado = 'badge-danger';
															break;
														
														default:
															$estado = 'badge-warning';
															break;
													}
												?>
												<span class="badge badge-pill {{ $estado }}">{{ $solicitud->socio->estado }}</span>
											</td>
											<td>
												@if($solicitud->socio->estado != 'LIQUIDADO' && $solicitud->socio->estado == 'RETIRO')
													<a href="{{ url('retiroSocio/preliquidacion') }}?preliquidar=1&socio_id={{ $solicitud->socio->id }}&fechaMovimiento={{ $solicitud->fecha_solicitud_retiro }}&fechaSaldo={{ $solicitud->fecha_solicitud_retiro }}" class="btn btn-outline-danger btn-sm" title="Liquidar asociado"><i class="fa fa-user-times"></i></a>
													<a href="#" data-toggle="modal" data-target="#mAnular" data-nombre="{{ $nombre }}" data-fecha="{{ $fecha }}" data-id="{{ $solicitud->id }}" class="btn btn-outline-secondary btn-sm" title="Anular retiro"><i class="fa fa-close"></i></a>
												@endif
												@if($solicitud->movimiento)
													<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ $solicitud->movimiento->tipoComprobante->codigo }}&numeroComprobante={{ $solicitud->movimiento->numero_comprobante }}" class="btn btn-outline-secondary btn-sm" title="Imprimir comprobante" target="_blank">
														<i class="fa fa-print"></i>
													</a>
													<a href="#" data-toggle="modal" data-target="#mAnularLiquidacion" data-nombre="{{ $nombre }}" data-fecha="{{ $fechaLiquidacion }}" data-id="{{ $solicitud->id }}" class="btn btn-outline-secondary btn-sm" title="Anular liquidación"><i class="fa fa-close"></i></a>
												@endif
											</td>
										</tr>
									<?php
									}
									?>
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $solicitudesRetiros->appends(Request::only('name', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $solicitudesRetiros->total()?'primary':'danger' }}">
						{{ $solicitudesRetiros->total() }}
					</span>&nbsp;elementos.
				</div>
			</div>
		</div>
	</section>
</div>


<div class="modal fade" id="mAnular" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["route" => ["retiroSocio.anular", ":id"], "method" => "delete", "id" => "frmManular"]) !!}
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="mLabel">Anular retiro</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-warning alert-dismissible">
					<h4><i class="icon fa fa-warning"></i> Alerta</h4>
					¿Desea anular el retiro?
				</div>
				<div class="row">
					<div class="col-md-12">
						<dl class="dl-horizontal">
							<dt>Nombre</dt>
							<dd id="mAnularNombre"></dd>

							<dt>Fecha retiro</dt>
							<dd id="mAnularFechaRetiro"></dd>
						</dl>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Anular", ["class" => "btn btn-outline-success"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>

<div class="modal fade" id="mAnularLiquidacion" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["route" => ["retiroSocio.anular", ":id"], "method" => "put", "id" => "frmManularLiquidacion"]) !!}
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="mLabel">Anular liquidación</h4>
			</div>
			<div class="modal-body">
				<div class="alert alert-warning alert-dismissible">
					<h4><i class="icon fa fa-warning"></i> Alerta</h4>
					Está a punto de anular la liquidación y el asociado pasará a estado 'RETIRO'
				</div>
				<div class="row">
					<div class="col-md-12">
						<dl class="dl-horizontal">
							<dt>Nombre</dt>
							<dd id="mNombre"></dd>

							<dt>Fecha liquidación</dt>
							<dd id="mFechaLiquidacion"></dd>
						</dl>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Anular", ["class" => "btn btn-outline-success"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
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

		$('#mAnular').on('show.bs.modal', function (event) {
			var data = $(event.relatedTarget);
			var modal = $(this);
			var id = data.data("id");
			var nombre = data.data("nombre")
			var fecha = data.data("fecha")
			var ruta = "{{ route('retiroSocio.anular', ':id') }}";


			modal.find("#mAnularNombre").text(nombre);
			modal.find("#mAnularFechaRetiro").text(fecha);

			frm = modal.find("#frmManular");
			action = ruta.replace(":id", id);
			frm.attr("action", action);
		});

		$('#mAnularLiquidacion').on('show.bs.modal', function (event) {
			var data = $(event.relatedTarget);
			var modal = $(this);
			var id = data.data("id");
			var nombre = data.data("nombre")
			var fecha = data.data("fecha")
			var ruta = "{{ route('retiroSocio.anularLiquidacion', ':id') }}";


			modal.find("#mNombre").text(nombre);
			modal.find("#mFechaLiquidacion").text(fecha);

			frm = modal.find("#frmManularLiquidacion");
			action = ruta.replace(":id", id);
			frm.attr("action", action);
		});
	});
</script>
@endpush
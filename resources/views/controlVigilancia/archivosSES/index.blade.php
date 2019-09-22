@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Archivos SES
						<small>Control y Vigilancia</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Control y Vigilancia</a></li>
						<li class="breadcrumb-item active">Archivos SES</li>
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
		<div class="container-fluid">
			<div class="card card-{{ false?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Archivos SES</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('fecha_reporte'), ['url' => 'archivosSES', 'method' => 'GET', 'role' => 'search']) !!}
						<div class="col-md-5">
							<div class="form-group {{ ($errors->has('fecha_reporte')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('fecha_reporte'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha de reporte
								</label>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									@php
										$fechaReporte = date('Y/m');
										if (Request::has('fecha_reporte') && !empty(Request::get('fecha_reporte'))) {
											$fechaReporte = Request::get('fecha_reporte');
										}
									@endphp
									{!! Form::text('fecha_reporte', $fechaReporte, ['class' => 'form-control', 'placeholder' => 'yyyy/mm' ]) !!}
								</div>
								@if ($errors->has('fecha_reporte'))
									<span class="help-block">{{ $errors->first('fecha_reporte') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<label class="control-label">&nbsp;</label>
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					
					@if ($carteraCerrada)
						<div class="row">
							<div class="col-md-12">
								<h3>Fecha de reporte: {{ $fecha->format("Y/m") }}</h3>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h4>Reportes disponibles</h4>
								@php
									$query = url('archivosSES/descargar') .
									"?fecha_reporte=%s&reporte=";
									$query = sprintf($query, $fecha->format("Y/m"));
									$query .= "%s";
								@endphp
								<table class="table table-striped">
									<thead>
										<tr>
											<th>Reporte</th>
											<th>Acción</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach ($reportes as $key => $value) {
												$href = sprintf($query, $key);
												?>
												<tr>
													<td>
														<a href="{{ $href }}" target="_blank">
															{{ $value }}
														</a>
													</td>
													<td>
														<a href="{{ $href }}"  target="_blank" class="btn btn-outline-success btn-sm">
															<i class="fa fa-download"></i> Descargar
														</a>
													</td>
												</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							</div>
						</div>
					@endif
				</div>
				<div class="card-footer">
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
		$("input[name='fecha_reporte']").datepicker( {
			format: "yyyy/mm",
			viewMode: "months",
			minViewMode: "months",
			autoclose: "true"
		});
	});
</script>
@endpush
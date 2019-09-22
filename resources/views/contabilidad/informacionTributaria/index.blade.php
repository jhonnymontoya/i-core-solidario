@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Información tributaria
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Información tributaria</li>
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
			<div class="card card-{{ $impuestos->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Impuestos</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'tipo', 'estado'), ['url' => 'informacionTributaria', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-6 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-5 col-sm-12">
							{!! Form::select('tipo', $tiposImpuestos, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}
					@if(!$impuestos->total())
						<br>
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron impuestos
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Tipo</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($impuestos as $impuesto)
										<tr>
											<td>{{ $impuesto->nombre }}</td>
											<td>{{ $impuesto->tipo }}</td>
											<?php
												$activo = $impuesto->esta_activo;
											?>
											<td class="text-right">
												@php
													$pt = "%s?impuesto=%s&fechaInicio=%s&fechaFinal=%s";
													$if = sprintf(
														$pt,
														route('reportesReporte', 19),
														$impuesto->id,
														\Carbon\Carbon::now()->subMonth()->startOfMonth()->format("Y/m/d"),
														\Carbon\Carbon::now()->subMonth()->endOfMonth()->format("Y/m/d")
													);
													$dt = sprintf(
														$pt,
														route('reportesReporte', 20),
														$impuesto->id,
														\Carbon\Carbon::now()->subMonth()->startOfMonth()->format("Y/m/d"),
														\Carbon\Carbon::now()->subMonth()->endOfMonth()->format("Y/m/d")
													);
												@endphp
												<a href="{{ $if }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Declaración">Declaración</a>
												<a href="{{ $dt }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Detalle">Detalle</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $impuestos->appends(Request::only('name', 'tipo', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $impuestos->total()?'primary':'danger' }}">
						{{ $impuestos->total() }}
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
	$(function() {
		$(".select2").select2();
	});
</script>
@endpush
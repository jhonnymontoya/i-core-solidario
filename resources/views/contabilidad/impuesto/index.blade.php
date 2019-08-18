@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Impuestos
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Impuestos</li>
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
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('impuesto/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-{{ $impuestos->total()?'primary':'danger' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Impuestos</h3>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only('name', 'tipo', 'estado'), ['url' => 'impuesto', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('tipo', $tiposImpuestos, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('estado', [true => "Activo", false => "Inactivo"], null, ['class' => 'form-control select2', 'placeholder' => 'Estado', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$impuestos->total())
					<br>
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron impuestos <a href="{{ url('impuesto/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
							</div>
						</div>
					</p>
				@else
					<br><br>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Tipo</th>
									<th>Número conceptos</th>
									<th>Estado</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($impuestos as $impuesto)
									<tr>
										<td>
											<a href="{{ route('impuesto.edit', $impuesto->id) }}" title="Editar">{{ $impuesto->nombre }}</a>
										</td>
										<td>{{ $impuesto->tipo }}</td>
										<?php
											$conceptos = $impuesto->conceptosImpuestos->count();
											$activo = $impuesto->esta_activo;
											$estado = $activo ? "Activo" : "Inactivo";
										?>
										<td>
											<span class="label label-{{ $conceptos ? "success" : "danger" }}">{{ $conceptos }}</span>
										</td>
										<td>
											<span class="label label-{{ $activo ? "success" : "danger" }}">{{ $estado }}</span>
										</td>
										<td>
											<a href="{{ route('impuesto.edit', $impuesto->id) }}" class="btn btn-xs btn-info" title="Editar"><i class="fa fa-edit"></i></a>
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
				<span class="label label-{{ $impuestos->total()?'primary':'danger' }}">
					{{ $impuestos->total() }}
				</span>&nbsp;elementos.
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
		$(window).formularioCrear("{{ url('impuesto/create') }}");
		$(".select2").select2();
	});
</script>
@endpush
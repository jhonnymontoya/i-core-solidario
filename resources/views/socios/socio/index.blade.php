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
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('socio/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Buscar socios</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::all(), ['url' => '/socio', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-4 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							{!! Form::select('pagaduria', $pagadurias, null, ['class' => 'form-control select2', 'placeholder' => 'Pagaduria']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('estado', ['ACTIVO' => 'Activo', 'NOVEDAD' => 'Novedad', 'RETIRO' => 'Retiro', 'LIQUIDADO' => 'Liquidado', 'PROCESO' => 'Proceso'], null, ['class' => 'form-control select2', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('calificacion', ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E', 'K' => 'K'], null, ['class' => 'form-control select2', 'placeholder' => 'Calificación']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}
					<br>
				</div>
			</div>
		</div>
		@if($terceros->count() > 0)
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $terceros->total() }} socios en total, mostrando {{ $terceros->count() }}</strong>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $terceros->appends(Request::all())->render() !!}
				</div>
			</div>
		@else
			<div class="row"><div class="col-md-12"><h4>No se encontraron socios</h4></div></div>
		@endif

		<div class="container-fluid">
			<div class="card card-solid card-outline">
				<div class="card-body pb-0">
					<div class="row d-flex align-items-stretch">
						@foreach($terceros as $socio)
							<?php
								$color = 'info';
								switch ($socio->socio->estado) {
									case 'ACTIVO':
										$color = 'success';
										break;
									case 'NOVEDAD':
										$color = 'warning';
										break;
									case 'RETIRO':
										$color = 'warning';
										break;
									case 'LIQUIDADO':
										$color = 'danger';
										break;
									case 'PROCESO':
										$color = 'info';
										break;
								}
							?>
							<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
								<div class="card card-{{ $color }} card-outline">
									<div class="card-body box-profile">
										<div class="text-center">
											<img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/asociados/' . (empty($socio->socio->avatar)?'avatar-160x160.png':$socio->socio->avatar) ) }}" alt="{{ $socio->nombre_corto }}" />
										</div>
										<h3 class="profile-username text-center">{{ $socio->primer_nombre . ' ' . $socio->segundo_nombre }}</h3>
										<p class="text-muted text-center">{{ $socio->primer_apellido . ' ' . $socio->segundo_apellido }}</p>
										<ul class="list-group list-group-unbordered mb-3">
											<li class="list-group-item">
												<b>Estado</b> <a class="float-right">{{ mb_convert_case($socio->socio->estado, MB_CASE_TITLE, "UTF-8") }}</a>
											</li>
											<li class="list-group-item">
												<b>Identificación</b> <a class="float-right">{{ $socio->identificacion }}</a>
											</li>
											<li class="list-group-item">
												<b>Pagaduría</b> <a class="float-right">{{ empty($socio->socio->pagaduria) ? '' : $socio->socio->pagaduria->nombre }}</a>
											</li>
											<li class="list-group-item">
												<?php
													$porcentaje = $socio->socio->endeudamiento();
													$badge = "badge-";
													if($porcentaje <= $porcentajeMaximoEndeudamientoPermitido) {
														$badge .= 'success';
													}
													else {
														$badge .= 'danger';
													}
												?>
												<b>Endeudamiento</b> <span class="badge badge-pill {{ $badge }} float-right">{{ number_format($porcentaje, 2) }}%</span>
											</li>
											<li class="list-group-item">
												<b>Cupo disponible</b> <a class="float-right">${{ number_format($socio->cupoDisponible()) }}</a>
											</li>
										</ul>
										<div class="row">
											<div class="col-sm-12 col-xs-12 text-center">
												<a href="{{ route('socioEdit', $socio->socio) }}" class="btn btn-outline-secondary" title="Editar">
													<i class="far fa-edit"></i>
												</a>
												<a href="{{ route('socioAfiliacion', $socio->socio) }}" class="btn btn-outline-secondary {{ ($socio->socio->estado == 'ACTIVO' || $socio->socio->estado == 'NOVEDAD') ? 'disabled' : '' }}" title="Afiliar">
													<i class="far fa-thumbs-up"></i>
												</a>
												<a href="{{ url('socio/consulta') }}?socio={{ $socio->socio->id }}&fecha={{ date('d/m/Y') }}" class="btn btn-outline-secondary" title="Consulta">
													<i class="fas fa-bullseye"></i>
												</a>
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
		@if($terceros->count() > 0)
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $terceros->appends(Request::all())->render() !!}
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $terceros->total() }} socios en total, mostrando {{ $terceros->count() }}</strong>
				</div>
			</div>
		@endif
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.card {
		min-width: 338px;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='name']").enfocar();
		$(".select2").select2();
		$(window).formularioCrear("{{ url('socio/create') }}");
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Usuario
						<small>Sistema</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Sistema</a></li>
						<li class="breadcrumb-item active">Usuario</li>
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
				<a href="{{ url('usuario/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-outline">
			<div class="card-header with-border">
				<h3 class="card-title">Buscar usuarios</h3>
			</div>
			<div class="card-body">
				{!! Form::model(Request::all(), ['url' => '/usuario', 'method' => 'GET', 'role' => 'search']) !!}
				<div class="row">
					<div class="col-md-4 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('entidad', $entidades, null, ['class' => 'form-control', 'placeholder' => 'Entidad']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('activo', ['1' => 'Sí', '0' => 'No'], null, ['class' => 'form-control', 'placeholder' => 'Activo']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('perfil', ['1' => 'Sí', '0' => 'No'], null, ['class' => 'form-control', 'placeholder' => 'Perfil completo']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
					</div>
				</div>
				{!! Form::close() !!}
				<br>
			</div>
		</div>
		@if($usuarios->count() > 0)
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $usuarios->total() }} usuarios en total, mostrando {{ $usuarios->count() }}</strong>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $usuarios->appends(Request::all())->render() !!}
				</div>
			</div>
		@else
			<div class="row"><div class="col-md-12"><h4>No se encontraron usuarios</h4></div></div>
		@endif
		<div class="row d-flex align-items-stretch">
			<?php
			foreach($usuarios as $usuario) {
				$color = $usuario->esta_activo ? 'success' : 'danger';
				?>
				<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
					<div class="card card-{{ $color }} card-outline">
						<div class="card-body box-profile">
							<div class="text-center">
								<img class="profile-user-img img-fluid img-circle" src="{{ asset('storage/avatars/' . (empty($usuario->avatar)?'avatar-160x160.png':$usuario->avatar) ) }}" alt="{{ $usuario->nombre_completo }}" />
							</div>
							<h3 class="profile-username text-center">{{ $usuario->primer_nombre . ' ' . $usuario->segundo_nombre }}</h3>
							<p class="text-muted text-center">{{ $usuario->primer_apellido . ' ' . $usuario->segundo_apellido }}</p>
							<ul class="list-group list-group-unbordered mb-3">
								<li class="list-group-item">
									<b>Entidades</b> <span class="float-right badge bg-{{ $usuario->perfiles->count()?'success':'danger' }}">{{ $usuario->perfiles->count() }}</span>
								</li>
								<li class="list-group-item">
									<a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a>
								</li>
								<li class="list-group-item">
									<strong>Perfil completo</strong>
									<span class="float-right badge bg-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}">{{ $usuario->porcentajePerfilCompleto() }}%</span>
								</li>
								<li class="list-group-item">
									<div class="progress progress-xs">
										<div class="progress-bar progress-bar-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}" style="width: {{ $usuario->porcentajePerfilCompleto() }}%"></div>
									</div>
								</li>
							</ul>
							<div class="row">
								<div class="col-sm-6 col-xs-12 text-center">
									<a href="{{ route('usuarioEdit', $usuario) }}" class="btn btn-outline-info btn-block">
										<i class="fa fa-edit"></i>&nbsp;Editar
									</a>
								</div>
								<div class="col-sm-6 col-xs-12 text-center">
									<a href="{{ url('usuario', $usuario) }}" class="btn btn-outline-info btn-block">
										<i class="fa fa-external-link"></i>&nbsp;Ver
									</a>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php
			}
			?>
		</div>
		@if($usuarios->count() > 0)
			<div class="row">
				<div class="col-md-12 text-center">
					{!! $usuarios->appends(Request::all())->render() !!}
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<strong>{{ $usuarios->total() }} usuarios en total, mostrando {{ $usuarios->count() }}</strong>
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
		$(window).formularioCrear("{{ url('usuario/create') }}");
	});
</script>
@endpush

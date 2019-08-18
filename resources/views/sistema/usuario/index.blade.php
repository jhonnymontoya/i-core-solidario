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
				<a href="{{ url('usuario/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-outline">
			<div class="card-header with-border">
				<h3 class="card-title">Buscar usuarios</h3>
				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::all(), ['url' => '/usuario', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
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
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
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
		<?php
			$contador = 0;
		?>
		@foreach($usuarios as $usuario)
			@if($contador % 3 == 0)
				@if($contador != 0)
					</div>
				@endif
				<div class="row">
			@endif
			<div class="col-sm-4">
				<div class="card card-widget widget-user card-outline">
					<div class="widget-user-header bg-{{ $usuario->esta_activo?'aqua':'red' }}-active">
						<div class="widget-user-username">
							{{ $usuario->nombre_corto }}
						</div>
						<div class="widget-user-desc">
							{{ $usuario->usuario }}
						</div>
					</div>
					<div class="widget-user-image">
						<img class="img-circle" src="{{ asset('storage/avatars/' . (empty($usuario->avatar)?'avatar-160x160.png':$usuario->avatar) ) }}" alt="{{ $usuario->nombre_completo }}" />
					</div>
					<div class="card-footer">
						<ul class="nav nav-stacked">
							<li>
								<a href="{{ route('usuarioShow', [$usuario->id, '#entidades']) }}">Entidades 
									<span class="pull-right badge bg-{{ $usuario->perfiles->count()?'green':'red' }}">{{ $usuario->perfiles->count() }}</span>
								</a>
							</li>
							<li>
								<a>En línea 
									<i class="fa fa-circle text-success pull-right"></i>
								</a>
							</li>
							<li>
								<a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}<i class="fa fa-envelope pull-right"></i></a>
							</li>
							<li>
								<a>
									Perfil completo
									<span class="pull-right badge bg-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}">{{ $usuario->porcentajePerfilCompleto() }}%</span>
									<br><br>
									<div class="progress progress-xs">
										<div class="progress-bar progress-bar-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}" style="width: {{ $usuario->porcentajePerfilCompleto() }}%"></div>
									</div>
								</a>
							</li>
						</ul>
						<div class="row">
							<div class="col-sm-6 col-xs-6 border-right text-center">
								<a href="{{ route('usuarioEdit', $usuario) }}" class="btn btn-info btn-block">
									<i class="fa fa-edit"></i>
									Editar
								</a>
							</div>
							<div class="col-sm-6 col-xs-6 border-right text-center">
								<a href="{{ url('usuario', $usuario) }}" class="btn btn-info btn-block">
									<i class="fa fa-external-link"></i>
									Ver
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
			<?php
				$contador += 1;
			?>
		@endforeach
		@if($contador > 0)
			</div>
		@endif
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
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('usuario/create') }}");
	});
</script>
@endpush

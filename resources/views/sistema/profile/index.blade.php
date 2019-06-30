@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<br>

	<div class="container-fluid">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
	</div>
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12">
				<a href="{{ url('profile/edit') }}" class="btn btn-default btn-sm pull-right" data-toggle="tooltip" data-placement="left" title="Editar perfil"><i class="glyphicon glyphicon-pencil"></i></a>
			</div>
		</div>
	</div>

	<section class="content">
		<div class="row">
			<div class="col-md-12 text-center">
				<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/avatars/' . (empty($usuario->avatar)?'avatar-160x160.png':$usuario->avatar)) }}" alt="Avatar" title="{{ $usuario->nombre_corto }}">
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-center">
				<h3 class="profile-username text-center" id="id_nombre_vista">{{ $usuario->nombre_completo }}</h3>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-center">
				{{ '@' . $usuario->usuario }} . Miembro desde {{ $usuario->created_at->toFormattedDateString() }} ({{ $usuario->created_at->diffForHumans() }})
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-center">
				<a href="mailto:{{ $usuario->email }}"><i class="fa fa-envelope-o"></i> {{ $usuario->email }}</a> . {{ $usuario->tipoIdentificacion->codigo }} {{ number_format($usuario->identificacion, 0) }} . Perfil completo al <span class="badge bg-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}">{{ $usuario->porcentajePerfilCompleto() }}%</span>
			</div>
		</div>

		<br>

		@if($usuario->perfiles->count() > 0)
			<div class="row">
				<div class="col-md-12">
					<strong>Entidades disponibles: {{ $usuario->perfiles->count() }}</strong>
				</div>
			</div>
		@else
			<div class="row"><div class="col-md-12"><h4>No se encontraron entidades</h4></div></div>
		@endif
		<br>

		<?php
			$contador = 0;
		?>
		@foreach($usuario->perfiles as $perfil)
			@if($contador % 2 == 0)
				@if($contador != 0)
					</div>
				@endif
				<div class="row">
			@endif
			<div class="col-md-6 col-sm-12 col-xs-12">
				<div class="info-box">					
					@if($perfil->entidad->categoriaImagenes->where('nombre', 'Logo Selección')->count())
						<span class="info-box-icon" style="line-height: 0;">
							<img src="{{ asset('storage/entidad/' . $perfil->entidad->categoriaImagenes->where('nombre', 'Logo Selección')->first()->pivot->nombre) }}" title="{{ $perfil->entidad->terceroEntidad->razon_social }}">
						</span>
					@else
						<span class="info-box-icon">
							img
						</span>
					@endif					
					<div class="info-box-content">
						<span class="info-box-number">{{ $perfil->entidad->terceroEntidad->razon_social }}</span>
						<span class="info-box-text">{{ $perfil->entidad->terceroEntidad->nit }}</span>
						<span class="info-box-text">
							{{ $perfil->nombre }}
							{!! Form::open(['url' => 'entidad/seleccion', 'method' => 'post']) !!}
							{!! Form::hidden('entidad', $perfil->entidad->id) !!}
							{!! Form::submit('Ir', ['class' => 'btn btn-primary btn-xs pull-right']) !!}
							{!! Form::close() !!}
						</span>
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
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.info-box-number{
		font-weight: 600;
		font-size: 14px;
	}
	.info-box-text{
		font-size: 12px;
	}
	.profile-user-img {
		border: none;
	}
	.profile-user-img {
		width: 250px;
	}
	@media(max-width: 992px){
		.info-box-number{
			font-weight: 500;
			font-size: 11px;
		}
		.info-box-text{
			font-size: 10px;
		}
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		var url = document.location.toString();
		if (url.match('#'))
		{
			$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		}
	})
</script>
@endpush

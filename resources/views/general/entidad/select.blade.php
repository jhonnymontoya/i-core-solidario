@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Seleccionar entidad
		</h1>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

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
@endpush
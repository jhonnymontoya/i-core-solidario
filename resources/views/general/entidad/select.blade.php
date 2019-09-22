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
		<div class="card card-solid">
			<div class="card-body pb-0">
				<div class="row d-flex align-items-stretch">
					@foreach($usuario->perfiles as $perfil)
						@php
							$entidad = $perfil->entidad;
							$tercero = $entidad->terceroEntidad;
						@endphp
						<div class="col-12 col-sm-6 col-md-4 d-flex align-items-stretch">
							<div class="card bg-light">
								<div class="card-header text-muted border-bottom-0">
									{{ $tercero->sigla }}
								</div>
								<div class="card-body pt-0">
									<div class="row">
										<div class="col-12 text-center">
											@if($perfil->entidad->categoriaImagenes->where('nombre', 'Logo Selección')->count())
												<img class="img-circle img-fluid" src="{{ asset('storage/entidad/' . $entidad->categoriaImagenes->where('nombre', 'Logo Selección')->first()->pivot->nombre) }}" title="{{ $tercero->razon_social }}">
											@else
												img
											@endif	
										</div>
									</div>
									<div class="row">
										<div class="col-12">
											<h2 class="lead"><b>{{ $tercero->razon_social }}</b></h2>
											<p class="text-muted text-sm"><b>Perfil: </b> {{ $perfil->nombre }}</p>
											<ul class="ml-4 mb-0 fa-ul text-muted">
												<li class="small"><span class="fa-li"><i class="fas fa-lg fa-address-card"></i></span> {{ $perfil->entidad->terceroEntidad->nit }}</li>
											</ul>
										</div>
									</div>
								</div>
								<div class="card-footer">
									<div class="text-center">
										{!! Form::open(['url' => 'entidad/seleccion', 'method' => 'post']) !!}
										{!! Form::hidden('entidad', $perfil->entidad->id) !!}
										<button type="submit" class="btn btn-block btn-outline-primary">
											<i class="fas fa-sign-in-alt"></i> Ir
										</button>
										{!! Form::close() !!}
									</div>
								</div>
							</div>
						</div>
					@endforeach
				</div>
			</div>
		</div>

		

	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.card {
		min-width: 337.66px;
	}
</style>
@endpush

@push('scripts')
@endpush
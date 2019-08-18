@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Seguros de cartera
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Seguros de cartera</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Editar seguro de cartera</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<h4>Editando las modalidades de crédito para el seguro de cartera <em>{{ $seguroCartera->codigo }} - {{ $seguroCartera->nombre }}</em></h4>
					</div>
				</div>
				<br>
				<strong>Modalidades</strong>
				@if ($modalidades->count())
					@foreach ($modalidades as $modalidad)
						@php
							$asociado = false;
							$seguroCarteraAsociado = null;
							if($modalidad->segurosCartera->count() > 0) {
								$asociado = true;
								$seguroCarteraAsociado = $modalidad->segurosCartera[0];
								if($seguroCarteraAsociado->id == $seguroCartera->id) {
									$seguroCarteraAsociado = null;
								}
							}
						@endphp
						<div class="row">
							<div class="col-md-3 col-md-offset-1"><p>{{ $modalidad->nombre }}</p></div>
							<div class="col-md-8">
								<a data-modalidad="{{ $modalidad->id }}" class="btn btn-xs asociar btn-{{ $asociado ? 'danger' : 'success' }}">{{ $asociado ? (empty($seguroCarteraAsociado) ? 'Desasociar' : 'Asociado en seguro ' . $seguroCarteraAsociado->codigo . ' - ' . $seguroCarteraAsociado->nombre . ' ¿Desasociar?') : 'Asociar' }}</a>
							</div>
						</div>
					@endforeach
				@else
					No existen mosalidades de crédito para asociar, <a class="btn btn-success btn-xs" href="{{ url('modalidadCredito/create') }}">Crear nueva modalidad de crédito</a>
				@endif
			</div>
			<div class="card-footer">
				<a href="{{ route('seguroCarteraEdit', $seguroCartera->id) }}" class="btn btn-danger pull-right">Volver</a>
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
		$(".asociar").click(function(e){
			$boton = $(this);
			$modalidad = $boton.data("modalidad");
			$url = "{{ url('seguroCartera') . "/" . $seguroCartera->id . "/" }}" + $modalidad;
			$.get({url: $url, dataType: 'json'}).done(function(data){
				if(data.asociado == true) {
					$boton.removeClass("btn-success");
					$boton.addClass("btn-danger");
					$boton.text("Desasociar");
				}
				else {
					$boton.removeClass("btn-danger");
					$boton.addClass("btn-success");
					$boton.text("Asociar");
				}
			});
		});
	});
</script>
@endpush

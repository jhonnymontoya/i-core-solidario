@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Editar entidad
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Entidad</li>
		</ol>
	</section>

	<section class="content">
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

		<div class="row">
			{!! Form::model($entidad, ['url' => ['entidad', $entidad, 'imagenes'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'id' => 'formularioImagenes']) !!}
			@foreach($categorias as $categoria)
			{{ Form::hidden('imagen' . $categoria->id, null) }}
			@endforeach
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('entidadEdit', $entidad->id) }}">Información básica</a></li>
						<li role="presentation" class="active"><a href="{{ route('entidadEditImagenes', $entidad->id) }}">Imágenes</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							@if($categorias->count())
								<?php
									$nuevaFila = false;
								?>
								@foreach($categorias as $categoria)
									<?php
										$nuevaFila = !$nuevaFila;
									?>
									@if($nuevaFila)
									<div class="row">
									@endif
										<div class="col-sm-6">
											<div class="panel panel-info">
												<div class="panel-heading">{{ $categoria->nombre }}</div>
												<div class="panel-body">
													{{ $categoria->descripcion }}
													<div class="row">
														<div class="col-sm-12 text-center">
															<div id="imagen{{ $categoria->id }}">
																<div id="image-cropper">
																	<div class="cropit-preview"></div>
																	<input type="file" class="cropit-image-input" />
																	<a class="select-image-btn btn btn-default btn-xs"><i class="fa fa-camera"></i></a>
																	<a class="rotate-ccw-btn btn btn-default btn-xs"><i class="fa fa-rotate-left"></i></a>
																	<a class="rotate-cw-btn btn btn-default btn-xs"><i class="fa fa-rotate-right"></i></a>
																	<a class="zoom-in-btn btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-in"></i></a>
																	<a class="zoom-out-btn btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-out"></i></a>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
									@if(!$nuevaFila)
									</div>
									@endif
								@endforeach
								@if(!$nuevaFila)
								</div>
								@endif
							@else
								<h4>No se encontrarón categorías de imágenes, ir a <a href="{{ url('categoriaImagen/create') }}" class="btn btn-primary btn-xs">Crear categorías de imágenes</a></h4>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('entidad') }}" class="btn btn-danger pull-right">Cancelar</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	input.cropit-image-input {
	  visibility: hidden;
	}

	.cropit-preview {
		margin-left: auto;
		margin-right: auto;
		background-color: #f8f8f8;
		background-size: cover;
		border: 1px solid #aaa;
		border-radius: 3px;
		margin-top: 7px;
	}
	.cropit-preview-image-container{
		background-size: cover;
	}

	@foreach($categorias as $categoria)
	#imagen{{ $categoria->id }} .cropit-preview {
		width: {{ $categoria->ancho }}px;
		height: {{ $categoria->alto }}px;
	}
	@endforeach

	.cropit-preview-image-container {
		cursor: move;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		@foreach($categorias as $categoria)
		$imageCropper{{ $categoria->id }} = $('#imagen{{ $categoria->id }} #image-cropper').cropit();
		@php
			$imagenCategoria = $categoria->entidades->where("id", $entidad->id)->first();
		@endphp
		@if(!empty($imagenCategoria))
		$imageCropper{{ $categoria->id }}.cropit('imageSrc', '{{ asset('storage/entidad/' . $imagenCategoria->pivot->nombre) }}');
		@endif
		$('#imagen{{ $categoria->id }} .select-image-btn').click(function(event) {
			event.preventDefault();
			$('#imagen{{ $categoria->id }} .cropit-image-input').click();
		});
		$('#imagen{{ $categoria->id }} .rotate-cw-btn').click(function() {
			$imageCropper{{ $categoria->id }}.cropit('rotateCW');
		});
		$('#imagen{{ $categoria->id }} .rotate-ccw-btn').click(function() {
			$imageCropper{{ $categoria->id }}.cropit('rotateCCW');
		});
		$('#imagen{{ $categoria->id }} .zoom-in-btn').click(function() {
			$imageCropper{{ $categoria->id }}.cropit('zoom', $imageCropper{{ $categoria->id }}.cropit('zoom') + .03);
		});
		$('#imagen{{ $categoria->id }} .zoom-out-btn').click(function() {
			$imageCropper{{ $categoria->id }}.cropit('zoom', $imageCropper{{ $categoria->id }}.cropit('zoom') - .03);
		});
		@endforeach

		$('#formularioImagenes').submit(function(){
			@foreach($categorias as $categoria)
			$("input[name='imagen{{ $categoria->id }}']").val($imageCropper{{ $categoria->id }}.cropit('export'));
			@endforeach
		});
	});
</script>
@endpush

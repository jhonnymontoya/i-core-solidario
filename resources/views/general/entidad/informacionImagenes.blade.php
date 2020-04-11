@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar entidad
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Editar entidad</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($entidad, ['url' => ['entidad', $entidad, 'imagenes'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'id' => 'formularioImagenes']) !!}
			@foreach($categorias as $categoria)
			{{ Form::hidden('imagen' . $categoria->id, null) }}
			@endforeach
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('entidadEdit', $entidad->id) }}">Información básica</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('entidadEditImagenes', $entidad->id) }}">Imágenes</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane active">



							<?php
								if($categorias->count()) {
									$nuevaFila = false;
									foreach($categorias as $categoria) {
										$nuevaFila = !$nuevaFila;
										if($nuevaFila) {
											?>
											<div class="row">
											<?php
										}
										?>
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
																	<div class="row">
																		<div class="col-12 text-center">
																			<a class="rotate-ccw-btn btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
																			<a class="rotate-cw-btn btn btn-outline-secondary btn-sm"><i class="fas fa-redo"></i></a>
																			<a class="select-image-btn btn btn-outline-secondary btn-sm"><i class="fa fa-camera"></i></a>
																			<a class="zoom-in-btn btn btn-outline-secondary btn-sm"><i class="fas fa-search-plus"></i></a>
																			<a class="zoom-out-btn btn btn-outline-secondary btn-sm"><i class="fas fa-search-minus"></i></a>
																		</div>
																	</div>
																</div>
															</div>
														</div>
													</div>
												</div>
											</div>
										</div>
										<?php
										if(!$nuevaFila) {
											?>
											</div>
											<?php
										}
									}
									if(!$nuevaFila) {
										?>
										<!--/div-->
										<?php
									}
								}
								else {
									?>
									<h4>No se encontrarón categorías de imágenes, ir a <a href="{{ url('categoriaImagen/create') }}" class="btn btn-outline-primary btn-sm">Crear categorías de imágenes</a></h4>
									<?php
								}
							?>






















































						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('entidad') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Editar socio</li>
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

		<div class="row">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'imagenes'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'files' => true, 'id' => 'formularioImagenes']) !!}
			{{ Form::hidden('imagen', null) }}
			{{ Form::hidden('firma', null) }}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-sm-12 text-center">
									<h4>Imagen</h4>
									<div id="imagen">
										<div id="image-cropper">
											<div class="cropit-preview"></div>
											<input type="file" class="cropit-image-input" />
											<a class="rotate-ccw-btn btn btn-default btn-sm"><i class="fa fa-rotate-left"></i></a>
											<a class="rotate-cw-btn btn btn-default btn-sm"><i class="fa fa-rotate-right"></i></a>
											<a class="zoom-in-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-in"></i></a>
											<a class="zoom-out-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-out"></i></a>
											<br>
											<a class="select-image-btn btn btn-primary">Seleccione una imagen</a>
										</div>
									</div>
								</div>
							</div>
							<br><br>
							<div class="row">
								<div class="col-sm-12 text-center">
									<h4>Firma</h4>
									<div id="firma">
										<div id="image-cropper">
											<div class="cropit-preview"></div>
											<input type="file" class="cropit-image-input" />
											<a class="rotate-ccw-btn btn btn-default btn-sm"><i class="fa fa-rotate-left"></i></a>
											<a class="rotate-cw-btn btn btn-default btn-sm"><i class="fa fa-rotate-right"></i></a>
											<a class="zoom-in-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-in"></i></a>
											<a class="zoom-out-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-out"></i></a>
											<br>
											<a class="select-image-btn btn btn-primary">Seleccione una imagen</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('socio') }}" class="btn btn-danger">Volver</a>
									<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} pull-right {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
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
	#imagen .cropit-preview {
		margin-left: auto;
		margin-right: auto;
		background-size: cover;
		width: 250px;
		height: 250px;
	}
	.select-image-btn{
		margin-top: 5px;
	}
	#imagen .cropit-preview-image-container{
		background-image: url('{{ asset('storage/avatars/avatar-160x160.png') }}');
		background-size: cover;
		border-radius: 50%;
	}

	#firma .cropit-preview {
		margin-left: auto;
		margin-right: auto;
		background-color: #f8f8f8;
		background-size: cover;
		border: 1px solid #aaa;
		border-radius: 3px;
		margin-top: 7px;
		width: 700px;
		height: 300px;
	}
	#firma .cropit-preview-image-container {
		cursor: move;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$imageCropper = $('#imagen #image-cropper').cropit();
		@if(!empty($socio->avatar))
		$imageCropper.cropit('imageSrc', '{{ asset('storage/asociados/' . $socio->avatar) }}');
		@endif
		$('#imagen  .select-image-btn').click(function(event) {
			event.preventDefault();
			$('#imagen  .cropit-image-input').click();
		});
		$('#imagen  .rotate-cw-btn').click(function() {
			$imageCropper.cropit('rotateCW');
		});
		$('#imagen  .rotate-ccw-btn').click(function() {
			$imageCropper.cropit('rotateCCW');
		});
		$('#imagen .zoom-in-btn').click(function() {
			$imageCropper.cropit('zoom', $imageCropper.cropit('zoom') + .03);
		});
		$('#imagen  .zoom-out-btn').click(function() {
			$imageCropper.cropit('zoom', $imageCropper.cropit('zoom') - .03);
		});

		$firmaCropper = $('#firma #image-cropper').cropit();
		@if(!empty($socio->firma))
		$firmaCropper.cropit('imageSrc', '{{ asset('storage/asociados/' . $socio->firma . '698x298.jpg') }}');
		@endif
		$('#firma .select-image-btn').click(function(event) {
			event.preventDefault();
			$('#firma .cropit-image-input').click();
		});
		$('#firma .rotate-cw-btn').click(function() {
			$firmaCropper.cropit('rotateCW');
		});
		$('#firma .rotate-ccw-btn').click(function() {
			$firmaCropper.cropit('rotateCCW');
		});
		$('#firma .zoom-in-btn').click(function() {
			$firmaCropper.cropit('zoom', $firmaCropper.cropit('zoom') + .03);
		});
		$('#firma .zoom-out-btn').click(function() {
			$firmaCropper.cropit('zoom', $firmaCropper.cropit('zoom') - .03);
		});

		$('#formularioImagenes').submit(function(){
			$("input[name='imagen']").val($imageCropper.cropit('export'));
			$("input[name='firma']").val($firmaCropper.cropit('export'));
		});
	});
</script>
@endpush

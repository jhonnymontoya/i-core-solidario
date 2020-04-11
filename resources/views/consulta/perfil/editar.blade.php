@extends('layouts.consulta')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content">

		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="container-fluid">
			<br>
			{!! Form::model($usuario, ['url' => 'consulta/perfil', 'method' => 'PUT', 'role' => 'form', 'id' => 'profile']) !!}
			{!! Form::hidden('avatar', null) !!}
			<div class="card">
				<div class="card-header with-border">
					<h3 class="card-title">Perfil</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-8 offset-md-2">
							<div id="image-cropper">
								<div class="col-12 text-center">
									<div class="cropit-preview"></div>
									<input type="file" class="cropit-image-input" />
								</div>
								<div class="col-12 text-center">
									<a class="rotate-ccw-btn btn btn-outline-secondary btn-sm"><i class="fas fa-undo"></i></a>
									<a class="rotate-cw-btn btn btn-outline-secondary btn-sm"><i class="fa fa-redo"></i></a>
									<a class="btn btn-outline-secondary btn-sm select-image-btn"><i class="fas fa-camera"></i></a>
									<a class="zoom-in-btn btn btn-outline-secondary btn-sm"><i class="fas fa-search-plus"></i></a>
									<a class="zoom-out-btn btn btn-outline-secondary btn-sm"><i class="fas fa-search-minus"></i></a>
								</div>
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-8 offset-md-2">
							<h5>Actualizar contraseña</h5>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 offset-md-2 text-center">
							<hr>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="form-group row">
								@php
									$valid = $errors->has('password') ? 'is-invalid' : '';
								@endphp
								<label class="col-3 control-label">Contraseña</label>
								<div class="col-9">
									{!! Form::password('password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Contraseña']) !!}
									@if ($errors->has('password'))
										<div class="invalid-feedback">{{ $errors->first('password') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="form-group row">
								@php
									$valid = $errors->has('confirmar_password') ? 'is-invalid' : '';
								@endphp
								<label class="col-3 control-label">Confirmar contraseña</label>
								<div class="col-9">
									{!! Form::password('confirmar_password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Confirmar contraseña']) !!}
									@if ($errors->has('confirmar_password'))
										<div class="invalid-feedback">{{ $errors->first('confirmar_password') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('consulta/perfil') }}" class="btn btn-outline-danger">Cancelar</a>
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
		background-size: cover;
		width: 250px;
		height: 250px;
	}
	.cropit-preview-image-container{
		background-image: url('{{ asset('storage/asociados/' . $socio->obtenerAvatar()) }}');
		background-size: cover;
		border-radius: 50%;
		border: 1px solid #adb5bd;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='identificacion']").enfocar();
		$imageCropper = $('#image-cropper').cropit();
		@if(!empty($usuario->avatar))
		$imageCropper.cropit('imageSrc', '{{ asset('storage/avatars/' . $socio->obtenerAvatar()) }}');
		@endif
		$('.select-image-btn').click(function(event) {
			event.preventDefault();
			$('.cropit-image-input').click();
		});
		$('.rotate-cw-btn').click(function() {
			$imageCropper.cropit('rotateCW');
		});
		$('.rotate-ccw-btn').click(function() {
			$imageCropper.cropit('rotateCCW');
		});
		$('.zoom-in-btn').click(function() {
			$imageCropper.cropit('zoom', $imageCropper.cropit('zoom') + .03);
		});
		$('.zoom-out-btn').click(function() {
			$imageCropper.cropit('zoom', $imageCropper.cropit('zoom') - .03);
		});

		$(".select2").select2();

		$("#profile").submit(function(){
			$("input[name='avatar']").val($imageCropper.cropit('export'));
		});

	});
</script>
@endpush

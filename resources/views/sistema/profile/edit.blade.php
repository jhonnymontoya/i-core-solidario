@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-12">
					<h1>
						Editar perfil
					</h1>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($usuario, ['url' => 'profile', 'method' => 'PUT', 'role' => 'form', 'id' => 'profile']) !!}
				{!! Form::hidden('avatar', null) !!}
				<div class="card-header">
					<h3 class="card-title">Editar perfil</h3>
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

					<div class="row">
						<div class="col-md-8 offset-md-2 text-center">
							<hr>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('tipo_identificacion_id') ? 'is-invalid' : '';
								@endphp
								<label class="col-3 control-label">
									Tipo de identificación
								</label>
								<div class="col-9">
									{!! Form::select('tipo_identificacion_id', $tipos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Tipo de identificación']) !!}
									@if ($errors->has('tipo_identificacion_id'))
										<div class="invalid-feedback">{{ $errors->first('tipo_identificacion_id') }}</div>
									@endif
								</div>							
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('identificacion') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Número de identificación
								</label>
								<div class="col-sm-9">
									{!! Form::text('identificacion', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Número de identificación', 'autocomplete' => 'off']) !!}
									@if ($errors->has('identificacion'))
										<div class="invalid-feedback">{{ $errors->first('identificacion') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('usuario') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Usuario
								</label>
								<div class="col-sm-9">
									{!! Form::text('usuario', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Usuario', 'readonly']) !!}
									@if ($errors->has('usuario'))
										<div class="invalid-feedback">{{ $errors->first('usuario') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('primer_nombre') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Primer nombre
								</label>
								<div class="col-sm-9">
									{!! Form::text('primer_nombre', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Primer nombre', 'autocomplete' => 'off']) !!}
									@if ($errors->has('primer_nombre'))
										<div class="invalid-feedback">{{ $errors->first('primer_nombre') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('segundo_nombre') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Segundo nombre
								</label>
								<div class="col-sm-9">
									{!! Form::text('segundo_nombre', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Segundo nombre', 'autocomplete' => 'off']) !!}
									@if ($errors->has('segundo_nombre'))
										<div class="invalid-feedback">{{ $errors->first('segundo_nombre') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('primer_apellido') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Primer apellido
								</label>
								<div class="col-sm-9">
									{!! Form::text('primer_apellido', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Primer apellido', 'autocomplete' => 'off']) !!}
									@if ($errors->has('primer_apellido'))
										<div class="invalid-feedback">{{ $errors->first('primer_apellido') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('segundo_apellido') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Segundo apellido
								</label>
								<div class="col-sm-9">
									{!! Form::text('segundo_apellido', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Segundo apellido', 'autocomplete' => 'off']) !!}
									@if ($errors->has('segundo_apellido'))
										<div class="invalid-feedback">{{ $errors->first('segundo_apellido') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('email') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Correo electrónico
								</label>
								<div class="col-sm-9">
									{!! Form::email('email', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Correo electrónico', 'autocomplete' => 'off']) !!}
									@if ($errors->has('email'))
										<div class="invalid-feedback">{{ $errors->first('email') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 offset-md-2 text-center">
							<hr>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('password') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Contraseña
								</label>
								<div class="col-sm-9">
									{!! Form::password('password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Contraseña']) !!}
									@if ($errors->has('password'))
										<div class="invalid-feedback">{{ $errors->first('password') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 offset-md-2 text-center">
							<div class="row form-group">
								@php
									$valid = $errors->has('confirmar_password') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-3 control-label">
									Confirmar contraseña
								</label>
								<div class="col-sm-9">
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
					<a href="{{ url('profile') }}" class="btn btn-outline-danger">Cancelar</a>
				</div>
				{!! Form::close() !!}
			</div>
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
		background-image: url('{{ asset('storage/avatars/avatar-160x160.png') }}');
		background-size: cover;
		border-radius: 50%;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='identificacion']").enfocar();
		$imageCropper = $('#image-cropper').cropit();
		@if(!empty($usuario->avatar))
		$imageCropper.cropit('imageSrc', '{{ asset('storage/avatars/' . $usuario->avatar) }}');
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

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
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($usuario, ['url' => 'consulta/perfil', 'method' => 'PUT', 'role' => 'form', 'id' => 'profile']) !!}
				{!! Form::hidden('avatar', null) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar perfil</h3>
				</div>
				<div class="card-body">

					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div id="image-cropper">
								<div class="cropit-preview"></div>
								<input type="file" class="cropit-image-input" />
								<a class="rotate-ccw-btn btn btn-default btn-xs"><i class="fa fa-rotate-left"></i></a>
								<a class="rotate-cw-btn btn btn-default btn-xs"><i class="fa fa-rotate-right"></i></a>
								<a class="zoom-in-btn btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-in"></i></a>
								<a class="zoom-out-btn btn btn-default btn-xs"><i class="glyphicon glyphicon-zoom-out"></i></a>
								<br>
								<a class="select-image-btn btn btn-primary">Seleccione una imagen</a>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<hr>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('tipoIdentifcacion')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('tipoIdentifcacion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo identificación
								</label>
								<div class="col-sm-9">
									{!! Form::text('tipoIdentifcacion', $tercero->tipoIdentificacion->nombre, ['class' => 'form-control', 'placeholder' => 'tipoIdentifcacion', 'readonly']) !!}
									@if ($errors->has('tipoIdentifcacion'))
										<span class="help-block">{{ $errors->first('tipoIdentifcacion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('numeroIdentifcacion')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('numeroIdentifcacion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Número identifcacion
								</label>
								<div class="col-sm-9">
									{!! Form::text('numeroIdentifcacion', $usuario->usuario, ['class' => 'form-control', 'placeholder' => 'Número Identifcacion', 'readonly']) !!}
									@if ($errors->has('numeroIdentifcacion'))
										<span class="help-block">{{ $errors->first('numeroIdentifcacion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('primerNombre')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('primerNombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Primer nombre
								</label>
								<div class="col-sm-9">
									{!! Form::text('primerNombre', $tercero->primer_nombre, ['class' => 'form-control', 'placeholder' => 'Primer nombre', 'readonly']) !!}
									@if ($errors->has('primerNombre'))
										<span class="help-block">{{ $errors->first('primerNombre') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('segundoNombre')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('segundoNombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Segundo nombre
								</label>
								<div class="col-sm-9">
									{!! Form::text('segundoNombre', $tercero->segundo_nombre, ['class' => 'form-control', 'placeholder' => 'Segundo nombre', 'readonly']) !!}
									@if ($errors->has('segundoNombre'))
										<span class="help-block">{{ $errors->first('segundoNombre') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('primerApellido')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('primerApellido'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Primer apellido
								</label>
								<div class="col-sm-9">
									{!! Form::text('primerApellido', $tercero->primer_apellido, ['class' => 'form-control', 'placeholder' => 'Primer apellido', 'readonly']) !!}
									@if ($errors->has('primerApellido'))
										<span class="help-block">{{ $errors->first('primerApellido') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('segundoApellido')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('segundoApellido'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Segundo apellido
								</label>
								<div class="col-sm-9">
									{!! Form::text('segundoApellido', $tercero->segundo_apellido, ['class' => 'form-control', 'placeholder' => 'Segundo apellido', 'readonly']) !!}
									@if ($errors->has('segundoApellido'))
										<span class="help-block">{{ $errors->first('segundoApellido') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-8 col-md-offset-2 text-center">
							<hr>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('password')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('password'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Contraseña
								</label>
								<div class="col-sm-9">
									{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña']) !!}
									@if ($errors->has('password'))
										<span class="help-block">{{ $errors->first('password') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row form-horizontal">
						<div class="col-md-8 col-md-offset-2 text-center">
							<div class="form-group {{ ($errors->has('confirmar_password')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('confirmar_password'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Confirmar contraseña
								</label>
								<div class="col-sm-9">
									{!! Form::password('confirmar_password', ['class' => 'form-control', 'placeholder' => 'Confirmar contraseña']) !!}
									@if ($errors->has('confirmar_password'))
										<span class="help-block">{{ $errors->first('confirmar_password') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
					<a href="{{ url('consulta') }}" class="btn btn-danger pull-right">Cancelar</a>
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
	.select-image-btn{
		margin-top: 5px;
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
		<?php
			$imagen = $socio->avatar;
			if(!empty($imagen)) {
				$imagen = sprintf("storage/asociados/%s", $imagen);
				?>
				$imageCropper.cropit('imageSrc', '{{ asset($imagen) }}');
				<?php
			}
		?>
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

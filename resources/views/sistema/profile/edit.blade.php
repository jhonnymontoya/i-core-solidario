@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Editar perfil
		</h1>
	</section>

	<section class="content">

		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="box box-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($usuario, ['url' => 'profile', 'method' => 'PUT', 'role' => 'form', 'id' => 'profile']) !!}
			{!! Form::hidden('avatar', null) !!}
			<div class="box-header with-border">
				<h3 class="box-title">Editar perfil</h3>
			</div>
			<div class="box-body">

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
						<div class="form-group {{ ($errors->has('tipo_identificacion_id')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('tipo_identificacion_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Tipo de identificación
							</label>
							<div class="col-sm-9">
								{!! Form::select('tipo_identificacion_id', $tipos, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo de identificación']) !!}
								@if ($errors->has('tipo_identificacion_id'))
									<span class="help-block">{{ $errors->first('tipo_identificacion_id') }}</span>
								@endif
							</div>							
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('identificacion')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('identificacion'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Número de identificación
							</label>
							<div class="col-sm-9">
								{!! Form::text('identificacion', null, ['class' => 'form-control', 'placeholder' => 'Número de identificación', 'autocomplete' => 'off']) !!}
								@if ($errors->has('identificacion'))
									<span class="help-block">{{ $errors->first('identificacion') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('usuario')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('usuario'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Usuario
							</label>
							<div class="col-sm-9">
								{!! Form::text('usuario', null, ['class' => 'form-control', 'placeholder' => 'Usuario', 'readonly']) !!}
								@if ($errors->has('usuario'))
									<span class="help-block">{{ $errors->first('usuario') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('primer_nombre')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('primer_nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Primer nombre
							</label>
							<div class="col-sm-9">
								{!! Form::text('primer_nombre', null, ['class' => 'form-control', 'placeholder' => 'Primer nombre', 'autocomplete' => 'off']) !!}
								@if ($errors->has('primer_nombre'))
									<span class="help-block">{{ $errors->first('primer_nombre') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('segundo_nombre')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('segundo_nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Segundo nombre
							</label>
							<div class="col-sm-9">
								{!! Form::text('segundo_nombre', null, ['class' => 'form-control', 'placeholder' => 'Segundo nombre', 'autocomplete' => 'off']) !!}
								@if ($errors->has('segundo_nombre'))
									<span class="help-block">{{ $errors->first('segundo_nombre') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('primer_apellido')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('primer_apellido'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Primer apellido
							</label>
							<div class="col-sm-9">
								{!! Form::text('primer_apellido', null, ['class' => 'form-control', 'placeholder' => 'Primer apellido', 'autocomplete' => 'off']) !!}
								@if ($errors->has('primer_apellido'))
									<span class="help-block">{{ $errors->first('primer_apellido') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('segundo_apellido')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('segundo_apellido'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Segundo apellido
							</label>
							<div class="col-sm-9">
								{!! Form::text('segundo_apellido', null, ['class' => 'form-control', 'placeholder' => 'Segundo apellido', 'autocomplete' => 'off']) !!}
								@if ($errors->has('segundo_apellido'))
									<span class="help-block">{{ $errors->first('segundo_apellido') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>

				<div class="row form-horizontal">
					<div class="col-md-8 col-md-offset-2 text-center">
						<div class="form-group {{ ($errors->has('email')?'has-error':'') }}">
							<label class="col-sm-3 control-label">
								@if ($errors->has('email'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Correo electrónico
							</label>
							<div class="col-sm-9">
								{!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Correo electrónico', 'autocomplete' => 'off']) !!}
								@if ($errors->has('email'))
									<span class="help-block">{{ $errors->first('email') }}</span>
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
			<div class="box-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('profile') }}" class="btn btn-danger pull-right">Cancelar</a>
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

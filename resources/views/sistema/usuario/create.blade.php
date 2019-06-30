@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Usuario
			<small>Sistema</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">sistema</a></li>
			<li class="active">usuario</li>
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
		<div class="row">
			{!! Form::open(['url' => 'usuario', 'method' => 'post', 'role' => 'form', 'files' => true, 'class' => 'form-horizontal']) !!}
			<div class="col-md-3">
				<div class="box box-primary">
					<div class="box-body box-profile">
						<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/avatars/avatar-160x160.png') }}" alt="Avatar">
						<h3 class="profile-username text-center" id="id_nombre_vista">Nombre</h3>
						<ul class="list-group list-group-unbordered">
							<li class="list-group-item">
								<b>Usuario</b> <a class="pull-right" id="id_usuario_vista">usuario</a>
							</li>
						</ul>
						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('avatar')?'has-error':'') }}">
									<div class="col-sm-12">
										<label class="control-label">
											@if ($errors->has('avatar'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Foto
										</label>
										<div class="">
											{!! Form::file('avatar', ['class' => 'form-control', 'placeholder' => 'Tipo de identificación']) !!}
										</div>
										@if ($errors->has('avatar'))
											<span class="help-block">{{ $errors->first('avatar') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						{!! Form::submit('Guardar', ['class' => 'btn btn-primary btn-block']) !!}
					</div>
				</div>
			</div>

			<div class="col-md-9">
				<div class="box box-success">
					<div class="box-body">
						
						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('tipo_identificacion_id')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('tipo_identificacion_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de identificación
									</label>
									<div class="col-sm-9">
										{!! Form::select('tipo_identificacion_id', $tipos, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo de identificación', 'required']) !!}
										@if ($errors->has('tipo_identificacion_id'))
											<span class="help-block">{{ $errors->first('tipo_identificacion_id') }}</span>
										@endif
									</div>
									
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('identificacion')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('identificacion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número de identificación
									</label>
									<div class="col-sm-9">
										{!! Form::text('identificacion', null, ['class' => 'form-control', 'placeholder' => 'Número de identificación', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('identificacion'))
											<span class="help-block">{{ $errors->first('identificacion') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('usuario')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('usuario'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Usuario
									</label>
									<div class="col-sm-9">
										{!! Form::text('usuario', null, ['class' => 'form-control', 'placeholder' => 'Usuario', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('usuario'))
											<span class="help-block">{{ $errors->first('usuario') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('password')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('password'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Contraseña
									</label>
									<div class="col-sm-9">
										{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('password'))
											<span class="help-block">{{ $errors->first('password') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('confirmar_password')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('confirmar_password'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Confirmar contraseña
									</label>
									<div class="col-sm-9">
										{!! Form::password('confirmar_password', ['class' => 'form-control', 'placeholder' => 'Confirmar contraseña', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('confirmar_password'))
											<span class="help-block">{{ $errors->first('confirmar_password') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('primer_nombre')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('primer_nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Primer nombre
									</label>
									<div class="col-sm-9">
										{!! Form::text('primer_nombre', null, ['class' => 'form-control', 'placeholder' => 'Primer nombre', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('primer_nombre'))
											<span class="help-block">{{ $errors->first('primer_nombre') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
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

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('primer_apellido')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('primer_apellido'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Primer apellido
									</label>
									<div class="col-sm-9">
										{!! Form::text('primer_apellido', null, ['class' => 'form-control', 'placeholder' => 'Primer apellido', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('primer_apellido'))
											<span class="help-block">{{ $errors->first('primer_apellido') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
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

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('email')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('email'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Correo electrónico
									</label>
									<div class="col-sm-9">
										{!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Correo electrónico', 'required', 'autocomplete' => 'off']) !!}
										@if ($errors->has('email'))
											<span class="help-block">{{ $errors->first('email') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									<div class="col-sm-offset-2 col-sm-9">
										{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
										<a href="{{ url('usuario') }}" class="btn btn-danger pull-right">Cancelar</a>
									</div>
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
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='identificacion']").enfocar();
		$(".select2").select2();

		$("input[name='usuario']").change(function(){
			changeUser($("input[name='usuario']").val());
		});

		$("input[name='usuario']").keyup(function(){
			changeUser($("input[name='usuario']").val());
		});

		function changeUser(obj)
		{
			var val = obj;
			val = val.length > 0?val:'usuario';
			if(val.length > 14)
			{
				val = val.substring(0, 14) + '...';
			}
			$("#id_usuario_vista").text(val);
		}

		$("input[name='primer_nombre']").change(function(){
			changeName();
		});

		$("input[name='primer_nombre']").keyup(function(){
			changeName();
		});

		$("input[name='primer_apellido']").change(function(){
			changeName();
		});

		$("input[name='primer_apellido']").keyup(function(){
			changeName();
		});

		function changeName(obj)
		{
			var nombre = $("input[name='primer_nombre']").val();
			var apellido = $("input[name='primer_apellido']").val();
			var nombre_corto = $.trim(nombre + " " + apellido);

			nombre_corto = nombre_corto.length > 0?nombre_corto:'Nombre';
			if(nombre_corto.length > 17)
			{
				nombre_corto = nombre_corto.substring(0, 17) + '...';
			}
			$("#id_nombre_vista").text(nombre_corto);
		}
	});
</script>
@endpush

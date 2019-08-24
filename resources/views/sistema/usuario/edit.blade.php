@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Usuario
						<small>Sistema</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Sistema</a></li>
						<li class="breadcrumb-item active">Usuario</li>
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
			<div class="row">
				{!! Form::model($usuario, ['url' => ['usuario', $usuario], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'id' => 'usuario']) !!}
				{!! Form::hidden('avatar', null) !!}
				<div class="col-md-3">
					<div class="card card-primary card-outline">
						<div class="card-body card-profile">
							<div id="image-cropper">
								<div class="cropit-preview"></div>
								<input type="file" class="cropit-image-input" />
								<div class="row text-center">
									<a class="select-image-btn btn btn-default btn-sm"><i class="fa fa-camera"></i></a>
									<a class="rotate-ccw-btn btn btn-default btn-sm"><i class="fa fa-rotate-left"></i></a>
									<a class="rotate-cw-btn btn btn-default btn-sm"><i class="fa fa-rotate-right"></i></a>
									<a class="zoom-in-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-in"></i></a>
									<a class="zoom-out-btn btn btn-default btn-sm"><i class="glyphicon glyphicon-zoom-out"></i></a>
								</div>
							</div>
							<br>
							<h3 class="profile-username text-center" id="id_nombre_vista">{{ $usuario->nombre_corto }}</h3>
							<ul class="list-group list-group-unbordered">
								<li class="list-group-item">
									<b>Usuario</b> <a class="pull-right" id="id_usuario_vista">{{ $usuario->usuario }}</a>
								</li>
							</ul>
							{!! Form::submit('Guardar', ['class' => 'btn btn-primary btn-block']) !!}
						</div>
					</div>
				</div>

				<div class="col-md-9">
					<div class="nav-tabs-custom">
						<ul class="nav nav-tabs">
							<li class="active"><a href="#general" data-toggle="tab" aria-expanded="true">General</a></li>
							<li class=""><a href="#entidades" data-toggle="tab" aria-expanded="false">Entidades</a></li>
						</ul>
						<div class="tab-content">
							<div class="tab-pane active" id="general">
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
												{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña']) !!}
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
												{!! Form::password('confirmar_password', ['class' => 'form-control', 'placeholder' => 'Confirmar contraseña']) !!}
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
										<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
											<label class="col-sm-3 control-label">
												@if ($errors->has('esta_activo'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Activo?
											</label>
											<div class="col-sm-9">
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary{{ $usuario->esta_activo?' active':'' }}">
														{!! Form::radio('esta_activo', '1', $usuario->esta_activo?true:false) !!}Sí
													</label>
													<label class="btn btn-danger{{ !$usuario->esta_activo?' active':'' }}">
														{!! Form::radio('esta_activo', '0', $usuario->esta_activo?false:true) !!}No
													</label>
												</div>
												@if ($errors->has('esta_activo'))
													<span class="help-block">{{ $errors->first('esta_activo') }}</span>
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
							<div class="tab-pane" id="entidades">
								<div class="table-responsive">
									<table class="table table-striped table-hover">
										<thead>
											<tr>
												<th>Entidad</th>
												<th>Perfil</th>
											</tr>
										</thead>
										<tbody>
											@foreach($entidades as $entidad)											
												<tr>
													<td>{{ $entidad->terceroEntidad->razon_social }}</td>
													<td>
														<?php
															$perfilActivo = null;
															$perfiles = $usuario->perfiles;
															foreach($perfiles as $perfil)
															{
																if($perfil->entidad->id == $entidad->id)
																{
																	$perfilActivo = $perfil->id;
																	break;
																}
															}
															$perfiles = $entidad->perfiles->pluck('nombre', 'id');
															$idSuperAdministrador = $perfiles->search(function($item, $key){
																return $item == "Super Administrador";
															});
															if($idSuperAdministrador) {
																$perfiles->pull($idSuperAdministrador);
															}
														?>
														{!! Form::select('entidades[' . $entidad->id . ']', $perfiles, $perfilActivo, ['class' => 'form-control', 'placeholder' => 'Seleccione perfil']) !!}
													</td>
												</tr>
											@endforeach
										</tbody>
										<tfoot>
											<tr>
												<th>Entidad</th>
												<th>Perfil</th>
											</tr>
										</tfoot>
									</table>
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
		width: 100px;
		height: 100px;
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
		var url = document.location.toString();
		if (url.match('#'))
		{
			$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		}

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

		$("input[name='esta_activo']").checkboxradio({
		  icon: false
		});

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

		$("#usuario").submit(function(){
			$("input[name='avatar']").val($imageCropper.cropit('export'));
		});

	});
</script>
@endpush

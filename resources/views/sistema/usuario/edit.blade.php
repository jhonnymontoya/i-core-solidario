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
			{!! Form::model($usuario, ['url' => ['usuario', $usuario], 'method' => 'put', 'role' => 'form', 'id' => 'usuario']) !!}
			{!! Form::hidden('avatar', null) !!}
			<div class="row">
				<div class="col-md-3">
					<div class="card card-primary card-outline">
						<div class="card-body">
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
							<br>
							<h3 class="profile-username text-center" id="id_nombre_vista">{{ $usuario->nombre_corto }}</h3>
							<ul class="list-group list-group-unbordered">
								<li class="list-group-item">
									<b>Usuario</b> <a class="pull-right" id="id_usuario_vista">{{ $usuario->usuario }}</a>
								</li>
							</ul>
							{!! Form::submit('Guardar', ['class' => 'btn btn-outline-primary btn-block']) !!}
						</div>
					</div>
				</div>

				<div class="col-md-9">
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills mb-3" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="pill" href="#general" role="tab" aria-selected="true">General</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="pill" href="#entidades" role="tab" aria-selected="false">entidades</a>
								</li>
							</ul>
							<div class="tab-content" id="pills-tabContent">
								<div class="tab-pane fade show active" id="general" role="tabpanel">
									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('tipo_identificacion_id') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Tipo de identificación</label>
												{!! Form::select('tipo_identificacion_id', $tipos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Tipo de identificación', 'required']) !!}
												@if ($errors->has('tipo_identificacion_id'))
													<div class="invalid-feedback">{{ $errors->first('tipo_identificacion_id') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('identificacion') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Número de identificación</label>
												{!! Form::text('identificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'required']) !!}
												@if ($errors->has('identificacion'))
													<div class="invalid-feedback">{{ $errors->first('identificacion') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('usuario') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Usuario</label>
												{!! Form::text('usuario', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Usuario', 'required']) !!}
												@if ($errors->has('usuario'))
													<div class="invalid-feedback">{{ $errors->first('usuario') }}</div>
												@endif
											</div>
										</div>
									</div>

									<hr>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('password') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Contraseña</label>
												{!! Form::password('password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Contraseña']) !!}
												@if ($errors->has('password'))
													<div class="invalid-feedback">{{ $errors->first('password') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('confirmar_password') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Confirmar contraseña</label>
												{!! Form::password('confirmar_password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Confirmar contraseña']) !!}
												@if ($errors->has('confirmar_password'))
													<div class="invalid-feedback">{{ $errors->first('confirmar_password') }}</div>
												@endif
											</div>
										</div>
									</div>

									<hr>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('primer_nombre') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Primer nombre</label>
												{!! Form::text('primer_nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer nombre', 'required']) !!}
												@if ($errors->has('primer_nombre'))
													<div class="invalid-feedback">{{ $errors->first('primer_nombre') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('segundo_nombre') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Segundo nombre</label>
												{!! Form::text('segundo_nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Segundo nombre']) !!}
												@if ($errors->has('segundo_nombre'))
													<div class="invalid-feedback">{{ $errors->first('segundo_nombre') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('primer_apellido') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Primer apellido</label>
												{!! Form::text('primer_apellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer apellido', 'required']) !!}
												@if ($errors->has('primer_apellido'))
													<div class="invalid-feedback">{{ $errors->first('primer_apellido') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('segundo_apellido') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Segundo apellido</label>
												{!! Form::text('segundo_apellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Segundo apellido']) !!}
												@if ($errors->has('segundo_apellido'))
													<div class="invalid-feedback">{{ $errors->first('segundo_apellido') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												@php
													$valid = $errors->has('email') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Correo electrónico</label>
												{!! Form::email('email', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico', 'required']) !!}
												@if ($errors->has('email'))
													<div class="invalid-feedback">{{ $errors->first('email') }}</div>
												@endif
											</div>
										</div>
									</div>

									<div class="row">
										<div class="col-md-12">
											<div class="form-group">
												<label class="control-label">¿Activo?</label>
												<div>
													@php
														$valid = $errors->has('esta_activo') ? 'is-invalid' : '';
														$estaActivo = empty(old('esta_activo')) ? $usuario->esta_activo : old('esta_activo');
													@endphp
													<div class="btn-group btn-group-toggle" data-toggle="buttons">
														<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
															{!! Form::radio('esta_activo', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
														</label>
														<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
															{!! Form::radio('esta_activo', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
														</label>
													</div>
													@if ($errors->has('esta_activo'))
														<div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
													@endif
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class="tab-pane fade" id="entidades" role="tabpanel">
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
								</div>
							</div>
						</div>
						<div class="card-footer text-right">
							{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
							<a href="{{ url('usuario') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		if (url.match('#')) {
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

		$(".select2").select2();

		$("input[name='usuario']").change(function(){
			changeUser($("input[name='usuario']").val());
		});

		$("input[name='usuario']").keyup(function(){
			changeUser($("input[name='usuario']").val());
		});

		function changeUser(obj) {
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

		function changeName(obj) {
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

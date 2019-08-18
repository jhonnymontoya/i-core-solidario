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
			<div class="col-md-3">
				<div class="card card-primary">
					<div class="card-body card-profile">
						<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/avatars/' . (empty($usuario->avatar)?'avatar-160x160.png':$usuario->avatar)) }}" alt="Avatar">
						<h3 class="profile-username text-center" id="id_nombre_vista">{{ $usuario->nombre_corto }}</h3>
						<ul class="list-group list-group-unbordered">
							<li class="list-group-item">
								<strong>Usuario</strong> <a class="pull-right" id="id_usuario_vista">{{ $usuario->usuario }}</a>
							</li>
							<li class="list-group-item">
								<strong>Entidades</strong>
								<span class="pull-right badge bg-{{ $usuario->perfiles->count()?'green':'red' }}">{{ $usuario->perfiles->count() }}</span>
							</li>
							<li class="list-group-item">
								<strong>En línea</strong>
								<i class="fa fa-circle text-success pull-right"></i>
							</li>
							<li class="list-group-item">
								<strong>Perfil completo</strong>
								<span class="pull-right badge bg-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}">{{ $usuario->porcentajePerfilCompleto() }}%</span>
								<br><br>
								<div class="progress progress-xs">
                  					<div class="progress-bar progress-bar-{{ $usuario->porcentajePerfilCompleto() == 100?'green':'yellow' }}" style="width: {{ $usuario->porcentajePerfilCompleto() }}%"></div>
                				</div>
							</li>
						</ul>
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
									<div class="form-group">
										<label class="col-sm-4">
											Tipo de identificación
										</label>
										<div class="col-sm-8">
											{{ $usuario->tipoIdentificacion->nombre }}
										</div>
										
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Número de identificación
										</label>
										<div class="col-sm-8">
											{{ $usuario->identificacion }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Usuario
										</label>
										<div class="col-sm-8">
											{{ $usuario->usuario }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Primer nombre
										</label>
										<div class="col-sm-8">
											{{ $usuario->primer_nombre }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Segundo nombre
										</label>
										<div class="col-sm-8">
											{{ $usuario->segundo_nombre }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Primer apellido
										</label>
										<div class="col-sm-8">
											{{ $usuario->primer_apellido }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Segundo apellido
										</label>
										<div class="col-sm-8">
											{{ $usuario->segundo_apellido }}
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											@if ($errors->has('email'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Correo electrónico
										</label>
										<div class="col-sm-8">
											<a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label class="col-sm-4 control-label">
											Estado
										</label>
										<div class="col-sm-8">
											<label class="label label-{{ $usuario->esta_activo?'success':'danger' }}">{{ $usuario->esta_activo?'Activo':'Inactivo' }}</label>
										</div>
									</div>
								</div>
							</div>
							<br><br>
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-sm-5">
											<a href="{{ url('usuario') }}" class="btn btn-block btn-sm btn-info"><i class="fa fa-arrow-left"></i> Volver</a>
										</div>
										<div class="col-sm-5">
											<a href="{{ route('usuarioEdit', $usuario) }}" class="btn btn-block btn-sm btn-info pull-right"><i class="fa fa-edit"></i> Editar</a>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="tab-pane" id="entidades">
							@if($usuario->perfiles->count())
								<div class="table-responsive">
									<table class="table table-striped table-hover">
										<thead>
											<tr>
												<th>Entidad</th>
												<th>Perfil</th>
											</tr>
										</thead>
										<tbody>
											@foreach($usuario->perfiles as $perfil)
												<tr>
													<td>{{ $perfil->entidad->terceroEntidad->razon_social }}</td>
													<td>{{ $perfil->nombre }}</td>
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
							@else
								<div class="row"><div class="col-md-12"><h4>No hay entidades asociadas</h4></div></div>
							@endif

							<br><br>
							<div class="row">
								<div class="col-md-6">
									<div class="row">
										<div class="col-sm-5">
											<a href="{{ url('usuario') }}" class="btn btn-block btn-sm btn-info"><i class="fa fa-arrow-left"></i> Volver</a>
										</div>
										<div class="col-sm-5">
											<a href="{{ route('usuarioEdit', [$usuario, '#entidades']) }}" class="btn btn-block btn-sm btn-info pull-right"><i class="fa fa-edit"></i> Editar</a>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>

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
		var url = document.location.toString();
		if (url.match('#'))
		{
			$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		}
	})
</script>
@endpush

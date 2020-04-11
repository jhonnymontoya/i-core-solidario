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
		<div class="container-fluid">
			<div class="row">
				<div class="col-md-3">
					<div class="card card-primary card-outline">
						<div class="card-body card-profile">
							<div class="text-center">
								<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/avatars/' . (empty($usuario->avatar)?'avatar-160x160.png':$usuario->avatar)) }}" alt="Avatar">
							</div>
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
					<div class="card">
						<div class="card-body">
							<ul class="nav nav-pills mb-3" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" data-toggle="pill" href="#general" role="tab" aria-controls="pills-home" aria-selected="true">General</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" data-toggle="pill" href="#entidades" role="tab" aria-controls="pills-profile" aria-selected="false">Entidades</a>
								</li>
							</ul>
							<div class="tab-content">
								<div class="tab-pane fade show active" id="general" role="tabpanel">
									<dl class="row">
										<dt class="col-4">Tipo de identificación</dt>
										<dd class="col-8">{{ $usuario->tipoIdentificacion->nombre }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Número de identificación</dt>
										<dd class="col-8">{{ number_format($usuario->identificacion) }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Usuario</dt>
										<dd class="col-8">{{ $usuario->usuario }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Primer nombre</dt>
										<dd class="col-8">{{ $usuario->primer_nombre }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Segundo nombre</dt>
										<dd class="col-8">{{ $usuario->segundo_nombre }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Segundo nombre</dt>
										<dd class="col-8">{{ $usuario->segundo_nombre }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Primer apellido</dt>
										<dd class="col-8">{{ $usuario->primer_apellido }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Segundo apellido</dt>
										<dd class="col-8">{{ $usuario->segundo_apellido }}</dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Correo electrónico</dt>
										<dd class="col-8"><a href="mailto:{{ $usuario->email }}">{{ $usuario->email }}</a></dd>
									</dl>

									<dl class="row">
										<dt class="col-4">Estado</dt>
										<label class="badge badge-pill badge-{{ $usuario->esta_activo?'success':'danger' }}">{{ $usuario->esta_activo?'Activo':'Inactivo' }}</label>
									</dl>
								</div>
								<div class="tab-pane fade" id="entidades" role="tabpanel">
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
								</div>
							</div>
						</div>
						<div class="card-footer text-right">
							<a href="{{ route('usuarioEdit', $usuario) }}" class="btn btn-outline-info pull-right"><i class="fa fa-edit"></i> Editar</a>
							<a href="{{ url('usuario') }}" class="btn btn-outline-danger"><i class="fa fa-arrow-left"></i> Volver</a>
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
		if (url.match('#')) {
			$('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
		}
	})
</script>
@endpush

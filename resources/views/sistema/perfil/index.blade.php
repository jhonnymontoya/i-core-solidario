@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Perfiles
						<small>Sistema</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Sistema</a></li>
						<li class="breadcrumb-item active">Perfiles</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('perfil/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $perfiles->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Perfiles</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'entidad', 'estado'), ['url' => 'perfil', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-4 col-sm-12">
							{!! Form::select('entidad', $entidades, null, ['class' => 'form-control select2', 'placeholder' => 'Entidad']); !!}
						</div>
						<div class="col-md-4 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							{!! Form::select('estado', [true => 'Activo', false => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}

					@if(!$perfiles->total())
						<br><br>
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron perfiles <a href="{{ url('perfil/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<br><br>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Entidad</th>
										<th>Nombre</th>
										<th>Descripción</th>
										<th>Menús</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($perfiles as $perfil)
										<tr>
											<td>{{ $perfil->entidad->terceroEntidad->razon_social }}</td>
											<td>{{ $perfil->nombre }}</td>
											<td>{{ str_limit($perfil->descripcion, 30) }}</td>
											<td>
												<span class="badge badge-pill bg-green">
													{{ $perfil->menus->count() }}
												</span>
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $perfil->esta_activo?'success':'danger' }}">
													{{ $perfil->esta_activo?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('perfilEdit', $perfil) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $perfiles->appends(Request::only('name', 'entidad', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $perfiles->total()?'primary':'danger' }}">
						{{ $perfiles->total() }}
					</span>&nbsp;elementos.
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
		$(window).formularioCrear("{{ url('perfil/create') }}");
		$(".select2").select2();
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Control de módulos
						<small>Sistema</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Sistema</a></li>
						<li class="breadcrumb-item active">Control de módulos</li>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $modulos->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Control de módulos</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'entidad', 'estado'), ['url' => '/controlModulos', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-5">
							{!! Form::select('entidad', $entidades, null, ['class' => 'form-control select2', 'placeholder' => 'Entidad', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col">
							{!! Form::select('estado', $estados, null, ['class' => 'form-control select2', 'placeholder' => 'Estado', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-1">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
					<br>
					@if(!$modulos->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron módulos
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Entidad</th>
										<th>Codigo</th>
										<th>Nombre</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($modulos as $modulo)
										<tr>
											<td>
												{{ $modulo->entidad->terceroEntidad->nombre }}
											</td>
											<td>
												{{ $modulo->codigo }}
											</td>
											<td>
												{{ $modulo->nombre }}
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $modulo->esta_activo ? 'success' : 'danger' }}">{{ $modulo->esta_activo ? 'Activo' : 'Inactivo' }}</span>
											</td>
											<td>
												<td><a class="btn btn-outline-info btn-sm" href="{{ route('controlModulos.edit', $modulo->id) }}"><i class="fa fa-edit"></i></a></td>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $modulos->appends(Request::only('name', 'entidad', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $modulos->total()?'primary':'danger' }}">
						{{ $modulos->total() }}
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
		$("input[name='name']").enfocar();
		$('.select2').select2();
	});
</script>
@endpush

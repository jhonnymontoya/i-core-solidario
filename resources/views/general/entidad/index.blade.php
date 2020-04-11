@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Entidad
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Entidad</li>
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
				<a href="{{ url('entidad/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $entidades->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Entidad</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name'), ['url' => '/entidad', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-11 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
					<br>
					@if(!$entidades->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron entidades <a href="{{ url('entidad/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>NIT</th>
										<th>Sigla</th>
										<th>Fecha inicial</th>
										<th>Sucursales</th>
										<th>Centros de Costo</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($entidades as $entidad)
										<tr>
											<td>{{ $entidad->terceroEntidad->razon_social }}</td>
											<td>{{ $entidad->terceroEntidad->nit }}</td>
											<td>{{ $entidad->terceroEntidad->sigla }}</td>
											<td>{{ $entidad->fecha_inicio_contabilidad->toFormattedDateString() }}</td>
											<td>
												<span class="badge badge-pill badge-{{ $entidad->usa_dependencia?'bg bg-purple':'bg bg-orange' }}">
													{{ $entidad->usa_dependencia?'Sí':'No' }}
												</span>
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $entidad->usa_centro_costos?'bg bg-purple':'bg bg-orange' }}">
													{{ $entidad->usa_centro_costos?'Sí':'No' }}
												</span>
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $entidad->terceroEntidad->esta_activo?'success':'danger' }}">
													{{ $entidad->terceroEntidad->esta_activo?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('entidadEdit', $entidad) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $entidades->appends(['name', 'estado'])->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $entidades->total()?'primary':'danger' }}">
						{{ $entidades->total() }}
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
		$(window).formularioCrear("{{ url('entidad/create') }}");
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Terceros
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Terceros</li>
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
				<a href="{{ url('tercero/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $terceros->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Terceros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'naturaleza', 'tipoIdentificacion', 'estado'), ['url' => 'tercero', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-3 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('naturaleza', ['NATURAL' => 'Natural', 'JURIDICA' => 'Juridico'], null, ['class' => 'form-control', 'placeholder' => 'Naturaleza']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('tipoIdentificacion', $tiposIdentificaciones, null, ['class' => 'form-control', 'placeholder' => 'Tipo identificación']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('estado', [1 => 'Activo', 0 => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$terceros->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron terceros <a href="{{ url('tercero/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="row">
							<div class="col-md-12 text-center">
								{!! $terceros->appends(Request::only('name', 'naturaleza', 'tipoIdentificacion', 'estado'))->render() !!}
							</div>
						</div>
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Naturaleza</th>
										<th>Tipo ID</th>
										<th>Identificación</th>
										<th>Nombre</th>
										<th class="text-center">Socio</th>
										<th class="text-center">Proveedor</th>
										<th class="text-center">Empleado</th>
										<th class="text-center">Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($terceros as $tercero)
										@php
											$numeroIdentificacion = $tercero->numero_identificacion;
											if($tercero->tipo_tercero == 'JURIDICA') {
												$numeroIdentificacion .= '-' . $tercero->digito_verificacion;
											}
										@endphp
										<tr>
											<td>{{ $tercero->tipo_tercero }}</td>
											<td>{{ $tercero->tipoIdentificacion->codigo }}</td>
											<td><a href="{{ route('terceroEdit', $tercero) }}">{{ $numeroIdentificacion }}</a></td>
											<td><a href="{{ route('terceroEdit', $tercero) }}">{{ $tercero->nombre }}</a></td>
											<td class="text-center">
												@if ($tercero->es_asociado)
													Sí
												@else
													No
												@endif
											</td>
											<td class="text-center">
												@if ($tercero->es_proveedor)
													Sí
												@else
													No
												@endif
											</td>
											<td class="text-center">
												@if ($tercero->es_cliente)
													Sí
												@else
													No
												@endif
											</td>
											<td class="text-center">
												@if ($tercero->esta_activo)
													<span class="badge badge-pill badge-success">Activo</span>
												@else
													<span class="badge badge-pill badge-danger">Inactivo</span>
												@endif
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('terceroEdit', $tercero) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $terceros->appends(Request::only('name', 'naturaleza', 'tipoIdentificacion', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $terceros->total()?'primary':'danger' }}">
						{{ $terceros->total() }}
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
		$(window).formularioCrear("{{ url('tercero/create') }}");
	});
</script>
@endpush
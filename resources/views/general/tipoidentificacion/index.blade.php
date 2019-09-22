@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de identificación
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Tipos de identificación</li>
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
				<a href="{{ url('tipoIdentificacion/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tiposIdentificacion->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Tipos de identificación</h3>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'estado'), ['url' => '/tipoIdentificacion', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
						</div>
						<div class="col-md-5 col-sm-12">
							{!! Form::select('estado', ['1' => 'Activo', '0' => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$tiposIdentificacion->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tipos de identificación <a href="{{ url('tipoIdentificacion/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th class="text-center">Código</th>
										<th class="text-center">Nombre</th>
										<th class="text-center">Tipo de persona</th>
										<th class="text-center">Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($tiposIdentificacion as $tipoIdentificacion)
										<tr>
											<td>{{ $tipoIdentificacion->codigo }}</td>
											<td>{{ $tipoIdentificacion->nombre }}</td>
											<td>{{ $tipoIdentificacion->aplicacion }}</td>
											<td>
												<span class="badge badge-pill badge-{{ $tipoIdentificacion->esta_activo?'success':'danger' }}">
													{{ $tipoIdentificacion->esta_activo?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('tipoIdentificacionEdit', $tipoIdentificacion) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $tiposIdentificacion->appends(Request::only('name', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $tiposIdentificacion->total()?'primary':'danger' }}">
						{{ $tiposIdentificacion->total() }}
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
		$(window).formularioCrear("{{ url('tipoIdentificacion/create') }}");
	});
</script>
@endpush
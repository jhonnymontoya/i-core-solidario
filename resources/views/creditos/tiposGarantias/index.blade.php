@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de garantías
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Tipos de garantías</li>
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
				<a href="{{ url('tipoGarantia/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tiposGarantias->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Tipos de garantías</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'entidad', 'estado'), ['url' => 'perfil', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-8 col-sm-12">
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

					@if(!$tiposGarantias->total())
						<br><br>
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tipos de garantía <a href="{{ url('tipoGarantia/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<br><br>
						<div class="table-responsive">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Tipo garantía</th>
										<th>Condición</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($tiposGarantias as $tipoGarantia)
										<tr>
											<td>{{ $tipoGarantia->codigo }}</td>
											<td>{{ $tipoGarantia->nombre }}</td>
											<td>{{ $tipoGarantia->tipo_garantia }}</td>
											<td>{{ $tipoGarantia->condicion }}</td>
											<td>
												<span class="badge badge-pill badge-{{ $tipoGarantia->esta_activa?'success':'danger' }}">
													{{ $tipoGarantia->esta_activa?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('tipoGarantiaEdit', $tipoGarantia) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $tiposGarantias->appends(Request::only('name', 'entidad', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $tiposGarantias->total()?'primary':'danger' }}">
						{{ $tiposGarantias->total() }}
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
		$(window).formularioCrear("{{ url('tipoGarantia/create') }}");
	});
</script>
@endpush

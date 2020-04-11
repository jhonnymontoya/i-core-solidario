@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Reportes
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Reportes</li>
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

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Menú reportes</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'modulo'), ['url' => 'reportes', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-8 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							{!! Form::select('modulo', $modulos, null, ['class' => 'form-control select2', 'placeholder' => 'Categoría']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}
					@if(!$reportes->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									<br>
									No se encontraron reportes
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Categoría</th>
										<th>Descripción</th>
										<th>Parámetros</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($reportes as $reporte)
										<tr>
											<td><a href="{{ route('reportesReporte', $reporte->id) }}">{{ $reporte->nombre }}</a></td>
											<td>{{ $reporte->modulo->nombre }}</td>
											<td>{{ str_limit($reporte->descripcion, 50) }}</td>
											<td>{{ $reporte->parametros->count() }}</td>
											<td>
												<a href="{{ route('reportesReporte', $reporte->id) }}" class="btn btn-outline-primary btn-sm"><i class="far fa-play-circle"></i></a>
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th>Nombre</th>
										<th>Categoría</th>
										<th>Descripción</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $reportes->appends(Request::only('name', 'modulo'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
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
@endpush
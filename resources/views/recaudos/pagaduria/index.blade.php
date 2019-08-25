@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Pagaduría
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Pagaduría</li>
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
				<a href="{{ url('pagaduria/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $pagadurias->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Pagadurías</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/pagaduria', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$pagadurias->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron pagadurías <a href="{{ url('pagaduria/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Periodicidad</th>
										<th>Contacto</th>
										<th>Email</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($pagadurias as $pagaduria)
										<tr>
											<td>{{ $pagaduria->nombre }}</td>
											<td>{{ $pagaduria->periodicidad_pago }}</td>
											<td>{{ $pagaduria->contacto }}</td>
											<td>{{ $pagaduria->contacto_email }}</td>
											<td>
												<span class="badge badge-pill badge-{{ $pagaduria->esta_activa?'success':'danger' }}">
													{{ $pagaduria->esta_activa?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('pagaduriaEdit', $pagaduria) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $pagadurias->appends(['name', 'estado'])->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $pagadurias->total()?'primary':'danger' }}">
						{{ $pagadurias->total() }}
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
		$(window).formularioCrear("{{ url('pagaduria/create') }}");
	});
</script>
@endpush
@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Pagaduría
			<small>Recaudos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Recaudos</a></li>
			<li class="active">Pagaduría</li>
		</ol>
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
				<a href="{{ url('pagaduria/create') }}" class="btn btn-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $pagadurias->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Pagadurías</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name'), ['url' => '/pagaduria', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				<br>
				@if(!$pagadurias->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron pagadurías <a href="{{ url('pagaduria/create') }}" class="btn btn-primary btn-xs">crear una nueva</a>
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
											<span class="label label-{{ $pagaduria->esta_activa?'success':'danger' }}">
												{{ $pagaduria->esta_activa?'activo':'inactivo' }}
											</span>
										</td>
										<td><a class="btn btn-info btn-xs" href="{{ route('pagaduriaEdit', $pagaduria) }}"><i class="fa fa-edit"></i></a></td>
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
			<div class="box-footer">
				<span class="label label-{{ $pagadurias->total()?'primary':'danger' }}">
					{{ $pagadurias->total() }}
				</span>&nbsp;elementos.
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
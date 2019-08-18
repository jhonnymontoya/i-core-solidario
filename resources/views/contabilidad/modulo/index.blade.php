@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Módulos
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Módulos</li>
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
				<a href="{{ url('modulo/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-{{ $modulos->total()?'primary':'danger' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Módulos</h3>

				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only('name', 'estado'), ['url' => '/modulo', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
					</div>
					<div class="col-md-5 col-sm-12">
						{!! Form::select('estado', ['1' => 'Activo', '0' => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$modulos->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron módulos <a href="{{ url('modulo/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
							</div>
						</div>
					</p>
				@else
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Estado</th>
									<th>Cuentas</th>
									<th>Tipos de comprobantes</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($modulos as $modulo)
									<tr>
										<td>{{ $modulo->nombre }}</td>
										<td>
											<span class="label label-{{ $modulo->esta_activo?'success':'danger' }}">
												{{ $modulo->esta_activo?'activo':'inactivo' }}
											</span>
										</td>
										<td>{{ $modulo->cuentasContables->where('entidad_id', Auth::getSession()->get('entidad')->id)->count() }}</td>
										<td>{{ $modulo->tiposComprobantes->where('entidad_id', Auth::getSession()->get('entidad')->id)->count() }}</td>
										<td>
											<a class="btn btn-primary btn-xs" href="{{ route('moduloEdit', $modulo) }}"><i class="fa fa-edit"></i></a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $modulos->appends(['name', 'estado'])->render() !!}
					</div>
				</div>
			</div>
			<div class="card-footer">
				<span class="label label-{{ $modulos->total()?'primary':'danger' }}">
					{{ $modulos->total() }}
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
	$(window).keydown(function(event) {
		if(event.altKey && event.keyCode == 78) { 
			window.location.href = "{{ url('modulo/create') }}";
			event.preventDefault(); 
		}
	});
	$(function(){
		$("input[name='name']").focus();
		$("input[name='name']").val($("input[name='name']").val());
	});
</script>
@endpush
@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cobros administrativos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Cobros administrativos</li>
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
				<a href="{{ url('cobrosAdministrativos/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $cobrosAdministrativos->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Cobros administrativos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/cobrosAdministrativos', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$cobrosAdministrativos->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron cobros administrativos <a href="{{ url('cobrosAdministrativos/create') }}" class="btn btn-primary btn-sm">crear una nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Efecto</th>
										<th>¿Parametrizado?</th>
										<th>Modalidades</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($cobrosAdministrativos as $cobro)
										@php
											$efecto = $cobro->efecto = 'DEDUCCIONCREDITO'? 'Deducción de crédito' : 'Adición al crédito';
										@endphp
										<tr>
											<td>
												<a href="{{ route('cobrosAdministrativos.edit', $cobro) }}">{{ $cobro->codigo }}</a>
											</td>
											<td>
												<a href="{{ route('cobrosAdministrativos.edit', $cobro) }}">{{ $cobro->nombre }}</a>
											</td>
											<td>{{ $efecto }}</td>
											<td>
												@php
													$parametrizado = $cobro->estaParametrizado();
												@endphp
												<span class="label label-{{ $parametrizado ? 'success' : 'danger' }}">
													{{ $parametrizado ? 'Sí' : 'No' }}
												</span>
											</td>
											<td>
												@php
													$modalidades = $cobro->modalidades->count();
												@endphp
												<span class="badge label-{{ $modalidades > 0 ? 'success' : 'danger' }}">{{ $modalidades }}</span>
											</td>
											<td>
												<span class="label label-{{ $cobro->esta_activo?'success':'danger' }}">
													{{ $cobro->esta_activo?'activa':'inactiva' }}
												</span>
											</td>
											<td>
												<a href="{{ route('cobrosAdministrativos.edit', $cobro) }}" class="btn btn-info btn-sm"><i class="fa fa-edit"></i></a>
												<a href="{{ route('cobrosAdministrativos.modalidades', $cobro) }}" class="btn btn-primary btn-sm" data-toggle="tooltip" title="Asociar modalidades"><i class="fa fa-plus"></i></a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $cobrosAdministrativos->appends(Request::only('name'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="label label-{{ $cobrosAdministrativos->total()?'primary':'danger' }}">
						{{ $cobrosAdministrativos->total() }}
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
		$(window).formularioCrear("{{ url('cobrosAdministrativos/create') }}");
	});
</script>
@endpush
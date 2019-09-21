@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Seguros de cartera
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Seguros de cartera</li>
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
				<a href="{{ url('seguroCartera/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $segurosCartera->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Seguros de cartera</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name'), ['url' => 'seguroCartera', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-11 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}

					@if(!$segurosCartera->total())
						<br><br>
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron seguros de cartera <a href="{{ url('seguroCartera/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
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
										<th>Aseguradora</th>
										<th>Base prima</th>
										<th class="text-center">Tasa</th>
										<th>Modalidades asociadas</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($segurosCartera as $seguroCartera)
										<tr>
											<td>{{ $seguroCartera->codigo }}</td>
											<td>{{ $seguroCartera->nombre }}</td>
											<td>{{ $seguroCartera->aseguradoraTercero->nombre }}</td>
											<td>{{ $seguroCartera->base_prima }}</td>
											<td class="text-right">{{ number_format($seguroCartera->tasa_mes, 4) }}%</td>
											@php
												$label = "warning";
												$label = $seguroCartera->modalidades->count() == 0 ? "warning" : "success";
											@endphp
											<td class="text-center"><span class="badge badge-pill badge-{{ $label }}">{{ $seguroCartera->modalidades->count() }}</span></td>
											<td>
												<span class="badge badge-pill badge-{{ $seguroCartera->esta_activo?'success':'danger' }}">
													{{ $seguroCartera->esta_activo?'activo':'inactivo' }}
												</span>
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('seguroCarteraEdit', $seguroCartera) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $segurosCartera->appends(Request::only('name'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $segurosCartera->total()?'primary':'danger' }}">
						{{ $segurosCartera->total() }}
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
		$(window).formularioCrear("{{ url('seguroCartera/create') }}");
	});
</script>
@endpush

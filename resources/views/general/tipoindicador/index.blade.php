@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de indicadores
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Tipos de indicadores</li>
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
				<a href="{{ url('tipoIndicador/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tiposIndicadores->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Tipos de indicadores</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/tipoIndicador', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-10 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$tiposIndicadores->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tipos de indicadores <a href="{{ url('tipoIndicador/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Periodicidad</th>
										<th>Variable</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($tiposIndicadores as $tipoIndicador)
										<?php
											$variable = "";
											switch ($tipoIndicador->variable)
											{
												case 'PORCENTAJE':
													$variable = '%';
													break;
												case 'VALOR':
													$variable = '$';
													break;
												
												default:
													$variable = '%';
													break;
											}
										?>
										<tr>
											<td>{{ $tipoIndicador->codigo }}</td>
											<td>{{ $tipoIndicador->periodicidad }}</td>
											<td><span class="badge badge-pill badge-primary">{{ $variable }}</span></td>
											<td>											
												@if($tipoIndicador->esta_actualizado)
													<span class="badge badge-pill badge-success">
														actualizado
													</span>
												@else
													<a href="{{ route('indicadorCreate', $tipoIndicador->id) }}">
														<span class="badge badge-pill badge-warning">
															por actualizar
														</span>
													</a>
												@endif
											</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('tipoIndicadorEdit', $tipoIndicador) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $tiposIndicadores->appends(Request::only('name', 'estado'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $tiposIndicadores->total()?'primary':'danger' }}">
						{{ $tiposIndicadores->total() }}
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
		$(window).formularioCrear("{{ url('tipoIndicador/create') }}");
	});
</script>
@endpush
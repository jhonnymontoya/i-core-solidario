@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos de indicadores
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Tipos de indicadores</li>
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
				<a href="{{ url('tipoIndicador/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $tiposIndicadores->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Tipos de indicadores</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name'), ['url' => '/tipoIndicador', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-10 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$tiposIndicadores->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron tipos de indicadores <a href="{{ url('tipoIndicador/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
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
										<td><span class="label label-primary">{{ $variable }}</span></td>
										<td>											
											@if($tipoIndicador->esta_actualizado)
												<span class="label label-success">
													actualizado
												</span>
											@else
												<a href="{{ route('indicadorCreate', $tipoIndicador->id) }}">
													<span class="label label-warning">
														por actualizar
													</span>
												</a>
											@endif
										</td>
										<td><a class="btn btn-info btn-xs" href="{{ route('tipoIndicadorEdit', $tipoIndicador) }}"><i class="fa fa-edit"></i></a></td>
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
			<div class="box-footer">
				<span class="label label-{{ $tiposIndicadores->total()?'primary':'danger' }}">
					{{ $tiposIndicadores->total() }}
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
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('tipoIndicador/create') }}");
	});
</script>
@endpush
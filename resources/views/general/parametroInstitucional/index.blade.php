@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Parametros institucionales
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Parametros institucionales</li>
		</ol>
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
		<br>
		<div class="box box-{{ $parametros->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Parametros institucionales</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name'), ['url' => '/entidad', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				<br>
				@if(!$parametros->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron parametros
							</div>
						</div>
					</p>
				@else
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Modulo</th>
									<th>Código</th>
									<th>Descripción</th>
									<th>Valor</th>
									<th>Indicador</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($parametros as $parametro)
									<?php
										if($parametro->indicador === null)
										{
											$indicador = '';
										}
										elseif($parametro->indicador === true)
										{
											$indicador = 'Sí';
										}
										else
										{
											$indicador = 'No';
										}
									?>
									<tr>
										<td>{{ $parametro->modulo }}</td>
										<td>{{ $parametro->codigo }}</td>
										<td>{{ str_limit($parametro->descripcion, 70) }}</td>
										<td>{{ $parametro->valor }}</td>
										<td>{{ $indicador }}</td>
										<td><a class="btn btn-info btn-xs" href="{{ route('parametroInstitucionalEdit', $parametro) }}"><i class="fa fa-edit"></i></a></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $parametros->appends(['name', 'estado'])->render() !!}
					</div>
				</div>
			</div>
			<div class="box-footer">
				<span class="label label-{{ $parametros->total()?'primary':'danger' }}">
					{{ $parametros->total() }}
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
@endpush
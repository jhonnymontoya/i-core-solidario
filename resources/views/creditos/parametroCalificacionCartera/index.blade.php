@extends('layouts.admin')

@section('content')

{{-- Modal de actualización de calificación --}}
@component('creditos.parametroCalificacionCartera.modales.actualizar')
@endcomponent

{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Parámetros Calificación Cartera
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Parámetros Calificación Cartera</li>
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
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Parámetros calificación de cartera</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">
									@if ($errors->has('tipo_cartera'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo de cartera
								</label>
								<br>
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn btn-primary active">
										{!! Form::radio('tipo_cartera', 'CONSUMO', true) !!}Consumo
									</label>
									<label class="btn btn-primary disabled">
										{!! Form::radio('tipo_cartera', 'VIVIENDA', false) !!}Vivienda
									</label>
									<label class="btn btn-primary disabled">
										{!! Form::radio('tipo_cartera', 'COMERCIAL', false) !!}Comercial
									</label>
									<label class="btn btn-primary disabled">
										{!! Form::radio('tipo_cartera', 'MICROCREDITO', false) !!}Microcredito
									</label>
								</div>
								@if ($errors->has('tipo_cartera'))
									<span class="help-block">{{ $errors->first('tipo_cartera') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th class="text-center" width="30%">Categoría</th>
										<th class="text-center" width="30%">Mora en días desde</th>
										<th class="text-center" width="30%">Mora en días hasta</th>
										<th class="text-center" width="10%"></th>
									</tr>
								</thead>
								<tbody>
									@php
										$c = $calificaciones->where('tipo_cartera', 'CONSUMO')->where('calificacion', 'A')->first();
									@endphp
									<tr class="actualizar" data-tipocartera="CONSUMO" data-calificacion="A" data-desde="{{ optional($c)->dias_desde }}" data-hasta="{{ optional($c)->dias_hasta }}">
										<th class="text-center">A</th>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_desde }}</td>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_hasta }}</td>
										<td class="text-center">
											<a href="#" class="btn btn-outline-primary btn-sm hide act" data-toggle="modal" data-target="#mActualizar">Actualizar</a>
										</td>
									</tr>

									@php
										$c = $calificaciones->where('tipo_cartera', 'CONSUMO')->where('calificacion', 'B')->first();
									@endphp
									<tr class="actualizar" data-tipocartera="CONSUMO" data-calificacion="B" data-desde="{{ optional($c)->dias_desde }}" data-hasta="{{ optional($c)->dias_hasta }}">
										<th class="text-center">B</th>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_desde }}</td>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_hasta }}</td>
										<td class="text-center">
											<a href="#" class="btn btn-outline-primary btn-sm hide act" data-toggle="modal" data-target="#mActualizar">Actualizar</a>
										</td>
									</tr>

									@php
										$c = $calificaciones->where('tipo_cartera', 'CONSUMO')->where('calificacion', 'C')->first();
									@endphp
									<tr class="actualizar" data-tipocartera="CONSUMO" data-calificacion="C" data-desde="{{ optional($c)->dias_desde }}" data-hasta="{{ optional($c)->dias_hasta }}">
										<th class="text-center">C</th>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_desde }}</td>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_hasta }}</td>
										<td class="text-center">
											<a href="#" class="btn btn-outline-primary btn-sm hide act" data-toggle="modal" data-target="#mActualizar">Actualizar</a>
										</td>
									</tr>

									@php
										$c = $calificaciones->where('tipo_cartera', 'CONSUMO')->where('calificacion', 'D')->first();
									@endphp
									<tr class="actualizar" data-tipocartera="CONSUMO" data-calificacion="D" data-desde="{{ optional($c)->dias_desde }}" data-hasta="{{ optional($c)->dias_hasta }}">
										<th class="text-center">D</th>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_desde }}</td>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_hasta }}</td>
										<td class="text-center">
											<a href="#" class="btn btn-outline-primary btn-sm hide act" data-toggle="modal" data-target="#mActualizar">Actualizar</a>
										</td>
									</tr>

									@php
										$c = $calificaciones->where('tipo_cartera', 'CONSUMO')->where('calificacion', 'E')->first();
									@endphp
									<tr class="actualizar" data-tipocartera="CONSUMO" data-calificacion="E" data-desde="{{ optional($c)->dias_desde }}" data-hasta="{{ optional($c)->dias_hasta }}">
										<th class="text-center">E</th>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_desde }}</td>
										<td class="text-center">{{ is_null($c) ? 'No parametrizado' : $c->dias_hasta }}</td>
										<td class="text-center">
											<a href="#" class="btn btn-outline-primary btn-sm hide act" data-toggle="modal" data-target="#mActualizar">Actualizar</a>
										</td>
									</tr>
								</tbody>
							</table>
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
<style type="text/css">
	.disabled {
		cursor: not-allowed;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(".act").click(function(e){
			e.preventDefault();
		});
		$(".actualizar").hover(function() {
			$(this).find(".btn").removeClass("hide");
		}, function() {
			$(this).find(".btn").addClass("hide");
		});
	});
</script>
@endpush
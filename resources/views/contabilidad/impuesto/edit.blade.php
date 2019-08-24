@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Impuestos
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Impuestos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		{!! Form::model($impuesto, ['url' => ['impuesto', $impuesto], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar impuesto</h3>
				</div>
				<div class="card-body">
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								<div class="col-sm-8">
									{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre del impuesto', 'autofocus']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group {{ ($errors->has('tipo')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('tipo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo de impuesto
								</label>
								<div class="col-sm-8">
									{!! Form::select('tipo', $tiposImpuestos, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un tipo de impuesto']) !!}
									@if ($errors->has('tipo'))
										<span class="help-block">{{ $errors->first('tipo') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('esta_activo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<div class="col-sm-8">
									<div class="btn-group" data-toggle="buttons">
										@php
											$estado = $impuesto->esta_activo;
											if (!is_null(old('esta_activo'))) {
												$estado = (bool) old('esta_activo');
											}
										@endphp
										<label class="btn btn-outline-primary{{ $estado ? ' active' : '' }}">
											{!! Form::radio('esta_activo', '1', $estado) !!}Activo
										</label>
										<label class="btn btn-outline-danger{{ $estado ? '' : ' active' }}">
											{!! Form::radio('esta_activo', '0', !$estado) !!}Inactivo
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<span class="help-block">{{ $errors->first('esta_activo') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr>
					<h3>Adicionar concepto</h3>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('nombreConcepto')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombreConcepto'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombreConcepto', null, ['class' => 'form-control', 'placeholder' => 'Nombre del concepto', 'form' => 'formConcepto']) !!}
								@if ($errors->has('nombreConcepto'))
									<span class="help-block">{{ $errors->first('nombreConcepto') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('cuenta'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Cuenta destino
								</label>
								{!! Form::select('cuenta', [], null, ['class' => 'form-control select2', 'placeholder' => 'Cuenta destino', 'form' => 'formConcepto']) !!}
								@if ($errors->has('cuenta'))
									<span class="help-block">{{ $errors->first('cuenta') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('tasa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tasa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tasa
								</label>
								{!! Form::number('tasa', null, ['class' => 'form-control', 'placeholder' => 'Tasa', 'form' => 'formConcepto', 'min' => 0, 'max' => 100, 'step' => '0.01']) !!}
								@if ($errors->has('tasa'))
									<span class="help-block">{{ $errors->first('tasa') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-1">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<br>
								{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success', 'form' => 'formConcepto']) !!}
							</div>
						</div>
					</div>
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-12">
							<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
						</div>
					</div>
					{{-- FIN FILA --}}
					<br>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-striped">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Cuenta destino</th>
										<th>Tasa</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody id="dataConceptos">
									<?php
										foreach ($conceptos as $concepto) {
											$estado = $concepto->esta_activo;
											?>
											<tr data-id="{{ $concepto->id }}">
												<td>{{ $concepto->nombre }}</td>
												<td>{{ $concepto->cuentaDestino->full }}</td>
												<td>{{ number_format($concepto->tasa, 2) }}%</td>
												<td>
													<a href="#" title="Inactivar" class="toggle-estado">
														<span class="label label-{{ $estado ? "success" : "danger" }}">
															{{ $estado ? "Activo" : "Inactivo" }}
														</span>
													</a>
												</td>
											</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('impuesto') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
		{!! Form::open(['id' => 'formConcepto', 'data-maskMoney-removeMask']) !!}
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(".select2").select2();

		$("select[name='cuenta']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						modulo: '5',
						estado: '1',
						tipoCuenta: 'AUXILIAR'
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});

		$("#formConcepto").on("submit", function(event){
			event.preventDefault();
			var $data = $(this).serialize();
			$.ajax({
				url: "{{ route('impuesto.agregarConcepto', $impuesto->id) }}",
				method: 'PUT',
				dataType: 'json',
				data: $data
			}).done(function(data){
				var $data = data;
				var $fila = $("<tr data-id='" + $data.id + "'></tr>");
				$fila.append("<td>" + $data.nombre + "</td>");
				$fila.append("<td>" + $data.cuenta + "</td>");
				$fila.append("<td>" + $data.tasa + "</td>");
				$fila.append("<td><a href='#' title='Inactivar' class='toggle-estado'><span class='label label-" + ($data.estado ? "success" : "danger") + "'>" + ($data.estado ? "Activo" : "Inactivo") + "</span></a></td>");
				$("#dataConceptos").append($fila);
			}).fail(function(data) {
				var $error = data.responseJSON.errors;
				error($error);
			});
		});
		$(".toggle-estado").click(function(event){
			event.preventDefault();
			var boton = $(this);
			var concepto = boton.parents("tr").data("id");
			var url = "{{ url('impuesto', $impuesto->id) }}/" + concepto;
			$.ajax({
				url: url,
				method: 'PUT',
				dataType: 'json',
				data: "_token={{ csrf_token() }}"
			}).done(function(data){
				var tmp = boton.find("span");
				if (data.estado) {
					boton.attr("title", "Inactivar");
					tmp.removeClass("label-danger").addClass("label-success");
					tmp.text("Activo");
				}
				else {
					boton.attr("title", "Activar");
					tmp.removeClass("label-success").addClass("label-danger");
					tmp.text("Inactivo");
				}
			}).fail(function(data) {
				var $error = new Array(data.responseJSON.error);
				error([$error]);
			});
		});
	});
	function error(data)
	{
		$msg = "";
		$.each(data, function (key, subData){
			$.each(subData, function (index, childData) {
				$msg += childData + "<br>";
			});
		});
		$("#error").html($msg);
		$("#error").show();
		$("#error").fadeOut(5000);
	}
</script>
@endpush

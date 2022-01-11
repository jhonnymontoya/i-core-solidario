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
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo de mpuesto</label>
								{!! Form::select('tipo', $tiposImpuestos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione un tipo de impuesto']) !!}
								@if ($errors->has('tipo'))
									<div class="invalid-feedback">{{ $errors->first('tipo') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">¿Activo?</label>
								<div>
									@php
										$valid = $errors->has('esta_activo') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activo')) ? $impuesto->esta_activo : old('esta_activo');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Activo
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}Inactivo
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr>
					<h3>Adicionar concepto</h3>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('nombreConcepto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombreConcepto', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'form' => 'formConcepto']) !!}
								@if ($errors->has('nombreConcepto'))
									<div class="invalid-feedback">{{ $errors->first('nombreConcepto') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('cuenta') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta destino</label>
								{!! Form::select('cuenta', [], null, ['class' => [$valid, 'form-control', 'select2'], 'form' => 'formConcepto']) !!}
								@if ($errors->has('cuenta'))
									<div class="invalid-feedback">{{ $errors->first('cuenta') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tasa') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tasa</label>
								<div class="input-group">
									{!! Form::number('tasa', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tasa', 'form' => 'formConcepto', 'min' => 0, 'max' => 100, 'step' => '0.001']) !!}
									<div class="input-group-prepend">
										<span class="input-group-text">%</span>
									</div>
									@if ($errors->has('tasa'))
										<div class="invalid-feedback">{{ $errors->first('tasa') }}</div>
									@endif
								</div>
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
							<table class="table table-striped table-hover">
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
												<td>{{ number_format($concepto->tasa, 3) }}%</td>
												<td>
													<a href="#" title="Inactivar" class="toggle-estado">
														<span class="badge badge-pill badge-{{ $estado ? "success" : "danger" }}">
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
				<div class="card-footer text-right">
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

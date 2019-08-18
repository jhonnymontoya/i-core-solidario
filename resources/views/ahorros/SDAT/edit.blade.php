@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						SDAT
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">SDAT</li>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('error') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar tipo SDAT</h3>
					</div>
					<div class="card-body">
						{!! Form::model($tipo, ['url' => ['tipoSDAT', $tipo], 'method' => 'put', 'role' => 'form']) !!}
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'autofocus']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('esta_activo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Esta activo?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$activo = empty(old('esta_activo')) ? $tipo->esta_activo : old('esta_activo');
											$activo = $activo ? true : false;
										?>
										<label class="btn btn-primary {{ $activo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', '1', $activo ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$activo ? 'active' : '' }}">
											{!! Form::radio('esta_activo', '0', !$activo ? true : false) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<span class="help-block">{{ $errors->first('esta_activo') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('capital_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('capital_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta capital
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('capital_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('capital_cuif_id'))
										<span class="help-block">{{ $errors->first('capital_cuif_id') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('intereses_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('intereses_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta intereses
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('intereses_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('intereses_cuif_id'))
										<span class="help-block">{{ $errors->first('intereses_cuif_id') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('intereses_por_pagar_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('intereses_por_pagar_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta intereses por pagar
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('intereses_por_pagar_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('intereses_por_pagar_cuif_id'))
										<span class="help-block">{{ $errors->first('intereses_por_pagar_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<br>

						<div class="row">
							<div class="col-md-12">
								{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
								<a href="{{ url('tipoSDAT') }}" class="btn btn-danger pull-right">Cancelar</a>
							</div>
						</div>
						{!! Form::close() !!}
						<hr>

						{{-- FORMULARIO DE AGREGAR CONDICIÓN PERIODO --}}
						<a class="btn btn-success btn-add-per"><i class="fa fa-plus"></i> Agregar rango de tiempo</a>
						<div id="conPer" style="display: none">
							<form id="frmConPer" data-maskMoney-removeMask>
							{{ csrf_field() }}
							<div class="row">
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Días desde</label>
										{!! Form::text('dd', '0', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Días desde', 'data-maskmoney', 'data-allowzero' => 'true']) !!}
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">Días hasta</label>
										{!! Form::text('dh', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Días hasta', 'data-maskmoney']) !!}
									</div>
								</div>

								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">&nbsp;</label><br>
										{!! Form::submit('Agregar', ['class' => 'btn btn-success']) !!}
									</div>
								</div>
							</div>
							</form>
							<div class="row">
								<div class="col-md-12">&nbsp;
									<span class="text-danger"></span>
									<span class="text-success"></span>
								</div>
							</div>
						</div>
						{{-- FIN DE FORMULARIO DE AGREGAR CONDICIÓN PERIODO --}}

						<br><br>
						<div class="row">
							<div class="col-md-12">
								<div class="panel-group">
									@php
										$id = 0;
									@endphp
									@foreach ($condiciones as $condicion)
										<div class="panel panel-default">
											<div class="panel-heading" role="tab">
												<h4 class="panel-title">
													<a role="button" data-toggle="collapse" data-parent="#accordion" href="#con{{$id}}" aria-expanded="false" aria-controls="con{{$id}}">
														{{ $condicion["periodo"] }}
													</a>
													<div class="pull-right">
														<a class="btn btn-success btn-xs" data-toggle="modal" data-target="#mam" data-href="#con{{$id}}" data-dd="{{ $condicion["plazo_minimo"] }}" data-dh="{{ $condicion["plazo_maximo"] }}">
															<font color="#fff"><i class="fa fa-plus"></i> agregar montos</font>
														</a>
														<a data-toggle="modal" data-target="#mdelPeriodo" data-dd="{{ $condicion["plazo_minimo"] }}" data-dh="{{ $condicion["plazo_maximo"] }}" class="btn btn-danger btn-xs" title="Eliminar rango de tiempo"><font color="#fff"><i class="fa fa-trash"></i></font></a>
													</div>
												</h4>
											</div>
											<div id="con{{$id}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="con{{$id++}}">
												<div class="panel-body">
													@if($condicion->get("montos")->count())
														<div class="row">
															<div class="col-md-12 tablesponsive">
																<table class="table table-striped">
																	<thead>
																		<tr>
																			<th class="text-center">Monto mínimo</th>
																			<th class="text-center">Monto máximo</th>
																			<th class="text-center">Tasa</th>
																			<th class="text-center"></th>
																		</tr>
																	</thead>

																	<tbody>
																		@foreach ($condicion->get("montos") as $monto)
																			<tr>
																				<td class="text-right">{{ $monto["monto_minimo"] }}</td>
																				<td class="text-right">{{ $monto["monto_maximo"] }}</td>
																				<td class="text-right">{{ $monto["tasa"] }}</td>
																				<td class="text-right">
																					<a data-toggle="modal" data-target="#mdelMonto" data-id="{{ $monto["id"] }}" class="btn btn-danger btn-xs"><i class="fa fa-trash"></i></a>
																				</td>
																			</tr>
																		@endforeach
																	</tbody>
																</table>
															</div>
														</div>
													@else
														Sin registros para mostrar
													@endif
												</div>
											</div>
										</div>
									@endforeach
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade" id="mam" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["id" => "frmConMon", "data-maskMoney-removeMask"]) !!}
	{!! Form::hidden("dd", "") !!}
	{!! Form::hidden("dh", "") !!}
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="mLabel">Rango de monto</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						Especifique el monto mínimo, monto máximo y la tasa.
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Monto mínimo</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('md', '0', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Monto desde', 'data-maskmoney', 'data-allowzero' => 'true']) !!}
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Monto máximo</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('mh', '0', ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Monto hasta', 'data-maskmoney']) !!}
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">Tasa E.A.</label>
							<div class="input-group">
								{!! Form::text('tasa', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tasa']) !!}
								<span class="input-group-addon">%</span>
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">&nbsp;
						<span class="text-danger"></span>
						<span class="text-success"></span>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Guardar", ["class" => "btn btn-success"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>

<div class="modal fade" id="mdelMonto" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["method" => "delete"]) !!}
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="mLabel">Eliminar monto</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						¿Seguro que desea eliminar el rango de monto?
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Eliminar", ["class" => "btn btn-danger"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>

<div class="modal fade" id="mdelPeriodo" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["route" => ["tipoSDAT.eliminarCondicionPeriodo", $tipo->id], "method" => "delete"]) !!}
	{{ Form::hidden("dd", null) }}
	{{ Form::hidden("dh", null) }}
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title" id="mLabel">Eliminar rango de tiempo</h4>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						¿Seguro que desea eliminar el rango de tiempo?
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-success" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Eliminar", ["class" => "btn btn-danger"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='capital_cuif_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						tipoCuenta: 'AUXILIAR',
						estado: 1,
						modulo: 6,
						page: params.page
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

		@php
			$capitalCuifId = $tipo->capital_cuif_id;
			$interesesCuifId = $tipo->intereses_cuif_id;
			$interesesPorPagarCuifId = $tipo->intereses_por_pagar_cuif_id;

			$capitalCuifId = empty(old('capital_cuif_id')) ? $capitalCuifId : old('capital_cuif_id');
			$interesesCuifId = empty(old('intereses_cuif_id')) ? $interesesCuifId : old('intereses_cuif_id');
			$interesesPorPagarCuifId = empty(old('intereses_por_pagar_cuif_id')) ? $interesesPorPagarCuifId : old('intereses_por_pagar_cuif_id');
		@endphp

		$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $capitalCuifId }} }}).done(function(data){
			if(data.total_count == 1)
			{
				element = data.items[0];
				$('<option>').val(element.id).text(element.text).appendTo($("select[name='capital_cuif_id']"));
				$("select[name='capital_cuif_id']").val(element.id).trigger("change");
			}
		});

		$("select[name='intereses_cuif_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						tipoCuenta: 'AUXILIAR',
						estado: 1,
						modulo: 2,
						page: params.page
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

		$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $interesesCuifId }} }}).done(function(data){
			if(data.total_count == 1)
			{
				element = data.items[0];
				$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_cuif_id']"));
				$("select[name='intereses_cuif_id']").val(element.id).trigger("change");
			}
		});

		$("select[name='intereses_por_pagar_cuif_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						tipoCuenta: 'AUXILIAR',
						estado: 1,
						modulo: 6,
						page: params.page
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

		$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $interesesPorPagarCuifId }} }}).done(function(data){
			if(data.total_count == 1)
			{
				element = data.items[0];
				$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_por_pagar_cuif_id']"));
				$("select[name='intereses_por_pagar_cuif_id']").val(element.id).trigger("change");
			}
		});

		$("#frmConPer").on("submit", function(event){
			var frm = $(this);
			data = frm.serialize();
			event.preventDefault();
			$.post({
				url: "{{ route('tipoSDAT.agregarCondicionPeriodo', $tipo->id) }}",
				dataType: 'json',
				data: data
			}).done(function(data){
				$(".text-success").html("Condición agregada");
				$(".text-success").show();
				$(".text-success").fadeOut(5000);
				frm.trigger("reset");
				$(".btn-add-per").show();
				$("#conPer").hide();
				window.location.href = "{{ route('tipoSDAT.edit', $tipo->id) }}";
			}).fail(function(data){
				var $error = jQuery.parseJSON(data.responseText);
				error($error);
			});
		});

		$(".btn-add-per").click(function(event){
			event.preventDefault();
			$(this).hide();
			$("#conPer").show();
		});

		$('#mam').on('show.bs.modal', function (event) {
			var btn = $(event.relatedTarget);
			var dd = btn.data('dd');
			var dh = btn.data('dh');

			var modal = $(this);
			modal.find('input[name="dd"]').val(dd);
			modal.find('input[name="dh"]').val(dh);
			modal.find('#mLabel').text("Rango de monto :dd - :dh".replace(":dd", dd).replace(":dh", dh));
			$(btn.data("href")).collapse("show")
			$("#frmConMon").trigger("reset");
		});

		$("#frmConMon").on("submit", function(event){
			event.preventDefault();
			var frm = $(this);
			var token = $(frm.find("input[name='_token']")).val();
			var dd = $(frm.find("input[name='dd']")).val();
			var dh = $(frm.find("input[name='dh']")).val();
			var md = $(frm.find("input[name='md']")).maskMoney("cleanvalue");
			var mh = $(frm.find("input[name='mh']")).maskMoney("cleanvalue");
			var tasa = $(frm.find("input[name='tasa']")).val();
			data = "_token=:token&dd=:dd&dh=:dh&md=:md&mh=:mh&tasa=:tasa";
			data = data.replace(":token", token).replace(":dd", dd).replace(":dh", dh).replace(":md", md).replace(":mh", mh).replace(":tasa", tasa);

			$.ajax({
				url: "{{ route('tipoSDAT.agregarCondicionMonto', $tipo->id) }}",
				dataType: 'json',
				type: 'put',
				data: data
			}).done(function(data){
				window.location.href = "{{ route('tipoSDAT.edit', $tipo->id) }}";
			}).fail(function(data){
				var $error = jQuery.parseJSON(data.responseText);
				error($error);
			});
		});

		$('#mdelMonto').on('show.bs.modal', function (event) {
			var href = "{{ route('tipoSDAT.eliminarCondicionMonto', [$tipo->id, ':montoId']) }}";
			var btn = $(event.relatedTarget);
			var id = btn.data('id');

			var modal = $(this);
			var form = modal.find('form');
			form.attr("action", href.replace(":montoId", id));
		});

		$('#mdelPeriodo').on('show.bs.modal', function (event) {
			var btn = $(event.relatedTarget);
			var dd = btn.data('dd');
			var dh = btn.data('dh');

			var modal = $(this);
			modal.find("input[name='dd']").val(dd);
			modal.find("input[name='dh']").val(dh);
		});
	});
	function error(data) {
		$msg = "";
		$.each(data.errors, function (index, childData) {
			$msg += childData + "<br>";
		});
		$(".text-danger").html($msg);
		$(".text-danger").show();
		$(".text-danger").fadeOut(5000);
	}
</script>
@endpush

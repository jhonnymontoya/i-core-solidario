@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		@if (Session::has('faltantes'))
			<div class="alert alert-warning alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				Por favor revisar lo siguientes antes de contabilizar...<br>
				@foreach(Session::get('faltantes') as $faltante)
					<br>
					<label>{!! $faltante !!}</label>
				@endforeach
			</div>
		@endif
		{!! Form::model($comprobante, ['url' => ['comprobante', $comprobante], 'method' => 'put', 'role' => 'form', 'id' => 'comprobante']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar comprobante</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					{{-- INICIO FILA --}}
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tipo_comprobante_id')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('tipo_comprobante_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo de Comprobante
								</label>
								<div class="col-sm-8">
									{!! Form::text('tipo_comprobante_id', $comprobante->tipoComprobante->nombre_completo, ['class' => 'form-control', 'form' => 'comprobante', 'readonly']) !!}
									@if ($errors->has('tipo_comprobante_id'))
										<span class="help-block">{{ $errors->first('tipo_comprobante_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('fecha_movimiento')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('fecha_movimiento'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha
								</label>
								<div class="col-sm-9">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha_movimiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'form' => 'comprobante', 'readonly']) !!}
									</div>
									@if ($errors->has('fecha_movimiento'))
										<span class="help-block">{{ $errors->first('fecha_movimiento') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row form-horizontal">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="col-sm-2 control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								<div class="col-sm-10">
									{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción', 'form' => 'comprobante']) !!}
									@if ($errors->has('descripcion'))
										<span class="help-block">{{ $errors->first('descripcion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					<div class="row">
						<div class="col-md-12">
							<div class="pull-right">
								<a href="{{ route('comprobante.pagoImpuesto', $comprobante) }}" class="btn bg-purple">Pago impuestos</a>
								<a href="{{ route('comprobante.impuesto', $comprobante) }}" class="btn bg-olive">Agregar impuesto</a>
								<a href="{{ route('comprobante.cargue', $comprobante) }}" class="btn bg-navy">Cargar plano</a>
							</div>
						</div>
					</div>
					<br>
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-12">
							<h4>Registros</h4>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('cuenta'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Cuenta
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-table"></i></span>
									{!! Form::select('cuenta', [], null, ['class' => 'form-control select2', 'form' => 'detalle', 'tabIndex' => '5']) !!}
								</div>
								@if ($errors->has('cuenta'))
									<span class="help-block">{{ $errors->first('cuenta') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tercero'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tercero
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-male"></i></span>
									{!! Form::select('tercero', [], null, ['class' => 'form-control select2', 'form' => 'detalle', 'tabIndex' => '6']) !!}
								</div>
								@if ($errors->has('tercero'))
									<span class="help-block">{{ $errors->first('tercero') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('debito')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('debito'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Débito
								</label>
								<div class="input-group">
									<span class="input-group-addon">$</span>
									{!! Form::text('debito', null, ['class' => 'form-control text-right registros', 'placeholder' => 'Débito', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'false', 'form' => 'detalle', 'tabIndex' => '7']) !!}
								</div>
								@if ($errors->has('debito'))
									<span class="help-block">{{ $errors->first('debito') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('credito')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('credito'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Crédito
								</label>
								<div class="input-group">
									<span class="input-group-addon">$</span>
									{!! Form::text('credito', null, ['class' => 'form-control text-right registros', 'placeholder' => 'Crédito', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'false', 'form' => 'detalle', 'tabIndex' => '8']) !!}
								</div>
								@if ($errors->has('credito'))
									<span class="help-block">{{ $errors->first('credito') }}</span>
								@endif
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-8">
							<div class="form-group {{ ($errors->has('referencia')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('referencia'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Referencia
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-map-marker"></i></span>
									{!! Form::text('referencia', null, ['class' => 'form-control', 'placeholder' => 'Referencia', 'form' => 'detalle', 'tabIndex' => '9', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('referencia'))
									<span class="help-block">{{ $errors->first('referencia') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-1">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<br>
								{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success', 'form' => 'detalle', 'tabIndex' => '10']) !!}
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-12">
							<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO TABLA --}}
					<div style="height: 350px;">
					<table id="tablaRegistros" class="table table-hover table-responsive">
						<thead>
							<tr>
								<th>Registros: <label id="totalRegistros">{{ $detalles->count() }}</label></th>
							</tr>
							<tr>
								<th>Cuenta</th>
								<th>Tercero</th>
								<th>Referencia</th>
								<th class="text-center">Débito</th>
								<th class="text-center">Crédito</th>
								<th></th>
							</tr>
						</thead>
						<tbody id="id_registros">
							@foreach($detalles as $detalle)
								<tr data-id="{{ $detalle->id }}">
									<td>{{ str_limit($detalle->cuif_codigo . ' - ' . $detalle->cuif_nombre, 30) }}</td>
									<td>{{ str_limit($detalle->tercero, 30) }}</td>
									<td>{{ str_limit($detalle->referencia, 30) }}</td>
									<td class="text-right">${{ number_format($detalle->debito, 0) }}</td>
									<td class="text-right">${{ number_format($detalle->credito, 0) }}</td>
									<td class="text-center">
										<a href="#" onclick="javascript:return rowDelete(this);" title="Eliminar" class="btn btn-outline-danger btn-sm">
											<i class="fa fa-trash"></i>
										</a>
									</td>
								</tr>
							@endforeach
						</tbody>
					</table>
					</div>
					<br>
					<table class="table table-responsive pull-right" style="width: 50%;">
						<thead>
							<tr>
								<th colspan="2" class="text-right">Débitos</th>
								<th class="text-right">Créditos</th>
								<th class="text-center">Diferencia</th>
							</tr>
							<tr>
								<th class="text-right">Totales generales</th>
								<th class="text-right" id="totalDebitos">${{ number_format($comprobante->debitos, 0) }}</th>
								<th class="text-right" id="totalCreditos">${{ number_format($comprobante->creditos, 0) }}</th>
								<th class="text-right" id="totalDiferencia">${{ number_format($comprobante->debitos - $comprobante->creditos, 0) }}</th>
							</tr>
						</thead>
					</table>
					{{-- FIN TABLA --}}
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success', 'form' => 'comprobante']) !!}
					<a href="{{ route('comprobanteContabilizar', $comprobante) }}" class="btn btn-outline-info">Contabilizar</a>
					<a href="{{ url('comprobante') }}" class="btn btn-outline-danger pull-right">Volver</a>
				</div>
			</div>
		</div>
		{!! Form::close() !!}

		{!! Form::open(['url' => '#', 'method' => 'put', 'role' => 'form', 'id' => 'detalle']) !!}
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.ui-menu{
		background-color: #fff;
		border: 0.1em solid #aaa;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='tipo_comprobante_id']").selectAjax("{{ url('api/tipoComprobante') }}", {entidad: {{ Auth::getSession()->get('entidad')->id }}, id:"{{ $comprobante->tipo_comprobante_id | old('tipo_comprobante_id') }}"});
		$("select[name='cuenta']").selectAjax("{{ url('cuentaContable/cuentaContable') }}");

		$("select[name='tercero']").select2({
			allowClear: true,
			placeholder: "Seleccione un tercero",
			ajax: {
				url: "{{ url('tercero/getTerceroConParametros') }}",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						estado: 'ACTIVO'
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

		@if(!empty(old('tercero')))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ old('tercero') }} }}).done(function(data){
				if(data.total_count == 1)  {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tercero']"));
					$("select[name='tercero']").val(element.id).trigger("change");
				}
			});
		@endif

		$("form[id='detalle']").on("submit", function(event){
			event.preventDefault();
			$("input[name='debito']").maskMoney('unmask');
			$("input[name='credito']").maskMoney('unmask');
			var $data = $("#detalle").serialize();
			$("input[name='debito']").maskMoney('mask');
			$("input[name='credito']").maskMoney('mask');

			$.ajax({
					url: "{{ route('comprobanteEditDetalle', $comprobante->id) }}",
					method: 'PUT',
					dataType: 'json',
					data: $data
			}).done(function(data){
				var $data = data;
				$("#detalle")[0].reset();
				$("select[name='cuenta']").val('').trigger('change');
				$("select[name='tercero']").val('').trigger('change');

				var $fila = $("<tr data-id='" + $data.item.id + "'></tr>")	;
				$fila.append("<td>" + $data.item.cuenta + "</td>");
				$fila.append("<td>" + $data.item.tercero + "</td>");
				$fila.append("<td>" + $data.item.referencia + "</td>");
				$fila.append("<td class='text-right'>$" + $data.item.debito + "</td>");
				$fila.append("<td class='text-right'>$" + $data.item.credito + "</td>");
				$fila.append("<td class='text-center'><a href='#' onclick='javascript:return rowDelete(this);' title='Eliminar' class='btn btn-outline-danger btn-sm'><i class='fa fa-trash'></i></a></td>");
				$('#tablaRegistros').DataTable().destroy();
				$("#id_registros").prepend($fila);
				$('#tablaRegistros').DataTable({"scrollY": '340px', "scrollCollapse": true, "paging": false, "ordering": false, "info": false, "searching": false});


				$("#totalDebitos").text("$" + $data.debitos);
				$("#totalCreditos").text("$" + $data.creditos);
				$("#totalDiferencia").text("$" + $data.diferencia);
				$("#totalRegistros").text($data.registros);
			}).fail(function(data){
				var $error = data.responseJSON.errors;
				error($error);
			});
		});

		function error(data)
		{
			$msg = "";
			$.each(data, function (key, subData){
				$.each(subData, function (index, childData) {
					$msg += childData + "<br>";
				});
			})

			$("#error").html($msg);
			$("#error").show();
			$("#error").fadeOut(5000);
		}
		$("input[name='referencia']").autocomplete({
			source: "referencia",
			minLength: 2
		});

		$('#tablaRegistros').DataTable({
			"scrollY": '350px',
			"scrollCollapse": true,
			"paging": false,
			"ordering": false,
			"info": false,
			"autoWidth": false,
			"searching": false
		});
	});

	function rowDelete(row){
		row = $(row).parents('tr');
		row.fadeOut(1000);
		var $data = "_token={{ csrf_token() }}&id=" + row.data('id');
		
		$.ajax({
				url: "{{ route('comprobanteDeleteDetalle', $comprobante->id) }}",
				method: 'DELETE',
				dataType: 'json',
				data: $data
		}).done(function(data){
			var $data = data;
			$("#totalDebitos").text("$" + $data.debitos);
			$("#totalCreditos").text("$" + $data.creditos);
			$("#totalDiferencia").text("$" + $data.diferencia);
			$("#totalRegistros").text($data.registros);
			row.remove();
		}).fail(function(data){
			var $error = jQuery.parseJSON(data.responseText);
			row.fadeIn(1000);
			error($error);
		});

		return false;
	}

	function error(data) {
		$msg = "";
		$.each(data, function (key, subData){
			$.each(subData, function (index, childData) {
				$msg += childData + "<br>";
			});
		})

		$("#error").html($msg);
		$("#error").show();
		$("#error").fadeOut(5000);
	}
</script>
@endpush

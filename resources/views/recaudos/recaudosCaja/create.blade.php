@extends('layouts.admin')
@section('content')
@php
	$fecha = Request::has('fecha') ? Request::get('fecha') : date('d/m/Y');
	$fecha = empty($fecha) ? date('d/m/Y') : $fecha;
	$fecha = \Carbon\Carbon::createFromFormat("d/m/Y", $fecha)->startOfDay();
@endphp
{{-- Modal de ahorros --}}
@component('recaudos.recaudosCaja.componentes.modales.ahorros')
@endcomponent

{{-- Modal de créditos --}}
@component('recaudos.recaudosCaja.componentes.modales.creditos')
@endcomponent

@if ($tercero)
	{{-- Modal de resumen --}}
	@component('recaudos.recaudosCaja.componentes.modales.resumen', ['totalAhorros' => $totalAhorros, 'tercero' => $tercero, 'socio' => $socio, 'cuenta' => $cuenta, 'fecha' => $fecha])
	@endcomponent
@endif
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Recaudos por caja
			<small>Recaudos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Recaudos</a></li>
			<li class="active">Recaudos por caja</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Recaudo por caja</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('tercero', 'fecha', 'cuenta'), ['url' => 'recaudosCaja/create', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('tercero'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione tercero
							</label>
							{!! Form::select('tercero', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
							@if ($errors->has('tercero'))
								<span class="help-block">{{ $errors->first('tercero') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('cuenta'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione cuenta
							</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-table"></i>
								</div>
								{!! Form::select('cuenta', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
							</div>
							@if ($errors->has('cuenta'))
								<span class="help-block">{{ $errors->first('cuenta') }}</span>
							@endif								
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('fecha')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('fecha'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha
							</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								{!! Form::text('fecha', $fecha, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
							</div>
							@if ($errors->has('fecha'))
								<span class="help-block">{{ $errors->first('fecha') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-1 col-sm-12">
						<label class="control-label">&nbsp;</label><br>
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>

				@if ($tercero)
					<br>
					<div class="row">
						<div class="col-md-8">
							<strong>{{ $tercero->nombre_completo }}</strong>
							@if(!is_null($socio) && $socio->estado != 'ACTIVO')
								<span class="label label-warning">SOCIO NO ACTIVO</span>
							@endif
							@if(is_null($socio))
								<span class="label label-default">TERCERO NO ASOCIADO</span>
							@endif
							@if ($cuenta)
								<br>
								{{ $cuenta->codigo }} - {{ $cuenta->nombre }}
							@endif
							<br>
							Fecha recaudo: {{ $fecha }}
							<br>
							@php
								$pagaduria = optional($socio)->pagaduria;
							@endphp
							{{ optional($pagaduria)->nombre }}
						</div>
						<div class="col-md-4 col-sm-12">
							<h1 class="text-primary total">Total: $0</h1>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<a class="btn btn-success pull-right" data-toggle="modal" data-target="#mResumen">Continuar</a>
						</div>
					</div>
					<br>
					@if ($errors->has('data'))
						<p class="text-danger"><i class="fa fa-warning"></i> {{ $errors->first('data') }}</p>
					@endif
					<h3>Ahorros</h3>
					{{-- Componente de ahorros --}}
					@component('recaudos.recaudosCaja.componentes.ahorros', ['ahorros' => $ahorros])
					@endcomponent

					<h3>Créditos</h3>
					{{-- Componente de créditos --}}
					@component('recaudos.recaudosCaja.componentes.creditos', ['creditos' => $creditos, 'fecha' => $fecha])
					@endcomponent
				@else
					<br>
				@endif
			</div>
			<div class="box-footer">
				@if ($tercero)
					<a class="btn btn-success" data-toggle="modal" data-target="#mResumen">Continuar</a>
					<a href="{{ url('recaudosCaja') }}" class="btn btn-danger pull-right">Cancelar</a>
				@else
					<a href="{{ url('recaudosCaja') }}" class="btn btn-danger pull-right">Volver</a>
				@endif
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
	var data = new Object();
	data.ahorros = [];
	data.creditos = [];
	data.totalRecaudo = 0;
	$(function(){
		$("select[name='tercero']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('tercero/getTerceroConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						tipo: 'NATURAL'
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

		@if(Request::has('tercero') && !empty(Request::get('tercero')))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ Request::get('tercero') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='tercero']"));
					$("select[name='tercero']").val(element.id).trigger("change");
				}
			});
		@endif

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
						modulo: '1',
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

		@if(Request::has('cuenta') && !empty(Request::get('cuenta')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ Request::get('cuenta') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta']"));
					$("select[name='cuenta']").val(element.id).trigger("change");
				}
			});
		@endif
	});
	function actualizar() {
		var total = 0;
		data.ahorros.forEach(function(ahorro){
			total += ahorro.valor;
		});
		data.creditos.forEach(function(ahorro){
			total += ahorro.total;
		});
		data.totalRecaudo = total;
		$(".total").text("Total: $" + $().formatoMoneda(total));
	}
	<?php
		if(!empty(old("data"))) {
			$data = str_replace('"visible":true', '"visible":false', old("data"));
			?>
			data = JSON.parse('{!! $data !!}');
			actualizarAhorros();
			actualizarCreditos();
			<?php
		}		
	?>
</script>
@endpush
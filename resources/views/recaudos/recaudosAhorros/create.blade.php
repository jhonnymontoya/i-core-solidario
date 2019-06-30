@extends('layouts.admin')
@section('content')
@php
	$fecha = Request::has('fecha') ? Request::get('fecha') : date('d/m/Y');
	$fecha = empty($fecha) ? date('d/m/Y') : $fecha;
	$fecha = \Carbon\Carbon::createFromFormat("d/m/Y", $fecha)->startOfDay();
@endphp
{{-- Modal de ahorros --}}
@component('recaudos.recaudosAhorros.componentes.modales.ahorros')
@endcomponent

{{-- Modal de créditos --}}
@component('recaudos.recaudosAhorros.componentes.modales.creditos')
@endcomponent

@if ($tercero)
	{{-- Modal de resumen --}}
	@component('recaudos.recaudosAhorros.componentes.modales.resumen', ['totalAhorros' => $totalAhorros, 'tercero' => $tercero, 'socio' => $socio, 'modalidad' => $modalidad, 'fecha' => $fecha])
	@endcomponent
@endif
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Abono con ahorros
			<small>Recaudos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Recaudos</a></li>
			<li class="active">Abono con ahorros</li>
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
				<h3 class="box-title">Abonos con ahorros</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('socio', 'fecha', 'modalidad'), ['url' => 'recaudosAhorros/create', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('socio')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('socio'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione socio
							</label>
							{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
							@if ($errors->has('socio'))
								<span class="help-block">{{ $errors->first('socio') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('modalidad')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('modalidad'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione modalidad
							</label>
							{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione modalidad']) !!}
							@if ($errors->has('modalidad'))
								<span class="help-block">{{ $errors->first('modalidad') }}</span>
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
							<br>
							Fecha recaudo: {{ $fecha }}
							<br>
							@php
								$pagaduria = optional($socio)->pagaduria;
							@endphp
							{{ optional($pagaduria)->nombre }}
							@if ($modalidad)
								<br><br>
								{{ $modalidad->codigo }} - {{ $modalidad->nombre }}
								<br>
								<strong>Saldo:</strong> ${{ number_format($modalidad->saldo) }}
							@endif
							<br>
							<strong>GMF:</strong> <span class="gmf">$0</span>
						</div>
						<div class="col-md-4 col-sm-12">
							<h1 class="text-primary total">Total: $0</h1>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<a class="btn btn-success pull-right continuar" data-toggle="modal" data-target="#mResumen">Continuar</a>
						</div>
					</div>
					<br>
					@if ($errors->has('data'))
						<p class="text-danger"><i class="fa fa-warning"></i> {{ $errors->first('data') }}</p>
					@endif
					<h3>Ahorros</h3>
					{{-- Componente de ahorros --}}
					@component('recaudos.recaudosAhorros.componentes.ahorros', ['ahorros' => $ahorros])
					@endcomponent

					<h3>Créditos</h3>
					{{-- Componente de créditos --}}
					@component('recaudos.recaudosAhorros.componentes.creditos', ['creditos' => $creditos, 'fecha' => $fecha])
					@endcomponent
				@else
					<br>
				@endif
			</div>
			<div class="box-footer">
				@if ($tercero)
					<a class="btn btn-success continuar" data-toggle="modal" data-target="#mResumen">Continuar</a>
					<a href="{{ url('recaudosAhorros') }}" class="btn btn-danger pull-right">Cancelar</a>
				@else
					<a href="{{ url('recaudosAhorros') }}" class="btn btn-danger pull-right">Volver</a>
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
		$('.select2').select2();
		$("select[name='socio']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('socio/getSocioConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
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

		@if(Request::has('socio') && !empty(Request::get('socio')))
			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ Request::get('socio') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio']"));
					$("select[name='socio']").val(element.id).trigger("change");
				}
			});
		@endif
	});
	function actualizar() {
		var saldo = {{ is_null($modalidad) ? 0 : $modalidad->saldo }};
		var total = 0;
		var totalCreditos = 0;
		data.ahorros.forEach(function(ahorro){
			total += ahorro.valor;
		});
		data.creditos.forEach(function(ahorro){
			total += ahorro.total;
			totalCreditos += ahorro.total;
		});
		var gmf = Math.round((totalCreditos * 4) / 1000);
		data.GMF = gmf;
		data.totalRecaudo = total + gmf;
		$(".total").text("Total: $" + $().formatoMoneda(data.totalRecaudo));
		$(".gmf").text("$" + $().formatoMoneda(gmf));
		if(data.totalRecaudo > saldo) {
			$(".total").removeClass('text-primary');
			$(".total").addClass('text-danger');
			$(".continuar").addClass("disabled");
		}
		else {
			$(".total").addClass('text-primary');
			$(".total").removeClass('text-danger');
			$(".continuar").removeClass("disabled");
		}
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
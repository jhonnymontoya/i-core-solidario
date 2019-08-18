@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			SDAT
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">SDAT</li>
		</ol>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has("error"))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get("error") }}</p>
			</div>
		@endif
		{!! Form::open(['route' => ['SDAT.put.preSaldar', $sdat->id], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Saldar SDAT</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<h4>Confirmar devolución de depósito</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<dl class="dl-horizontal">
									<dt>No. deposito:</dt>
									<dd>{{ $sdat->id }}</dd>

									<dt>Tipo:</dt>
									<dd>{{ $sdat->tipoSdat->codigo }}</dd>

									<dt>Valor contituido:</dt>
									<dd>${{ number_format($sdat->valor) }}</dd>

									<dt>Tasa E.A.:</dt>
									<dd>{{ number_format($sdat->tasa, 2) }}%</dd>

									<dt>Fecha constitución:</dt>
									<dd>{{ $sdat->fecha_constitucion }} ({{ $sdat->fecha_constitucion->diffForHumans() }})</dd>

									<dt>Plazo (días):</dt>
									<dd>{{ number_format($sdat->plazo) }}</dd>

									<dt>Fecha vencimiento:</dt>
									<dd>{{ $sdat->fecha_vencimiento }} ({{ $sdat->fecha_vencimiento->diffForHumans() }})</dd>
									@php
										$tercero = $sdat->socio->tercero;
										$nombre = sprintf(
											"%s %s - %s",
											$tercero->tipoIdentificacion->codigo,
											$tercero->numero_identificacion,
											$tercero->nombre_corto
										);
									@endphp

									<dt>Nombre:</dt>
									<dd>{{ $nombre }}</dd>

								</dl>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fechaDevolucion')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fechaDevolucion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha devolución
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										@php
											$fechaDevolucion = old('fechaDevolucion');
											$fechaDevolucion = empty($fechaDevolucion) ? date('d/m/Y') : $fechaDevolucion;
										@endphp
										{!! Form::text('fechaDevolucion', $fechaDevolucion, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fechaDevolucion'))
										<span class="help-block">{{ $errors->first('fechaDevolucion') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuenta'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('cuenta', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('cuenta'))
										<span class="help-block">{{ $errors->first('cuenta') }}</span>
									@endif
								</div>
							</div>
						</div>

					</div>
					<div class="card-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('SDAT') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
				</div>
			</div>
		</div>
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
						tipoCuenta: 'AUXILIAR',
						estado: 1,
						modulo: '2,1',
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

		@if(!empty(old('cuenta')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta') }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta']"));
					$("select[name='cuenta']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

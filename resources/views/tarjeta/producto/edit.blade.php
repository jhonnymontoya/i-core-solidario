@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Producto
			<small>Tarjeta</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Tarjeta</a></li>
			<li class="active">Producto</li>
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
		{!! Form::model($producto, ['url' => ['tarjetaProducto', $producto], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Editar producto</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'readonly']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('convenio')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('convenio'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número de convenio
									</label>
									{!! Form::text('convenio', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de convenio', 'readonly']) !!}
									@if ($errors->has('convenio'))
										<span class="help-block">{{ $errors->first('convenio') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('cuenta_compensacion_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuenta_compensacion_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta de compensación
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('cuenta_compensacion_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('cuenta_compensacion_cuif_id'))
										<span class="help-block">{{ $errors->first('cuenta_compensacion_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<?php
									$credito = $producto->credito;
									$ahorro = $producto->ahorro;
									$vista = $producto->vista;

									$credito = is_null(old("credito")) ? $credito : old("credito");
									$ahorro = is_null(old("ahorro")) ? $ahorro : old("ahorro");
									$vista = is_null(old("vista")) ? $vista : old("vista");

									$error = $errors->has('credito') or
										$errors->has('ahorro') or
										$errors->has('vista');

									$men = $errors->first('credito');
								?>
								<div class="form-group {{ ($error?'has-error':'') }}">
									<label class="control-label">
										@if ($error)
											<i class="fa fa-times-circle-o"></i>
										@endif
										Modalidad de producto
									</label>
									<div>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary {{ $credito ? 'active' : '' }}">
												{!! Form::checkbox('credito', '1', $credito) !!}Crédito
											</label>
											<label class="btn btn-primary {{ $ahorro ? 'active' : '' }}">
												{!! Form::checkbox('ahorro', '1', $ahorro) !!}Cuenta de ahorros
											</label>
											<label class="btn btn-primary {{ $vista ? 'active' : '' }}">
												{!! Form::checkbox('vista', '1', $vista) !!}Vista
											</label>
										</div>
										@if ($error)
											<span class="help-block">{{ $men }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-3 modalidadCredito" style="display:{{ ($credito ? 'block' : 'none') }};">
								<div class="form-group {{ ($errors->has('modalidad_credito_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('modalidad_credito_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Modalidad de crédito
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-money"></i></span>
										{!! Form::select('modalidad_credito_id', $modalidades, null, ['class' => 'form-control select2', 'style' => 'width:100%;', 'placeholder' => 'Seleccione una opción']) !!}
									</div>
									@if ($errors->has('modalidad_credito_id'))
										<span class="help-block">{{ $errors->first('modalidad_credito_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('tipo_pago_cuota_manejo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipo_pago_cuota_manejo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Forma pago cuota de manejo
									</label>
									<div>
										@php
											$tipoPagoCuotaManejo = empty(old('tipo_pago_cuota_manejo')) ? $producto->tipo_pago_cuota_manejo : old('tipo_pago_cuota_manejo');
											switch ($tipoPagoCuotaManejo) {
												case 'ANTICIPADO':
													$tipoPagoCuotaManejo = 'A';
													break;
												case 'VENCIDO':
													$tipoPagoCuotaManejo = 'B';
													break;
												default:
													$tipoPagoCuotaManejo = 'B';
													break;
											}
										@endphp
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary disabled {{ $tipoPagoCuotaManejo == 'A' ? 'active' : '' }}">
												{!! Form::radio('tipo_pago_cuota_manejo', 'ANTICIPADO', $tipoPagoCuotaManejo == 'A' ? true : false) !!}Anticipado
											</label>
											<label class="btn btn-primary {{ $tipoPagoCuotaManejo == 'B' ? 'active' : '' }}">
												{!! Form::radio('tipo_pago_cuota_manejo', 'VENCIDO', $tipoPagoCuotaManejo == 'B' ? true : false ) !!}Vencido
											</label>
										</div>
										@if ($errors->has('tipo_pago_cuota_manejo'))
											<span class="help-block">{{ $errors->first('tipo_pago_cuota_manejo') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('valor_cuota_manejo_mes')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('valor_cuota_manejo_mes'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Valor cuota manejo mes
									</label>
									<div>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{!! Form::text('valor_cuota_manejo_mes', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Sueldo mensual', 'data-maskMoney']) !!}
										</div>
										@if ($errors->has('valor_cuota_manejo_mes'))
											<span class="help-block">{{ $errors->first('valor_cuota_manejo_mes') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('periodicidad_cuota_manejo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('periodicidad_cuota_manejo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Periodicidad pago cuota manejo
									</label>
									{!! Form::select('periodicidad_cuota_manejo', $periodicidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una opción']) !!}
									@if ($errors->has('periodicidad_cuota_manejo'))
										<span class="help-block">{{ $errors->first('periodicidad_cuota_manejo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('meses_sin_cuota_manejo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('meses_sin_cuota_manejo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Meses sin cuota de manejo
									</label>
									<div>
										{!! Form::number('meses_sin_cuota_manejo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Meses sin cuota de manejo']) !!}
										@if ($errors->has('meses_sin_cuota_manejo'))
											<span class="help-block">{{ $errors->first('meses_sin_cuota_manejo') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('cuota_manejo_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuota_manejo_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta contable cuota de manejo
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('cuota_manejo_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('cuota_manejo_cuif_id'))
										<span class="help-block">{{ $errors->first('cuota_manejo_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('numero_retiros_sin_cobro_red')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('numero_retiros_sin_cobro_red'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Retiros sin cobro en red propia (mes)
									</label>
									<div>
										{!! Form::number('numero_retiros_sin_cobro_red', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Retiros sin cobro en red propia (mes)']) !!}
										@if ($errors->has('numero_retiros_sin_cobro_red'))
											<span class="help-block">{{ $errors->first('numero_retiros_sin_cobro_red') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('numero_retiros_sin_cobro_otra_red')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('numero_retiros_sin_cobro_otra_red'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Retiros sin cobro en otras redes (mes)
									</label>
									<div>
										{!! Form::number('numero_retiros_sin_cobro_otra_red', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Retiros sin cobro en otras redes (mes)']) !!}
										@if ($errors->has('numero_retiros_sin_cobro_otra_red'))
											<span class="help-block">{{ $errors->first('numero_retiros_sin_cobro_otra_red') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('ingreso_comision_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('ingreso_comision_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta ingreso comisión
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('ingreso_comision_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('ingreso_comision_cuif_id'))
										<span class="help-block">{{ $errors->first('ingreso_comision_cuif_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('egreso_comision_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('egreso_comision_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta egreso comisión
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('egreso_comision_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('egreso_comision_cuif_id'))
										<span class="help-block">{{ $errors->first('egreso_comision_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('esta_activo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Activo?
									</label>
									<div>
										@php
											$estaActivo = empty(old('esta_activo')) ? $producto->esta_activo : old('esta_activo');
										@endphp
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
												{!! Form::radio('esta_activo', 1, $estaActivo ? true : false) !!}Activo
											</label>
											<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
												{!! Form::radio('esta_activo', 0, !$estaActivo ? true : false ) !!}Inactivo
											</label>
										</div>
										@if ($errors->has('esta_activo'))
											<span class="help-block">{{ $errors->first('esta_activo') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tarjetaProducto') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$(window).load(function(){
			$("input[name='valor_cuota_manejo_mes']").maskMoney('mask');
		});
		$("select[name='modalidad_credito_id']").select2();
		$("select[name='cuenta_compensacion_cuif_id']").select2({
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
		@php
			$cuentaCompensacionCuifId = empty(old('cuenta_compensacion_cuif_id')) ? $producto->cuenta_compensacion_cuif_id : old('cuenta_compensacion_cuif_id');
		@endphp
		@if(!empty($cuentaCompensacionCuifId))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $cuentaCompensacionCuifId }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta_compensacion_cuif_id']"));
					$("select[name='cuenta_compensacion_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='ingreso_comision_cuif_id']").select2({
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
		@php
			$ingresoComisionCuifId = empty(old('ingreso_comision_cuif_id')) ? $producto->ingreso_comision_cuif_id : old('ingreso_comision_cuif_id');
		@endphp
		@if(!empty($ingresoComisionCuifId))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $ingresoComisionCuifId }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='ingreso_comision_cuif_id']"));
					$("select[name='ingreso_comision_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='egreso_comision_cuif_id']").select2({
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
		@php
			$egresoComisionCuifId = empty(old('egreso_comision_cuif_id')) ? $producto->egreso_comision_cuif_id : old('egreso_comision_cuif_id');
		@endphp
		@if(!empty($egresoComisionCuifId))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $egresoComisionCuifId }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='egreso_comision_cuif_id']"));
					$("select[name='egreso_comision_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='cuota_manejo_cuif_id']").select2({
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
		@php
			$cuotaManejoCuifId = empty(old('cuota_manejo_cuif_id')) ? $producto->cuota_manejo_cuif_id : old('cuota_manejo_cuif_id');
		@endphp
		@if(!empty($cuotaManejoCuifId))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $cuotaManejoCuifId }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuota_manejo_cuif_id']"));
					$("select[name='cuota_manejo_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("input[name='credito']").change(function(event){
			var $valor = this.checked;
			var $modalidadCredito = $(".modalidadCredito");
			if($valor == false) {
				$modalidadCredito.find("select[name='modalidad_credito_id']").val(null).trigger('change');
				$modalidadCredito.hide(100);
			}
			else {
				$modalidadCredito.show(100);
			}
		});
	});
</script>
@endpush

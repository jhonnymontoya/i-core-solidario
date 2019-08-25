@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Producto
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Producto</li>
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
		{!! Form::model($producto, ['url' => ['tarjetaProducto', $producto], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar producto</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
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
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('convenio') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Número de convenio</label>
								{!! Form::text('convenio', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de convenio', 'readonly']) !!}
								@if ($errors->has('convenio'))
									<div class="invalid-feedback">{{ $errors->first('convenio') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('cuenta_compensacion_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta de compensación</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('cuenta_compensacion_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('cuenta_compensacion_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('cuenta_compensacion_cuif_id') }}</div>
									@endif
								</div>
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
							<div class="form-group">
								@php
									$valid = $errors->has('variable') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Modalidad de producto</label>
								<div>
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-sm btn-primary {{ $credito ? 'active' : '' }}">
											{!! Form::checkbox('credito', '1', $credito, ['class' => [$valid]]) !!}Crédito
										</label>
										<label class="btn btn-sm btn-primary {{ $ahorro ? 'active' : '' }}">
											{!! Form::checkbox('ahorro', '1', $ahorro, ['class' => [$valid]]) !!}Cuenta de ahorros
										</label>
										<label class="btn btn-sm btn-primary {{ $vista ? 'active' : '' }}">
											{!! Form::checkbox('vista', '1', $vista, ['class' => [$valid]]) !!}Vista
										</label>
									</div>
									@if ($error)
										<div class="invalid-feedback">{{ $errors->first('variable') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-3 modalidadCredito" style="display:{{ ($credito ? 'block' : 'none') }};">
							<div class="form-group">
								@php
									$valid = $errors->has('modalidad_credito_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Modalidad de crédito</label>
								{!! Form::select('modalidad_credito_id', $modalidades, null, ['class' => [$valid, 'form-control', 'select2'], 'style' => 'width:100%;', 'placeholder' => 'Seleccione una opción']) !!}
								@if ($errors->has('modalidad_credito_id'))
									<div class="invalid-feedback">{{ $errors->first('modalidad_credito_id') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_pago_cuota_manejo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Pago cuota manejo</label>
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
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-sm btn-primary disabled {{ $tipoPagoCuotaManejo == 'A' ? 'active' : '' }}">
											{!! Form::radio('tipo_pago_cuota_manejo', 'ANTICIPADO', ($tipoPagoCuotaManejo == 'A' ? true : false), ['class' => [$valid]]) !!}Anticipado
										</label>
										<label class="btn btn-sm btn-primary {{ $tipoPagoCuotaManejo == 'B' ? 'active' : '' }}">
											{!! Form::radio('tipo_pago_cuota_manejo', 'VENCIDO', ($tipoPagoCuotaManejo == 'B' ? true : false), ['class' => [$valid]]) !!}Vencido
										</label>
									</div>
									@if ($errors->has('tipo_pago_cuota_manejo'))
										<div class="invalid-feedback">{{ $errors->first('tipo_pago_cuota_manejo') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('valor_cuota_manejo_mes') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor cuota manejo mes</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('valor_cuota_manejo_mes', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor cuota manejo mes', 'data-maskMoney']) !!}
									@if ($errors->has('valor_cuota_manejo_mes'))
										<div class="invalid-feedback">{{ $errors->first('valor_cuota_manejo_mes') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('periodicidad_cuota_manejo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Periodicidad pago cuota manejo</label>
								{!! Form::select('periodicidad_cuota_manejo', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
								@if ($errors->has('periodicidad_cuota_manejo'))
									<div class="invalid-feedback">{{ $errors->first('periodicidad_cuota_manejo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('meses_sin_cuota_manejo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Meses sin cuota de manejo</label>
								{!! Form::number('meses_sin_cuota_manejo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Meses sin cuota de manejo']) !!}
								@if ($errors->has('meses_sin_cuota_manejo'))
									<div class="invalid-feedback">{{ $errors->first('meses_sin_cuota_manejo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('cuota_manejo_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta contable cuota de manejo</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('cuota_manejo_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('cuota_manejo_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('cuota_manejo_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('numero_retiros_sin_cobro_red') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Retiros sin cobro en red propia (mes)</label>
								{!! Form::number('numero_retiros_sin_cobro_red', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Retiros sin cobro en red propia (mes)']) !!}
								@if ($errors->has('numero_retiros_sin_cobro_red'))
									<div class="invalid-feedback">{{ $errors->first('numero_retiros_sin_cobro_red') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('numero_retiros_sin_cobro_otra_red') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Retiros sin cobro en otras redes (mes)</label>
								{!! Form::number('numero_retiros_sin_cobro_otra_red', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Retiros sin cobro en otras redes (mes)']) !!}
								@if ($errors->has('numero_retiros_sin_cobro_otra_red'))
									<div class="invalid-feedback">{{ $errors->first('numero_retiros_sin_cobro_otra_red') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('ingreso_comision_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta ingreso comisión</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('ingreso_comision_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('ingreso_comision_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('ingreso_comision_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('egreso_comision_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta egreso comisión</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('egreso_comision_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('egreso_comision_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('egreso_comision_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Activo?</label>
								<div>
									@php
										$valid = $errors->has('esta_activo') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activo')) ? $producto->esta_activo : old('esta_activo');
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
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tarjetaProducto') }}" class="btn btn-outline-danger">Cancelar</a>
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

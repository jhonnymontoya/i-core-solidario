@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos de ahorros
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">Tipos de ahorros</li>
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
		{!! Form::open(['url' => 'tipoCuotaAhorros', 'method' => 'post', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Crear nuevo tipo de ahorro</h3>
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
									{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'autofocus']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
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
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta contable capital
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('cuif_id'))
										<span class="help-block">{{ $errors->first('cuif_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('tipo_ahorro')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipo_ahorro'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de ahorro
									</label>
									{!! Form::select('tipo_ahorro', ['VOLUNTARIO' => 'Voluntario', 'PROGRAMADO' => 'Programado'], null, ['class' => 'form-control select2']) !!}
									@if ($errors->has('tipo_ahorro'))
										<span class="help-block">{{ $errors->first('tipo_ahorro') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('tasa')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tasa'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tasa E.A.
									</label>
									<div class="input-group">
										{!! Form::text('tasa', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tasa']) !!}
										<span class="input-group-addon">%</span>
									</div>
									@if ($errors->has('tasa'))
										<span class="help-block">{{ $errors->first('tasa') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('periodicidad_interes')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('periodicidad_interes'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Periodicidad causación de intereses
									</label>
									{{--
										Se deja activa solamente la periodicidad mensual y diaria
										['DIARIO' => 'Diario', 'SEMANAL' => 'Semanal', 'DECADAL' => 'Decadal', 'CATORCENAL' => 'Catorcenal', 'QUINCENAL' => 'Quincenal', 'MENSUAL' => 'Mensual', 'BIMESTRAL' => 'Bimestral', 'TRIMESTRAL' => 'Trimestral', 'CUATRIMESTRAL' => 'Cuatrimestral', 'SEMESTRAL' => 'Semestral', 'ANUAL' => 'Anual']
									--}}
									{!! Form::select('periodicidad_interes', ['DIARIO' => 'Diario', 'MENSUAL' => 'Mensual'], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una opción']) !!}
									@if ($errors->has('periodicidad_interes'))
										<span class="help-block">{{ $errors->first('periodicidad_interes') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('capitalizacion_simultanea')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('capitalizacion_simultanea'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Capitalización simultánea
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$capitalizacion = trim(old('capitalizacion_simultanea')) == '' ? '0' : old('capitalizacion_simultanea');
											$capitalizacion = $capitalizacion == '0' ? false : true;
										?>
										<label class="btn btn-primary {{ $capitalizacion ? 'active' : '' }}">
											{!! Form::radio('capitalizacion_simultanea', '1', $capitalizacion ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$capitalizacion ? 'active' : '' }}">
											{!! Form::radio('capitalizacion_simultanea', '0', !$capitalizacion ? true : false) !!}No
										</label>
									</div>
									@if ($errors->has('capitalizacion_simultanea'))
										<span class="help-block">{{ $errors->first('capitalizacion_simultanea') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('paga_retiros')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('paga_retiros'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Calcular intereses al retiro
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$capitalizacion = trim(old('paga_retiros')) == '' ? '0' : old('paga_retiros');
											$capitalizacion = $capitalizacion == '0' ? false : true;
										?>
										<label class="btn btn-primary {{ $capitalizacion ? 'active' : '' }}">
											{!! Form::radio('paga_retiros', '1', $capitalizacion ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$capitalizacion ? 'active' : '' }}">
											{!! Form::radio('paga_retiros', '0', !$capitalizacion ? true : false) !!}No
										</label>
									</div>
									@if ($errors->has('paga_retiros'))
										<span class="help-block">{{ $errors->first('paga_retiros') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('intereses_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('intereses_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta contable intereses
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
										Cuenta contable intereses por pagar
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

							<div class="form-group {{ ($errors->has('paga_intereses_retirados')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('paga_intereses_retirados'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Incluye retirados en el cálculo de rendimientos
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<?php
										$pagaInteresRetiro = trim(old('paga_intereses_retirados')) == '' ? '0' : old('paga_intereses_retirados');
										$pagaInteresRetiro = $pagaInteresRetiro == '0' ? false : true;
									?>
									<label class="btn btn-primary {{ $pagaInteresRetiro ? 'active' : '' }}">
										{!! Form::radio('paga_intereses_retirados', '1', $pagaInteresRetiro ? true : false) !!}Sí
									</label>
									<label class="btn btn-danger {{ !$pagaInteresRetiro ? 'active' : '' }}">
										{!! Form::radio('paga_intereses_retirados', '0', !$pagaInteresRetiro ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('paga_intereses_retirados'))
									<span class="help-block">{{ $errors->first('paga_intereses_retirados') }}</span>
								@endif
							</div>
						</div>

						<div class="row" style="display:none;" id="complementoProgramado">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('tipo_vencimiento')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipo_vencimiento'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de vencimiento
									</label>
									{!! Form::select('tipo_vencimiento', ['COLECTIVO' => 'Colectivo', 'INDIVIDUAL' => 'Individual'], null, ['class' => 'form-control select2']) !!}
									@if ($errors->has('tipo_vencimiento'))
										<span class="help-block">{{ $errors->first('tipo_vencimiento') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div id="colectivo" class="form-group {{ ($errors->has('fecha_vencimiento_colectivo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fecha_vencimiento_colectivo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha de vencimiento
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha_vencimiento_colectivo', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_vencimiento_colectivo'))
										<span class="help-block">{{ $errors->first('fecha_vencimiento_colectivo') }}</span>
									@endif
								</div>
								<div id="individual" style="display:none;" class="form-group {{ ($errors->has('plazo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('plazo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Meses de plazo
									</label>
									{!! Form::number('plazo', null, ['class' => 'form-control pull-right', 'placeholder' => 'Meses de plazo', 'autocomplete' => 'off']) !!}
									@if ($errors->has('plazo'))
										<span class="help-block">{{ $errors->first('plazo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('tasa_penalidad')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tasa_penalidad'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tasa penalidad E.A.
									</label>
									<div class="input-group">
										{!! Form::text('tasa_penalidad', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tasa penalidad']) !!}
										<span class="input-group-addon">%</span>
									</div>
									@if ($errors->has('tasa_penalidad'))
										<span class="help-block">{{ $errors->first('tasa_penalidad') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('penalidad_por_retiro')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('penalidad_por_retiro'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Penalidad por retiro voluntario
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$capitalizacion = trim(old('penalidad_por_retiro')) == '' ? '0' : old('penalidad_por_retiro');
											$capitalizacion = $capitalizacion == '0' ? false : true;
										?>
										<label class="btn btn-primary {{ $capitalizacion ? 'active' : '' }}">
											{!! Form::radio('penalidad_por_retiro', '1', $capitalizacion ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$capitalizacion ? 'active' : '' }}">
											{!! Form::radio('penalidad_por_retiro', '0', !$capitalizacion ? true : false) !!}No
										</label>
									</div>
									@if ($errors->has('penalidad_por_retiro'))
										<span class="help-block">{{ $errors->first('penalidad_por_retiro') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tipoCuotaAhorros') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("select[name='cuif_id']").selectAjax("{{ url('cuentaContable/cuentaContableAuxiliarAhorros') }}", {id:"{{ old('cuif_id') }}"});

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

		@if(!empty(old('intereses_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('intereses_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_cuif_id']"));
					$("select[name='intereses_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

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

		@if(!empty(old('intereses_por_pagar_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('intereses_por_pagar_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_por_pagar_cuif_id']"));
					$("select[name='intereses_por_pagar_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='tipo_ahorro']").on('change', function(){
			if($(this).find('option:selected').val() == 'PROGRAMADO')
			{
				$("#complementoProgramado").show(400);
			}
			else
			{
				$("#complementoProgramado").hide(400);
			}
		});

		$("select[name='tipo_vencimiento']").on('change', function(){
			if($(this).find('option:selected').val() == 'INDIVIDUAL')
			{
				$("#colectivo").hide();
				$("#individual").show();
			}
			else
			{
				$("#colectivo").show();
				$("#individual").hide();
			}
		});

		if($("select[name='tipo_ahorro']").find('option:selected').val() == 'PROGRAMADO')
		{
			$("#complementoProgramado").show(400);
		}

		if($("select[name='tipo_vencimiento']").find('option:selected').val() == 'INDIVIDUAL')
		{
			$("#colectivo").hide();
			$("#individual").show();
		}

	});
</script>
@endpush

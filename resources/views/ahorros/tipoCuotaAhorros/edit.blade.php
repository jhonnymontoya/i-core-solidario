@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de ahorros
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Tipos de ahorros</li>
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
		{!! Form::model($cuota, ['url' => ['tipoCuotaAhorros', $cuota], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar tipo de ahorro</h3>
				</div>
				<div class="card-body">
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
									Cuenta auxiliar
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-table"></i></span>
									{!! Form::text('cuif_id', $cuota->cuenta->full, ['class' => 'form-control', 'readonly']) !!}
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
								{!! Form::text('tipo_ahorro', null, ['class' => 'form-control', 'readonly']) !!}
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
									{!! Form::text('tasa', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tasa', 'autofocus']) !!}
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
								{!! Form::text('periodicidad_interes', null, ['class' => 'form-control', 'placeholder' => 'Seleccione una opción', 'readonly']) !!}
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
										$capitalizacion = $cuota->capitalizacion_simultanea;
										$capitalizacion = trim(old('capitalizacion_simultanea')) == '' ? $capitalizacion : old('capitalizacion_simultanea');
										$capitalizacion = $capitalizacion == '0' ? false : true;
									?>
									<label class="btn btn-outline-primary {{ $capitalizacion ? 'active' : '' }}">
										{!! Form::radio('capitalizacion_simultanea', '1', $capitalizacion ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ !$capitalizacion ? 'active' : '' }}">
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
									<label class="btn {{ $cuota->paga_retiros ? 'btn-outline-primary active' : 'btn-outline-danger active' }}">
										{!! Form::radio('paga_retiros', ($cuota->paga_retiros ? '1' : '0'), true) !!}{{ ($cuota->paga_retiros ? 'Sí' : 'No') }}
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
								@php
									$cuentaRendimientoInteresesPorPagar = optional($cuota->cuentaRendimientoInteresesPorPagar)->full;
								@endphp
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-table"></i></span>
									{!! Form::text('intereses_por_pagar_cuif_id', $cuentaRendimientoInteresesPorPagar, ['class' => 'form-control', 'readonly']) !!}
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
									$pagaInteresRetiro = trim(old('paga_intereses_retirados')) == '' ? $cuota->paga_intereses_retirados : old('paga_intereses_retirados');
									$pagaInteresRetiro = $pagaInteresRetiro == '0' ? false : true;
								?>
								<label class="btn btn-outline-primary {{ $pagaInteresRetiro ? 'active' : '' }}">
									{!! Form::radio('paga_intereses_retirados', '1', $pagaInteresRetiro ? true : false) !!}Sí
								</label>
								<label class="btn btn-outline-danger {{ !$pagaInteresRetiro ? 'active' : '' }}">
									{!! Form::radio('paga_intereses_retirados', '0', !$pagaInteresRetiro ? true : false) !!}No
								</label>
							</div>
							@if ($errors->has('paga_intereses_retirados'))
								<span class="help-block">{{ $errors->first('paga_intereses_retirados') }}</span>
							@endif
						</div>
					</div>

					@if($cuota->tipo_ahorro == 'PROGRAMADO')
					<div class="row">
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('tipo_vencimiento')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tipo_vencimiento'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo de vencimiento
								</label>
								{!! Form::text('tipo_vencimiento', null, ['class' => 'form-control', 'readonly']) !!}
								@if ($errors->has('tipo_vencimiento'))
									<span class="help-block">{{ $errors->first('tipo_vencimiento') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							@if($cuota->tipo_vencimiento == 'COLECTIVO')
							<div class="form-group {{ ($errors->has('fecha_vencimiento_colectivo')?'has-error':'') }}">
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
									{!! Form::text('fecha_vencimiento_colectivo', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
								</div>
								@if ($errors->has('fecha_vencimiento_colectivo'))
									<span class="help-block">{{ $errors->first('fecha_vencimiento_colectivo') }}</span>
								@endif
							</div>
							@else
							<div class="form-group {{ ($errors->has('plazo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('plazo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Meses de plazo
								</label>
								{!! Form::number('plazo', null, ['class' => 'form-control pull-right', 'placeholder' => 'Meses de plazo', 'autocomplete' => 'off', 'readonly']) !!}
								@if ($errors->has('plazo'))
									<span class="help-block">{{ $errors->first('plazo') }}</span>
								@endif
							</div>
							@endif
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
										$capitalizacion = $cuota->penalidad_por_retiro;
										$capitalizacion = trim(old('penalidad_por_retiro')) == '' ? $capitalizacion : old('penalidad_por_retiro');
										$capitalizacion = $capitalizacion == '0' ? false : true;
									?>
									<label class="btn {{ $capitalizacion ? 'btn-outline-primary active' : 'btn-outline-danger active' }}">
										{!! Form::radio('penalidad_por_retiro', ($capitalizacion ? '1' : '0'), true) !!}{{ ($capitalizacion ? 'Sí' : 'No') }}
									</label>
								</div>
								@if ($errors->has('penalidad_por_retiro'))
									<span class="help-block">{{ $errors->first('penalidad_por_retiro') }}</span>
								@endif
							</div>
						</div>
					</div>
					@endif
					<div class="row">
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Esta activo?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<?php
										$activo = trim(old('esta_activa')) == '' ? $cuota->esta_activa : old('esta_activa');
										$activo = $activo ? true : false;
									?>
									<label class="btn btn-outline-primary {{ $activo ? 'active' : '' }}">
										{!! Form::radio('esta_activa', '1', $activo ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ !$activo ? 'active' : '' }}">
										{!! Form::radio('esta_activa', '0', !$activo ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('esta_activa'))
									<span class="help-block">{{ $errors->first('esta_activa') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoCuotaAhorros') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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

		@php
			$interesesCuifId = !empty(old('intereses_cuif_id')) ? old('intereses_cuif_id') : $cuota->intereses_cuif_id;
			$interesesCuifId = empty($interesesCuifId) ? 0 : $interesesCuifId;
		@endphp
		$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $interesesCuifId }} }}).done(function(data){
			if(data.total_count == 1)
			{
				element = data.items[0];
				$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_cuif_id']"));
				$("select[name='intereses_cuif_id']").val(element.id).trigger("change");
			}
		});
	});
</script>
@endpush

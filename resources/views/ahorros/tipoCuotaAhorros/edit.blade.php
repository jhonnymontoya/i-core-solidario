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
						<div class="col-md-3">
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
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta auxiliar</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::text('cuif_id', $cuota->cuenta->full, ['class' => [$valid, 'form-control'], 'readonly']) !!}
									@if ($errors->has('cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_ahorro') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo de ahorro</label>
								{!! Form::text('tipo_ahorro', null, ['class' => [$valid, 'form-control'], 'readonly']) !!}
								@if ($errors->has('tipo_ahorro'))
									<div class="invalid-feedback">{{ $errors->first('tipo_ahorro') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('tasa') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tasa E.A.</label>
								<div class="input-group">
									{!! Form::text('tasa', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tasa E.A.']) !!}
									<div class="input-group-append">
										<span class="input-group-text">%</span>
									</div>
									@if ($errors->has('tasa'))
										<div class="invalid-feedback">{{ $errors->first('tasa') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('periodicidad_interes') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Periodicidad causación de intereses</label>
								{!! Form::text('periodicidad_interes', null, ['class' => [$valid, 'form-control'], 'readonly']) !!}
								@if ($errors->has('periodicidad_interes'))
									<div class="invalid-feedback">{{ $errors->first('periodicidad_interes') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Capitalización simultánea</label>
								<div>
									@php
										$valid = $errors->has('capitalizacion_simultanea') ? 'is-invalid' : '';
										$capitalizacion = empty(old('capitalizacion_simultanea')) ? $cuota->capitalizacion_simultanea : old('capitalizacion_simultanea');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $capitalizacion ? 'active' : '' }}">
											{!! Form::radio('capitalizacion_simultanea', 1, ($capitalizacion ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$capitalizacion ? 'active' : '' }}">
											{!! Form::radio('capitalizacion_simultanea', 0, (!$capitalizacion ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('capitalizacion_simultanea'))
										<div class="invalid-feedback">{{ $errors->first('capitalizacion_simultanea') }}</div>
									@endif
								</div>
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
								<div class="btn-group btn-group-toggle" data-toggle="buttons">
									<label class="btn {{ $cuota->paga_retiros ? 'btn-primary active' : 'btn-danger active' }}">
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
							<div class="form-group">
								@php
									$valid = $errors->has('intereses_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta contable intereses</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('intereses_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('intereses_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('intereses_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('intereses_por_pagar_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta contable intereses por pagar</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									@php
										$cuentaRendimientoInteresesPorPagar = optional($cuota->cuentaRendimientoInteresesPorPagar)->full;
									@endphp
									{!! Form::text('intereses_por_pagar_cuif_id', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cuenta contable intereses por pagar', 'readonly']) !!}
									@if ($errors->has('intereses_por_pagar_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('intereses_por_pagar_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Incluye retirados en el cálculo de rendimientos</label>
								<div>
									@php
										$valid = $errors->has('paga_intereses_retirados') ? 'is-invalid' : '';
										$pir = empty(old('paga_intereses_retirados')) ? $cuota->paga_intereses_retirados : old('paga_intereses_retirados');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $pir ? 'active' : '' }}">
											{!! Form::radio('paga_intereses_retirados', 1, ($pir ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$pir ? 'active' : '' }}">
											{!! Form::radio('paga_intereses_retirados', 0, (!$pir ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('paga_intereses_retirados'))
										<div class="invalid-feedback">{{ $errors->first('paga_intereses_retirados') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					@if($cuota->tipo_ahorro == 'PROGRAMADO')
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_vencimiento') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo de vencimiento</label>
								{!! Form::select('tipo_vencimiento', ['COLECTIVO' => 'Colectivo', 'INDIVIDUAL' => 'Individual'], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('tipo_vencimiento'))
									<div class="invalid-feedback">{{ $errors->first('tipo_vencimiento') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							@if($cuota->tipo_vencimiento == 'COLECTIVO')
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_vencimiento_colectivo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha de vencimiento</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_vencimiento_colectivo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_vencimiento_colectivo'))
										<div class="invalid-feedback">{{ $errors->first('fecha_vencimiento_colectivo') }}</div>
									@endif
								</div>
							</div>
							@else
							<div class="form-group">
								@php
									$valid = $errors->has('plazo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Meses de plazo</label>
								{!! Form::number('plazo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Meses de plazo']) !!}
								@if ($errors->has('plazo'))
									<div class="invalid-feedback">{{ $errors->first('plazo') }}</div>
								@endif
							</div>
							@endif
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tasa_penalidad') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tasa penalidad E.A.</label>
								<div class="input-group">
									{!! Form::text('tasa_penalidad', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tasa penalidad E.A.']) !!}
									<div class="input-group-prepend"><span class="input-group-text">%</span></div>
									@if ($errors->has('tasa_penalidad'))
										<div class="invalid-feedback">{{ $errors->first('tasa_penalidad') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Penalidad por retiro voluntario</label>
								<div>
									@php
										$valid = $errors->has('penalidad_por_retiro') ? 'is-invalid' : '';
										$ppr = empty(old('penalidad_por_retiro')) ? $cuota->penalidad_por_retiro : old('penalidad_por_retiro');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $ppr ? 'active' : '' }}">
											{!! Form::radio('penalidad_por_retiro', 1, ($ppr ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$ppr ? 'active' : '' }}">
											{!! Form::radio('penalidad_por_retiro', 0, (!$ppr ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('penalidad_por_retiro'))
										<div class="invalid-feedback">{{ $errors->first('penalidad_por_retiro') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					@endif
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Activo?</label>
								<div>
									@php
										$valid = $errors->has('esta_activa') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activa')) ? $cuota->esta_activa : old('esta_activa');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
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

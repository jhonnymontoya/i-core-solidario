@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos de garantías
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Tipos de garantías</li>
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
		<div class="box box-{{ $errors->count()?'danger':'success' }}">
			{!! Form::open(['url' => 'tipoGarantia', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="box-header with-border">
				<h3 class="box-title">Crear nuevo tipo de garantía</h3>
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
							{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autofocus']) !!}
							@if ($errors->has('codigo'))
								<span class="help-block">{{ $errors->first('codigo') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-7">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('tipo_garantia')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('tipo_garantia'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Tipo garantía
							</label>
							{{--{!! Form::select('tipo_garantia', ['PERSONAL' => 'Personal', 'REAL' => 'Real', 'FONDOGARANTIAS' => 'Fondo garantías'], null, ['class' => 'form-control select2', 'placeholder' => 'Tipo garantía']) !!}--}}
							{!! Form::select('tipo_garantia', ['PERSONAL' => 'Personal'], null, ['class' => 'form-control']) !!}
							@if ($errors->has('tipo_garantia'))
								<span class="help-block">{{ $errors->first('tipo_garantia') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('descripcion'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Descripción
							</label>
							{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción del tipo de garantía']) !!}
							@if ($errors->has('descripcion'))
								<span class="help-block">{{ $errors->first('descripcion') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-7">
						<div class="form-group">
							<label class="control-label">
								@if ($errors->has('condiciones'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Condiciones
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary active">
									{!! Form::radio('condiciones', 'esPermanente', true) !!}Permanente
								</label>
								<label class="btn btn-primary">
									{!! Form::radio('condiciones', 'esPermanenteConDescubierto', false) !!}Permanente con descubierto
								</label>
								<label class="btn btn-primary">
									{!! Form::radio('condiciones', 'requiereGarantiaPorMonto', false) !!}Requerir por monto
								</label>
								<label class="btn btn-primary">
									{!! Form::radio('condiciones', 'requiereGarantiaPorValorDescubierto', false) !!}Requerir por valor descubierto
								</label>
							</div>
							@if ($errors->has('tipo_cartera'))
								<span class="help-block">{{ $errors->first('tipo_cartera') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2 montoDesde" style="display: none">
						<div class="form-group {{ ($errors->has('monto')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('monto'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Monto desde
							</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('monto', null, ['class' => 'form-control text-right', 'autocomplete' => 'off', 'placeholder' => 'Monto', 'data-maskMoney']) !!}
							</div>
							@if ($errors->has('monto'))
								<span class="help-block">{{ $errors->first('monto') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2 descubiertoDesde" style="display: none">
						<div class="form-group {{ ($errors->has('valor_descubierto')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valor_descubierto'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Descubierto desde
							</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('valor_descubierto', null, ['class' => 'form-control text-right', 'autocomplete' => 'off', 'placeholder' => 'Valor descubierto', 'data-maskMoney']) !!}
							</div>
							@if ($errors->has('valor_descubierto'))
								<span class="help-block">{{ $errors->first('valor_descubierto') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('admite_codeudor_externo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('admite_codeudor_externo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Admite codeudor externo
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('admite_codeudor_externo', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('admite_codeudor_externo', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('admite_codeudor_externo'))
								<span class="help-block">{{ $errors->first('admite_codeudor_externo') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('valida_cupo_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valida_cupo_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Valida cupo del codeudor
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('valida_cupo_codeudor', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('valida_cupo_codeudor', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('valida_cupo_codeudor'))
								<span class="help-block">{{ $errors->first('valida_cupo_codeudor') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('tiene_limite_obligaciones_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('tiene_limite_obligaciones_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Limitar número codeudas
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('tiene_limite_obligaciones_codeudor', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('tiene_limite_obligaciones_codeudor', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('tiene_limite_obligaciones_codeudor'))
								<span class="help-block">{{ $errors->first('tiene_limite_obligaciones_codeudor') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('limite_obligaciones_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('limite_obligaciones_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Límite número codeudas
							</label>
							{!! Form::text('limite_obligaciones_codeudor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Límite número codeudas', 'disabled']) !!}
							@if ($errors->has('limite_obligaciones_codeudor'))
								<span class="help-block">{{ $errors->first('limite_obligaciones_codeudor') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('tiene_limite_saldo_codeudas')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('tiene_limite_saldo_codeudas'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Limitar por saldo codeudas
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('tiene_limite_saldo_codeudas', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('tiene_limite_saldo_codeudas', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('tiene_limite_saldo_codeudas'))
								<span class="help-block">{{ $errors->first('tiene_limite_saldo_codeudas') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('limite_saldo_codeudas')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('limite_saldo_codeudas'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Límite por saldo codeudas
							</label>
							<div class="input-group">
								<span class="input-group-addon">$</span>
								{!! Form::text('limite_saldo_codeudas', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Límite por saldo codeudas', 'data-maskMoney', 'disabled']) !!}
							</div>
							@if ($errors->has('limite_saldo_codeudas'))
								<span class="help-block">{{ $errors->first('limite_saldo_codeudas') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('valida_antiguedad_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valida_antiguedad_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Exigir antigüedad a codeudor
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('valida_antiguedad_codeudor', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('valida_antiguedad_codeudor', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('valida_antiguedad_codeudor'))
								<span class="help-block">{{ $errors->first('valida_antiguedad_codeudor') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('antiguedad_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('antiguedad_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Días mínimos de antigüedad codeudor
							</label>
							{!! Form::text('antiguedad_codeudor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Días mínimos de antigüedad codeudor', 'data-maskMoney', 'disabled']) !!}
							@if ($errors->has('antiguedad_codeudor'))
								<span class="help-block">{{ $errors->first('antiguedad_codeudor') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('valida_calificacion_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valida_calificacion_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Validar calificación codeudor
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary">
									{!! Form::radio('valida_calificacion_codeudor', '1', false) !!}Sí
								</label>
								<label class="btn btn-danger active">
									{!! Form::radio('valida_calificacion_codeudor', '0', true) !!}No
								</label>
							</div>
							@if ($errors->has('valida_calificacion_codeudor'))
								<span class="help-block">{{ $errors->first('valida_calificacion_codeudor') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('calificacion_minima_requerida_codeudor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('calificacion_minima_requerida_codeudor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Calificación mínima codeudor
							</label>
							{!! Form::select('calificacion_minima_requerida_codeudor', ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'], null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Calificación mínima codeudor', 'disabled']) !!}
							@if ($errors->has('calificacion_minima_requerida_codeudor'))
								<span class="help-block">{{ $errors->first('calificacion_minima_requerida_codeudor') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('tipoGarantia') }}" class="btn btn-danger pull-right">Cancelar</a>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(".select2").select2();
		$("input[name='condiciones']").change(function(e){
			$(".montoDesde").find("input[name='monto']").val('');
			$(".descubiertoDesde").find("input[name='valor_descubierto']").val('');
			$(".montoDesde").hide();
			$(".descubiertoDesde").hide();
			switch($(this).val()) {
				case 'esPermanente': 
				case 'esPermanenteConDescubierto':
					break;
				case 'requiereGarantiaPorMonto':
					$(".montoDesde").show();
					$(".montoDesde").find("input[name='monto']").enfocar();
					break;
				case 'requiereGarantiaPorValorDescubierto':
					$(".descubiertoDesde").show();
					$(".descubiertoDesde").find("input[name='valor_descubierto']").enfocar();
					break;
			}
		});

		$("input[name='tiene_limite_obligaciones_codeudor']").change(function(e) {
			activar("input[name='limite_obligaciones_codeudor']", $(this).val() == 1 ? true : false);
		});

		$("input[name='tiene_limite_saldo_codeudas']").change(function(e) {
			activar("input[name='limite_saldo_codeudas']", $(this).val() == 1 ? true : false);
		});

		$("input[name='valida_antiguedad_codeudor']").change(function(e) {
			activar("input[name='antiguedad_codeudor']", $(this).val() == 1 ? true : false);
		});

		$("input[name='valida_calificacion_codeudor']").change(function(e) {
			activar("select[name='calificacion_minima_requerida_codeudor']", $(this).val() == 1 ? true : false);
		});
	});

	function activar(selector, estado) {
		$(selector).prop('disabled', !estado);
		if(estado) {
			$(selector).enfocar();
		}
		else {
			$(selector).val('');
		}
	}
</script>
@endpush

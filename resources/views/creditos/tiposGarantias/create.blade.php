@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de garantías
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Tipos de garantías</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::open(['url' => 'tipoGarantia', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo tipo de garantía</h3>
				</div>
				<div class="card-body">

					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'autofocus']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-7">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_garantia') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo garantía</label>
								{!! Form::select('tipo_garantia', ['PERSONAL' => 'Personal'], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('tipo_garantia'))
									<div class="invalid-feedback">{{ $errors->first('tipo_garantia') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::text('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción del tipo de garantía']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								<label class="control-label">Condiciones</label>
								<div>
									@php
										$valid = $errors->has('condiciones') ? 'is-invalid' : '';
										$condicion = empty(old('condiciones')) ? 'esPermanente' : old('condiciones');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $condicion ? 'active' : '' }}">
											{!! Form::radio('condiciones', 'esPermanente', ($condicion ? true : false), ['class' => [$valid]]) !!}Permanente
										</label>
										<label class="btn btn-primary {{ !$condicion ? 'active' : '' }}">
											{!! Form::radio('condiciones', 'esPermanenteConDescubierto', (!$condicion ? true : false ), ['class' => [$valid]]) !!}Permanente con descubierto
										</label>
										<label class="btn btn-primary {{ !$condicion ? 'active' : '' }}">
											{!! Form::radio('condiciones', 'requiereGarantiaPorMonto', (!$condicion ? true : false ), ['class' => [$valid]]) !!}Requerir por monto
										</label>
										<label class="btn btn-primary {{ !$condicion ? 'active' : '' }}">
											{!! Form::radio('condiciones', 'requiereGarantiaPorValorDescubierto', (!$condicion ? true : false ), ['class' => [$valid]]) !!}Requerir por valor descubierto
										</label>
									</div>
									@if ($errors->has('condiciones'))
										<div class="invalid-feedback">{{ $errors->first('condiciones') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-3 montoDesde" style="display: none">
							<div class="form-group">
								@php
									$valid = $errors->has('monto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Monto desde</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('monto', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Monto desde', 'data-maskMoney']) !!}
									@if ($errors->has('monto'))
										<div class="invalid-feedback">{{ $errors->first('monto') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-3 descubiertoDesde" style="display: none">
							<div class="form-group">
								@php
									$valid = $errors->has('valor_descubierto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descubierto desde</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('valor_descubierto', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Descubierto desde', 'data-maskMoney']) !!}
									@if ($errors->has('valor_descubierto'))
										<div class="invalid-feedback">{{ $errors->first('valor_descubierto') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Admite codeudor externo?</label>
								<div>
									@php
										$valid = $errors->has('admite_codeudor_externo') ? 'is-invalid' : '';
										$admiteCodeudorExterno = empty(old('admite_codeudor_externo')) ? false : old('admite_codeudor_externo');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $admiteCodeudorExterno ? 'active' : '' }}">
											{!! Form::radio('admite_codeudor_externo', 1, ($admiteCodeudorExterno ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$admiteCodeudorExterno ? 'active' : '' }}">
											{!! Form::radio('admite_codeudor_externo', 0, (!$admiteCodeudorExterno ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('admite_codeudor_externo'))
										<div class="invalid-feedback">{{ $errors->first('admite_codeudor_externo') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Valida cupo del codeudor?</label>
								<div>
									@php
										$valid = $errors->has('valida_cupo_codeudor') ? 'is-invalid' : '';
										$validaCupoCodeudor = empty(old('valida_cupo_codeudor')) ? false : old('valida_cupo_codeudor');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $validaCupoCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_cupo_codeudor', 1, ($validaCupoCodeudor ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$validaCupoCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_cupo_codeudor', 0, (!$validaCupoCodeudor ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('valida_cupo_codeudor'))
										<div class="invalid-feedback">{{ $errors->first('valida_cupo_codeudor') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Limitar número codeudas?</label>
								<div>
									@php
										$valid = $errors->has('tiene_limite_obligaciones_codeudor') ? 'is-invalid' : '';
										$limitarCodeudor = empty(old('tiene_limite_obligaciones_codeudor')) ? false : old('tiene_limite_obligaciones_codeudor');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $limitarCodeudor ? 'active' : '' }}">
											{!! Form::radio('tiene_limite_obligaciones_codeudor', 1, ($limitarCodeudor ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$limitarCodeudor ? 'active' : '' }}">
											{!! Form::radio('tiene_limite_obligaciones_codeudor', 0, (!$limitarCodeudor ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('tiene_limite_obligaciones_codeudor'))
										<div class="invalid-feedback">{{ $errors->first('tiene_limite_obligaciones_codeudor') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('limite_obligaciones_codeudor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Límite número codeudas</label>
								{!! Form::text('limite_obligaciones_codeudor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Límite número codeudas', 'disabled']) !!}
								@if ($errors->has('limite_obligaciones_codeudor'))
									<div class="invalid-feedback">{{ $errors->first('limite_obligaciones_codeudor') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Limitar por saldo codeudas?</label>
								<div>
									@php
										$valid = $errors->has('tiene_limite_saldo_codeudas') ? 'is-invalid' : '';
										$limitarPorSaldoCodeudas = empty(old('tiene_limite_saldo_codeudas')) ? false : old('tiene_limite_saldo_codeudas');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $limitarPorSaldoCodeudas ? 'active' : '' }}">
											{!! Form::radio('tiene_limite_saldo_codeudas', 1, ($limitarPorSaldoCodeudas ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$limitarPorSaldoCodeudas ? 'active' : '' }}">
											{!! Form::radio('tiene_limite_saldo_codeudas', 0, (!$limitarPorSaldoCodeudas ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('tiene_limite_saldo_codeudas'))
										<div class="invalid-feedback">{{ $errors->first('tiene_limite_saldo_codeudas') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('limite_saldo_codeudas') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Límite por saldo codeudas</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('limite_saldo_codeudas', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Límite por saldo codeudas', 'data-maskMoney', 'disabled']) !!}
									@if ($errors->has('limite_saldo_codeudas'))
										<div class="invalid-feedback">{{ $errors->first('limite_saldo_codeudas') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Exigir antigüedad a codeudor?</label>
								<div>
									@php
										$valid = $errors->has('valida_antiguedad_codeudor') ? 'is-invalid' : '';
										$validaAntiguedadCodeudor = empty(old('valida_antiguedad_codeudor')) ? false : old('valida_antiguedad_codeudor');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $validaAntiguedadCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_antiguedad_codeudor', 1, ($validaAntiguedadCodeudor ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$validaAntiguedadCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_antiguedad_codeudor', 0, (!$validaAntiguedadCodeudor ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('valida_antiguedad_codeudor'))
										<div class="invalid-feedback">{{ $errors->first('valida_antiguedad_codeudor') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('antiguedad_codeudor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Días mínimos de antigüedad codeudor</label>
								{!! Form::text('antiguedad_codeudor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Días mínimos de antigüedad codeudor', 'data-maskMoney', 'disabled']) !!}
								@if ($errors->has('antiguedad_codeudor'))
									<div class="invalid-feedback">{{ $errors->first('antiguedad_codeudor') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Validar calificación codeudor?</label>
								<div>
									@php
										$valid = $errors->has('valida_calificacion_codeudor') ? 'is-invalid' : '';
										$validaCalificacionCodeudor = empty(old('valida_calificacion_codeudor')) ? false : old('valida_calificacion_codeudor');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $validaCalificacionCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_calificacion_codeudor', 1, ($validaCalificacionCodeudor ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$validaCalificacionCodeudor ? 'active' : '' }}">
											{!! Form::radio('valida_calificacion_codeudor', 0, (!$validaCalificacionCodeudor ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('valida_calificacion_codeudor'))
										<div class="invalid-feedback">{{ $errors->first('valida_calificacion_codeudor') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('calificacion_minima_requerida_codeudor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Calificación mínima codeudor</label>
								{!! Form::select('calificacion_minima_requerida_codeudor', ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D', 'E' => 'E'], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Calificación mínima codeudor', 'disabled']) !!}
								@if ($errors->has('calificacion_minima_requerida_codeudor'))
									<div class="invalid-feedback">{{ $errors->first('calificacion_minima_requerida_codeudor') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoGarantia') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
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

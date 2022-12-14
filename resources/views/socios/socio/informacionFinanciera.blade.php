@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Editar socio</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'financiera'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEdit', $socio->id) }}">General</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditLaboral', $socio->id) }}">Laboral</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditImagenes', $socio->id) }}">Imagen</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('socioEditFinanciera', $socio->id) }}">Financiera</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('activos') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Activos</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('activos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->activos, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Activos', 'data-maskMoney']) !!}
											@if ($errors->has('activos'))
												<div class="invalid-feedback">{{ $errors->first('activos') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('patrimonio') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Patrimonio</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('patrimonio', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->activos-$socio->tercero->informacionesFinancieras->last()->pasivos, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Patrimonio', 'readonly', 'tabindex' => -1]) !!}
											@if ($errors->has('patrimonio'))
												<div class="invalid-feedback">{{ $errors->first('patrimonio') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('pasivos') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Pasivos</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('pasivos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->pasivos, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Pasivos', 'data-maskMoney']) !!}
											@if ($errors->has('pasivos'))
												<div class="invalid-feedback">{{ $errors->first('pasivos') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('otros_ingresos') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Otros ingresos</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('otros_ingresos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->ingreso_mensual, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Otros ingresos', 'data-maskMoney']) !!}
											@if ($errors->has('otros_ingresos'))
												<div class="invalid-feedback">{{ $errors->first('otros_ingresos') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('resultado_neto') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Resultado neto</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('resultado_neto', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->ingreso_mensual - $socio->tercero->informacionesFinancieras->last()->gasto_mensual, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Resultado neto', 'readonly', 'tabindex' => -1]) !!}
											@if ($errors->has('resultado_neto'))
												<div class="invalid-feedback">{{ $errors->first('resultado_neto') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- INICIO CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('egresos_mensuales') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Egresos mensuales</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('egresos_mensuales', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->gasto_mensual, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Egresos mensuales', 'data-maskMoney']) !!}
											@if ($errors->has('egresos_mensuales'))
												<div class="invalid-feedback">{{ $errors->first('egresos_mensuales') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('fecha_corte') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha de corte</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_corte', $socio->tercero->informacionesFinancieras->last() == null ? null : $socio->tercero->informacionesFinancieras->last()->fecha_corte, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_corte'))
												<div class="invalid-feedback">{{ $errors->first('fecha_corte') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('socio') }}" class="btn btn-outline-danger">Volver</a>
					<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-outline-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
				</div>
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
		$("input[name='activos']").on('keyup keypress blur change focus', function(e){
			activos = parseInt($("input[name='activos']").maskMoney('cleanvalue')) | 0;
			pasivos = parseInt($("input[name='pasivos']").maskMoney('cleanvalue')) | 0;
			patrimonio = $("input[name='activos']").maskMoney('maskvalue', (activos - pasivos));
			$("input[name='patrimonio']").val(patrimonio);
		});

		$("input[name='pasivos']").on('keyup keypress blur change focus', function(e){
			activos = parseInt($("input[name='activos']").maskMoney('cleanvalue')) | 0;
			pasivos = parseInt($("input[name='pasivos']").maskMoney('cleanvalue')) | 0;
			patrimonio = $("input[name='activos']").maskMoney('maskvalue', (activos - pasivos));
			$("input[name='patrimonio']").val(patrimonio);
		});

		$("input[name='otros_ingresos']").on('keyup keypress blur change focus', function(e){
			otros_ingresos = parseInt($("input[name='otros_ingresos']").maskMoney('cleanvalue')) | 0;
			otros_egresos = parseInt($("input[name='egresos_mensuales']").maskMoney('cleanvalue')) | 0;
			patrimonio = $("input[name='activos']").maskMoney('maskvalue', (otros_ingresos - otros_egresos));
			$("input[name='resultado_neto']").val(patrimonio);
		});

		$("input[name='egresos_mensuales']").on('keyup keypress blur change focus', function(e){
			otros_ingresos = parseInt($("input[name='otros_ingresos']").maskMoney('cleanvalue')) | 0;
			otros_egresos = parseInt($("input[name='egresos_mensuales']").maskMoney('cleanvalue')) | 0;
			patrimonio = $("input[name='activos']").maskMoney('maskvalue', (otros_ingresos - otros_egresos));
			$("input[name='resultado_neto']").val(patrimonio);
		});
		
		$(window).load(function(){
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->activos) | old('activos'))
			$("input[name='activos']").maskMoney('mask');
			@endif
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->patrimonio) | old('patrimonio'))
			$("input[name='patrimonio']").maskMoney('mask');
			@endif
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->pasivos) | old('pasivos'))
			$("input[name='pasivos']").maskMoney('mask');
			@endif
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->ingreso_mensual) | old('otros_ingresos'))
			$("input[name='otros_ingresos']").maskMoney('mask');
			@endif
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->resultado) | old('resultado_neto'))
			$("input[name='resultado_neto']").maskMoney('mask');
			@endif
			@if((empty($socio->tercero->informacionesFinancieras->last()) ? null : $socio->tercero->informacionesFinancieras->last()->gasto_mensual) | old('egresos_mensuales'))
			$("input[name='egresos_mensuales']").maskMoney('mask');
			@endif
		});
	});
</script>
@endpush

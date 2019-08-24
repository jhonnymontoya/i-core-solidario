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

		<div class="row">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'financiera'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'data-maskMoney-removeMask']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('activos')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('activos'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Activos
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('activos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->activos, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Activos', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('activos'))
												<span class="help-block">{{ $errors->first('activos') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('patrimonio')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('patrimonio'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Patrimonio
										</label>
										<div class="col-sm-9">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('patrimonio', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->activos-$socio->tercero->informacionesFinancieras->last()->pasivos, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Patrimonio', 'readonly', 'tabindex' => -1]) !!}
											</div>
											@if ($errors->has('patrimonio'))
												<span class="help-block">{{ $errors->first('patrimonio') }}</span>
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
									<div class="form-group {{ ($errors->has('pasivos')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('pasivos'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Pasivos
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('pasivos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->pasivos, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Pasivos', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('pasivos'))
												<span class="help-block">{{ $errors->first('pasivos') }}</span>
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
									<div class="form-group {{ ($errors->has('otros_ingresos')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('otros_ingresos'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Otros ingresos
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('otros_ingresos', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->ingreso_mensual, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Otros ingresos', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('otros_ingresos'))
												<span class="help-block">{{ $errors->first('otros_ingresos') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('resultado_neto')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('resultado_neto'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Resultado neto
										</label>
										<div class="col-sm-9">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('resultado_neto', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->ingreso_mensual - $socio->tercero->informacionesFinancieras->last()->gasto_mensual, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Resultado neto', 'readonly', 'tabindex' => -1]) !!}
											</div>
											@if ($errors->has('resultado_neto'))
												<span class="help-block">{{ $errors->first('resultado_neto') }}</span>
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
									<div class="form-group {{ ($errors->has('egresos_mensuales')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('egresos_mensuales'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Egresos mensuales
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('egresos_mensuales', $socio->tercero->informacionesFinancieras->last() == null ? null : (int)$socio->tercero->informacionesFinancieras->last()->gasto_mensual, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Egresos mensuales', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('egresos_mensuales'))
												<span class="help-block">{{ $errors->first('egresos_mensuales') }}</span>
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
									<div class="form-group {{ ($errors->has('fecha_corte')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_corte'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha de corte
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												{!! Form::text('fecha_corte', $socio->tercero->informacionesFinancieras->last() == null ? null : $socio->tercero->informacionesFinancieras->last()->fecha_corte, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_corte'))
												<span class="help-block">{{ $errors->first('fecha_corte') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-outline-success']) !!}
									<a href="{{ url('socio') }}" class="btn btn-outline-danger">Volver</a>
									<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} pull-right {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
								</div>
							</div>
						</div>
					</div>
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

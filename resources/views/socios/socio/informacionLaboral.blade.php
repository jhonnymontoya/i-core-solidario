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
			{!! Form::model($socio, ['url' => ['socio', $socio, 'laboral'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'data-maskMoney-removeMask']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('pagaduria_id')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('pagaduria_id'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Empresa
										</label>
										<div class="col-sm-8">
											{!! Form::select('pagaduria_id', $pagadurias, null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Empresa', 'autofocus']) !!}
											@if ($errors->has('pagaduria_id'))
												<span class="help-block">{{ $errors->first('pagaduria_id') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('cargo')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('cargo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Cargo
										</label>
										<div class="col-sm-8">
											{!! Form::text('cargo', null, ['class' => 'form-control pull-right', 'placeholder' => 'Cargo', 'autocomplete' => 'off']) !!}
											@if ($errors->has('cargo'))
												<span class="help-block">{{ $errors->first('cargo') }}</span>
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
									<div class="form-group {{ ($errors->has('profesion')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('profesion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Profesión
										</label>
										<div class="col-sm-8">
											{!! Form::select('profesion', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('profesion'))
												<span class="help-block">{{ $errors->first('profesion') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('fecha_ingreso_empresa')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_ingreso_empresa'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha ingreso empresa
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												{!! Form::text('fecha_ingreso_empresa', $socio->fecha_ingreso, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_ingreso_empresa'))
												<span class="help-block">{{ $errors->first('fecha_ingreso_empresa') }}</span>
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
									<div class="form-group {{ ($errors->has('tipo_contrato')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_contrato'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo de contrato
										</label>
										<div class="col-sm-8">
											{!! Form::select('tipo_contrato', $tiposContratos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione un tipo de contrato']) !!}
											@if ($errors->has('tipo_contrato'))
												<span class="help-block">{{ $errors->first('tipo_contrato') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('fecha_fin_contrato')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_fin_contrato'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha fin de contrato
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												{!! Form::text('fecha_fin_contrato', $socio->fecha_fin_contrato, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_fin_contrato'))
												<span class="help-block">{{ $errors->first('fecha_fin_contrato') }}</span>
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
									<div class="form-group {{ ($errors->has('jornada_laboral')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('jornada_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Jornada laboral
										</label>
										<div class="col-sm-8">
											{!! Form::select('jornada_laboral', $jornadasLaborales, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una jornada laboral']) !!}
											@if ($errors->has('jornada_laboral'))
												<span class="help-block">{{ $errors->first('jornada_laboral') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('codigo_nomina')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('codigo_nomina'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Código nómina
										</label>
										<div class="col-sm-8">
											{!! Form::text('codigo_nomina', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Código nómina']) !!}
											@if ($errors->has('codigo_nomina'))
												<span class="help-block">{{ $errors->first('codigo_nomina') }}</span>
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
								<div class="col-md-12">
									<div class="form-group {{ ($errors->has('actividad_economica')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('actividad_economica'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Actividad económica
										</label>
										<div class="col-sm-10">
											{!! Form::select('actividad_economica', [], null, ['class' => 'form-control']) !!}
											@if ($errors->has('actividad_economica'))
												<span class="help-block">{{ $errors->first('actividad_economica') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								<div class="col-md-12">
									<h4>Remuneración</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('sueldo_mensual')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('sueldo_mensual'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Sueldo mensual
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('sueldo_mensual', $socio->sueldo_mes, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Sueldo mensual', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('sueldo_mensual'))
												<span class="help-block">{{ $errors->first('sueldo_mensual') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_comision')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_comision'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor comisión
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_comision', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor comisión', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_comision'))
												<span class="help-block">{{ $errors->first('valor_comision') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_comision')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_comision'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de comisión
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_comision', $periodicidades, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_comision'))
												<span class="help-block">{{ $errors->first('periodicidad_comision') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor prima
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_prima', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor prima', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_prima'))
												<span class="help-block">{{ $errors->first('valor_prima') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de prima
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_prima', $periodicidades, $socio->periodicidad_prima, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_prima'))
												<span class="help-block">{{ $errors->first('periodicidad_prima') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_extra_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_extra_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor extra prima
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_extra_prima', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor extra prima', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_extra_prima'))
												<span class="help-block">{{ $errors->first('valor_extra_prima') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_extra_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_extra_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de extra prima
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_extra_prima', $periodicidades, $socio->periodicidad_extra_prima, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_extra_prima'))
												<span class="help-block">{{ $errors->first('periodicidad_extra_prima') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_descuento_nomina')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_descuento_nomina'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor descuento nómina
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_descuento_nomina', $socio->descuentos_nomina, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor descuento nómina', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_descuento_nomina'))
												<span class="help-block">{{ $errors->first('valor_descuento_nomina') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_descuento_nomina')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_descuento_nomina'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de descuento nómina
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_descuento_nomina', $periodicidades, $socio->periodicidad_descuentos_nomina, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_descuento_nomina'))
												<span class="help-block">{{ $errors->first('periodicidad_descuento_nomina') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_descuento_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_descuento_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor descuento prima
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_descuento_prima', $socio->descuento_prima, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor descuento prima', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_descuento_prima'))
												<span class="help-block">{{ $errors->first('valor_descuento_prima') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_descuento_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_descuento_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de descuento prima
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_descuento_prima', $periodicidades, $socio->periodicidad_descuento_prima, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_descuento_prima'))
												<span class="help-block">{{ $errors->first('periodicidad_descuento_prima') }}</span>
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
									<div class="form-group {{ ($errors->has('valor_descuento_extra_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('valor_descuento_extra_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Valor descuento extra prima
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('valor_descuento_extra_prima', $socio->descuento_extra_prima, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor descuento extra prima', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('valor_descuento_extra_prima'))
												<span class="help-block">{{ $errors->first('valor_descuento_extra_prima') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('periodicidad_descuento_extra_prima')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('periodicidad_descuento_extra_prima'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidad de descuento extra prima
										</label>
										<div class="col-sm-8">
											{!! Form::select('periodicidad_descuento_extra_prima', $periodicidades, $socio->periodicidad_descuento_extra_prima, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
											@if ($errors->has('periodicidad_descuento_extra_prima'))
												<span class="help-block">{{ $errors->first('periodicidad_descuento_extra_prima') }}</span>
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
									{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('socio') }}" class="btn btn-danger">Volver</a>
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
		$("select[name='profesion']").selectAjax("{{ url('api/profesion') }}", {id:"{{ $socio->profesion_id | old('profesion') }}"});
		$("select[name='actividad_economica']").selectAjax("{{ url('ciiu') }}", {id:"{{ $socio->tercero->actividad_economica_id | old('actividad_economica') }}"});
		$(window).load(function(){
			@if(!empty($socio->sueldo_mes))
			$("input[name='sueldo_mensual']").maskMoney('mask');
			@endif
			@if($socio->valor_comision | old('valor_comision'))
			$("input[name='valor_comision']").maskMoney('mask');
			@endif
			@if($socio->valor_prima | old('valor_prima'))
			$("input[name='valor_prima']").maskMoney('mask');
			@endif
			@if($socio->valor_extra_prima | old('valor_extra_prima'))
			$("input[name='valor_extra_prima']").maskMoney('mask');
			@endif
			@if($socio->descuentos_nomina | old('valor_descuento_nomina'))
			$("input[name='valor_descuento_nomina']").maskMoney('mask');
			@endif
			@if($socio->descuento_prima | old('valor_descuento_prima'))
			$("input[name='valor_descuento_prima']").maskMoney('mask');
			@endif
			@if($socio->descuento_extra_prima | old('valor_descuento_extra_prima'))
			$("input[name='valor_descuento_extra_prima']").maskMoney('mask');
			@endif
		});

	});
</script>
@endpush

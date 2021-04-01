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
			{!! Form::model($socio, ['url' => ['socio', $socio, 'laboral'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEdit', $socio->id) }}">General</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('socioEditLaboral', $socio->id) }}">Laboral</a>
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
							<a class="nav-link" href="{{ route('socioEditFinanciera', $socio->id) }}">Financiera</a>
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
											$valid = $errors->has('pagaduria_id') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Empresa</label>
										{!! Form::select('pagaduria_id', $pagadurias, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Empresa']) !!}
										@if ($errors->has('pagaduria_id'))
											<div class="invalid-feedback">{{ $errors->first('pagaduria_id') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('cargo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Cargo</label>
										{!! Form::text('cargo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cargo', 'autofocus']) !!}
										@if ($errors->has('cargo'))
											<div class="invalid-feedback">{{ $errors->first('cargo') }}</div>
										@endif
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
											$valid = $errors->has('profesion') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Profesión</label>
										{!! Form::select('profesion', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('profesion'))
											<div class="invalid-feedback">{{ $errors->first('profesion') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('fecha_ingreso_empresa') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha ingreso empresa</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_ingreso_empresa', $socio->fecha_ingreso, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_ingreso_empresa'))
												<div class="invalid-feedback">{{ $errors->first('fecha_ingreso_empresa') }}</div>
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
											$valid = $errors->has('tipo_contrato') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tipo de contrato</label>
										{!! Form::select('tipo_contrato', $tiposContratos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione un tipo de contrato']) !!}
										@if ($errors->has('tipo_contrato'))
											<div class="invalid-feedback">{{ $errors->first('tipo_contrato') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('fecha_fin_contrato') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha fin de contrato</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_fin_contrato', $socio->fecha_fin_contrato, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_fin_contrato'))
												<div class="invalid-feedback">{{ $errors->first('fecha_fin_contrato') }}</div>
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
											$valid = $errors->has('jornada_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Jornada laboral</label>
										{!! Form::select('jornada_laboral', $jornadasLaborales, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una jornada laboral']) !!}
										@if ($errors->has('jornada_laboral'))
											<div class="invalid-feedback">{{ $errors->first('jornada_laboral') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('codigo_nomina') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Código nómina</label>
										{!! Form::text('codigo_nomina', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código nómina']) !!}
										@if ($errors->has('codigo_nomina'))
											<div class="invalid-feedback">{{ $errors->first('codigo_nomina') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-12">
									<div class="form-group">
										@php
											$valid = $errors->has('actividad_economica') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Actividad económica</label>
										{!! Form::select('actividad_economica', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('actividad_economica'))
											<div class="invalid-feedback">{{ $errors->first('actividad_economica') }}</div>
										@endif
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
								<div class="col-md-12">
									<div class="form-group">
										@php
											$valid = $errors->has('sueldo_mensual') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Sueldo mensual</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('sueldo_mensual', $socio->sueldo_mes, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Sueldo mensual', 'data-maskMoney']) !!}
											@if ($errors->has('sueldo_mensual'))
												<div class="invalid-feedback">{{ $errors->first('sueldo_mensual') }}</div>
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
											$valid = $errors->has('valor_comision') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor comisión</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_comision', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor comisión', 'data-maskMoney']) !!}
											@if ($errors->has('valor_comision'))
												<div class="invalid-feedback">{{ $errors->first('valor_comision') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_comision') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de comisión</label>
										{!! Form::select('periodicidad_comision', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_comision'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_comision') }}</div>
										@endif
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
											$valid = $errors->has('valor_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor prima</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_prima', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor prima', 'data-maskMoney']) !!}
											@if ($errors->has('valor_prima'))
												<div class="invalid-feedback">{{ $errors->first('valor_prima') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de prima</label>
										{!! Form::select('periodicidad_prima', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_prima'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_prima') }}</div>
										@endif
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
											$valid = $errors->has('valor_extra_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor extra prima</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_extra_prima', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor extra prima', 'data-maskMoney']) !!}
											@if ($errors->has('valor_extra_prima'))
												<div class="invalid-feedback">{{ $errors->first('valor_extra_prima') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_extra_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de extra prima</label>
										{!! Form::select('periodicidad_extra_prima', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_extra_prima'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_extra_prima') }}</div>
										@endif
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
											$valid = $errors->has('valor_descuento_nomina') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor descuento nómina</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_descuento_nomina', $socio->descuentos_nomina, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor descuento nómina', 'data-maskMoney']) !!}
											@if ($errors->has('valor_descuento_nomina'))
												<div class="invalid-feedback">{{ $errors->first('valor_descuento_nomina') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_descuento_nomina') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de descuento nómina</label>
										{!! Form::select('periodicidad_descuento_nomina', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_descuento_nomina'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_descuento_nomina') }}</div>
										@endif
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
											$valid = $errors->has('valor_descuento_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor descuento prima</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_descuento_prima', $socio->descuento_prima, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor descuento prima', 'data-maskMoney']) !!}
											@if ($errors->has('valor_descuento_prima'))
												<div class="invalid-feedback">{{ $errors->first('valor_descuento_prima') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_descuento_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de descuento prima</label>
										{!! Form::select('periodicidad_descuento_prima', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_descuento_prima'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_descuento_prima') }}</div>
										@endif
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
											$valid = $errors->has('valor_descuento_extra_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Valor descuento extra prima</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('valor_descuento_extra_prima', $socio->descuento_extra_prima, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor descuento extra prima', 'data-maskMoney']) !!}
											@if ($errors->has('valor_descuento_extra_prima'))
												<div class="invalid-feedback">{{ $errors->first('valor_descuento_extra_prima') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('periodicidad_descuento_extra_prima') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Periodicidad de descuento extra prima</label>
										{!! Form::select('periodicidad_descuento_extra_prima', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad_descuento_extra_prima'))
											<div class="invalid-feedback">{{ $errors->first('periodicidad_descuento_extra_prima') }}</div>
										@endif
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
		$("select[name='profesion']").selectAjax("{{ url('api/profesion') }}", {id:"{{ $socio->profesion_id | old('profesion') }}"});
		@if ($socio->tercero->actividad_economica_id || old('actividad_economica'))
			$("select[name='actividad_economica']").selectAjax("{{ url('ciiu') }}", {id:"{{ $socio->tercero->actividad_economica_id | old('actividad_economica') }}"});
		@endif

		$(document).ready(function(){
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

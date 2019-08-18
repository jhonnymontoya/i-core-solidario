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
			{!! Form::model($socio, ['url' => ['socio', $socio], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
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
									<div class="form-group {{ ($errors->has('tipo_identificacion')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_identificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo identificación
										</label>
										<div class="col-sm-8">
											{!! Form::text('tipo_identificacion', $socio->tercero->tipoIdentificacion->nombre, ['class' => 'form-control', 'readonly', 'tabindex' => -1]) !!}
											@if ($errors->has('tipo_identificacion'))
												<span class="help-block">{{ $errors->first('tipo_identificacion') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('identificacion')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('identificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Número de identificación
										</label>
										<div class="col-sm-8">
											{!! Form::text('identificacion', $socio->tercero->numero_identificacion, ['class' => 'form-control', 'autocomplete' => 'off', 'readonly', 'tabindex' => -1]) !!}
											@if ($errors->has('identificacion'))
												<span class="help-block">{{ $errors->first('identificacion') }}</span>
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
									<div class="form-group {{ ($errors->has('primer_nombre')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('primer_nombre'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Primer nombre
										</label>
										<div class="col-sm-8">
											{!! Form::text('primer_nombre', $socio->tercero->primer_nombre, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Primer nombre', 'autofocus']) !!}
											@if ($errors->has('primer_nombre'))
												<span class="help-block">{{ $errors->first('primer_nombre') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('segundo_nombre')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('segundo_nombre'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Segundo nombre
										</label>
										<div class="col-sm-8">
											{!! Form::text('segundo_nombre', $socio->tercero->segundo_nombre, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Segundo nombre']) !!}
											@if ($errors->has('segundo_nombre'))
												<span class="help-block">{{ $errors->first('segundo_nombre') }}</span>
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
									<div class="form-group {{ ($errors->has('primer_apellido')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('primer_apellido'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Primer apellido
										</label>
										<div class="col-sm-8">
											{!! Form::text('primer_apellido', $socio->tercero->primer_apellido, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Primer apellido']) !!}
											@if ($errors->has('primer_apellido'))
												<span class="help-block">{{ $errors->first('primer_apellido') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('segundo_apellido')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('segundo_apellido'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Segundo apellido
										</label>
										<div class="col-sm-8">
											{!! Form::text('segundo_apellido', $socio->tercero->segundo_apellido, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Segundo apellido']) !!}
											@if ($errors->has('segundo_apellido'))
												<span class="help-block">{{ $errors->first('segundo_apellido') }}</span>
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
									<div class="form-group {{ ($errors->has('fecha_nacimiento')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_nacimiento'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha de nacimiento
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												{!! Form::text('fecha_nacimiento', $socio->tercero->fecha_nacimiento, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_nacimiento'))
												<span class="help-block">{{ $errors->first('fecha_nacimiento') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('ciudad_nacimiento')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('ciudad_nacimiento'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Ciudad de nacimiento
										</label>
										<div class="col-sm-8">
											{!! Form::select('ciudad_nacimiento', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('ciudad_nacimiento'))
												<span class="help-block">{{ $errors->first('ciudad_nacimiento') }}</span>
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
									<div class="form-group {{ ($errors->has('fecha_exp_doc_id')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_exp_doc_id'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha expedición documento identidad
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<div class="input-group-addon">
													<i class="fa fa-calendar"></i>
												</div>
												{!! Form::text('fecha_exp_doc_id', $socio->tercero->fecha_expedicion_documento_identidad, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_exp_doc_id'))
												<span class="help-block">{{ $errors->first('fecha_exp_doc_id') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('ciudad_exp_doc_id')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('ciudad_exp_doc_id'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Ciudad expedición documento identidad
										</label>
										<div class="col-sm-8">
											{!! Form::select('ciudad_exp_doc_id', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('ciudad_exp_doc_id'))
												<span class="help-block">{{ $errors->first('ciudad_exp_doc_id') }}</span>
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
									<div class="form-group {{ ($errors->has('sexo')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('sexo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Sexo
										</label>
										<div class="col-sm-8">
											{!! Form::select('sexo', $sexos, $socio->tercero->sexo_id, ['class' => 'form-control select2', 'placeholder' => 'Seleccione sexo']) !!}
											@if ($errors->has('sexo'))
												<span class="help-block">{{ $errors->first('sexo') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('estado_civil')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('estado_civil'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Estado civil
										</label>
										<div class="col-sm-8">
											{!! Form::select('estado_civil', $estadosCiviles, $socio->estado_civil_id, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un estado civil']) !!}
											@if ($errors->has('estado_civil'))
												<span class="help-block">{{ $errors->first('estado_civil') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row" id="idMujerCabezaFamilia">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('mujer_cabeza_familia')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('mujer_cabeza_familia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Mujer cabeza de familia?
										</label>
										<div class="col-sm-8">
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-primary {{ $socio->es_mujer_cabeza_familia?'active':'' }}">
													{!! Form::radio('mujer_cabeza_familia', '1', $socio->es_mujer_cabeza_familia?true:false) !!}Sí
												</label>
												<label class="btn btn-primary {{ $socio->es_mujer_cabeza_familia?'':'active' }}">
													{!! Form::radio('mujer_cabeza_familia', '0', $socio->es_mujer_cabeza_familia?false:true) !!}No
												</label>
											</div>
											@if ($errors->has('mujer_cabeza_familia'))
												<span class="help-block">{{ $errors->first('mujer_cabeza_familia') }}</span>
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
									<h4>Cuenta transferencias</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('transferencia_banco_id')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('transferencia_banco_id'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Banco
										</label>
										<div class="col-sm-8">
											{!! Form::select('transferencia_banco_id', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('transferencia_banco_id'))
												<span class="help-block">{{ $errors->first('transferencia_banco_id') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('transferencia_numero_cuenta')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('transferencia_numero_cuenta'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Número de cuenta
										</label>
										<div class="col-sm-9">
											{!! Form::text('transferencia_numero_cuenta', $socio->tercero->banco->last() == null ? null : $socio->tercero->banco->last()->pivot->numero , ['class' => 'form-control', 'placeholder' => 'Número cuenta', 'autocomplete' => 'off', 'data-mask' => '00000000000000000000', 'data-mask-reverse' => true, 'autofocus']) !!}
											@if ($errors->has('transferencia_numero_cuenta'))
												<span class="help-block">{{ $errors->first('transferencia_numero_cuenta') }}</span>
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
									<div class="form-group {{ ($errors->has('transferencia_tipo_cuenta')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('transferencia_tipo_cuenta'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo de cuenta
										</label>
										<div class="col-sm-8">
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-primary {{ $socio->tercero->banco->last() == null ? 'active' : ($socio->tercero->banco->last()->pivot->tipo_cuenta == 'AHORROS' ? 'active' :  '')}}">
													{!! Form::radio('transferencia_tipo_cuenta', 'AHORROS', $socio->tercero->banco->last() == null ? true : ($socio->tercero->banco->last()->pivot->tipo_cuenta == 'AHORROS' ? true : false)) !!}Ahorros
												</label>
												<label class="btn btn-primary {{ $socio->tercero->banco->last() == null ? '' : ($socio->tercero->banco->last()->pivot->tipo_cuenta == 'AHORROS' ? '' :  'active')}}">
													{!! Form::radio('transferencia_tipo_cuenta', 'CORRIENTE', $socio->tercero->banco->last() == null ? false : ($socio->tercero->banco->last()->pivot->tipo_cuenta == 'AHORROS' ? false : true)) !!}Corriente
												</label>
											</div>
											@if ($errors->has('transferencia_tipo_cuenta'))
												<span class="help-block">{{ $errors->first('transferencia_tipo_cuenta') }}</span>
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
		$("select[name='ciudad_nacimiento']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $socio->tercero->ciudad_nacimiento_id | old('ciudad_nacimiento') }}"});
		$("select[name='ciudad_exp_doc_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $socio->tercero->ciudad_expedicion_documento_id | old('ciudad_exp_doc_id') }}"});
		$("select[name='transferencia_banco_id']").selectAjax("{{ url('banco/listar') }}", {id:"{{ ($socio->tercero->banco->last() == null ? null : $socio->tercero->banco->last()->id) | old('transferencia_banco_id') }}"});
		function mujerCabezaFamilia()
		{
			if($("select[name='sexo'] option:selected").text() == "femenino")
			{
				$("#idMujerCabezaFamilia").show();
			}
			else
			{
				$("input[name='mujer_cabeza_familia'][value='1']").prop('checked', false);
				$("input[name='mujer_cabeza_familia'][value='1']").parent().removeClass('active');
				$("input[name='mujer_cabeza_familia'][value='0']").prop('checked', true);
				$("input[name='mujer_cabeza_familia'][value='0']").parent().addClass('active');
				$("#idMujerCabezaFamilia").hide();
			}
		}
		mujerCabezaFamilia();
		$("select[name='sexo']").on('change', function(e){mujerCabezaFamilia()});
	});
</script>
@endpush

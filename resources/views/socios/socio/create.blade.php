@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Crear socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Crear socio</li>
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
			{!! Form::open(['url' => 'socio', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" href="#">General</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Laboral</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Contacto</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Beneficiarios</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Imagen</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Financiera</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Tarjetas de crédito</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Obligaciones financieras</a>
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
											$valid = $errors->has('tipo_identificacion') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tipo identificación</label>
										{!! Form::select('tipo_identificacion', $tiposIdentificacion, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('tipo_identificacion'))
											<div class="invalid-feedback">{{ $errors->first('tipo_identificacion') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('identificacion') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Número de identificación</label>
										{!! Form::text('identificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'autofocus']) !!}
										@if ($errors->has('identificacion'))
											<div class="invalid-feedback">{{ $errors->first('identificacion') }}</div>
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
											$valid = $errors->has('primer_nombre') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Primer nombre</label>
										{!! Form::text('primer_nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer nombre']) !!}
										@if ($errors->has('primer_nombre'))
											<div class="invalid-feedback">{{ $errors->first('primer_nombre') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('segundo_nombre') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Segundo nombre</label>
										{!! Form::text('segundo_nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Segundo nombre']) !!}
										@if ($errors->has('segundo_nombre'))
											<div class="invalid-feedback">{{ $errors->first('segundo_nombre') }}</div>
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
											$valid = $errors->has('primer_apellido') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Primer apellido</label>
										{!! Form::text('primer_apellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Primer apellido']) !!}
										@if ($errors->has('primer_apellido'))
											<div class="invalid-feedback">{{ $errors->first('primer_apellido') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('segundo_apellido') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Segundo apellido</label>
										{!! Form::text('segundo_apellido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Segundo apellido']) !!}
										@if ($errors->has('segundo_apellido'))
											<div class="invalid-feedback">{{ $errors->first('segundo_apellido') }}</div>
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
											$valid = $errors->has('fecha_nacimiento') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha de nacimiento</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_nacimiento', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_nacimiento'))
												<div class="invalid-feedback">{{ $errors->first('fecha_nacimiento') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('ciudad_nacimiento') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Ciudad de nacimiento</label>
										{!! Form::select('ciudad_nacimiento', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('ciudad_nacimiento'))
											<div class="invalid-feedback">{{ $errors->first('ciudad_nacimiento') }}</div>
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
											$valid = $errors->has('fecha_exp_doc_id') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha expedición documento identidad</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_exp_doc_id', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_exp_doc_id'))
												<div class="invalid-feedback">{{ $errors->first('fecha_exp_doc_id') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('ciudad_exp_doc_id') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Ciudad expedición documento identidad</label>
										{!! Form::select('ciudad_exp_doc_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('ciudad_exp_doc_id'))
											<div class="invalid-feedback">{{ $errors->first('ciudad_exp_doc_id') }}</div>
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
											$valid = $errors->has('sexo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">sexo</label>
										{!! Form::select('sexo', $sexos, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione sexo']) !!}
										@if ($errors->has('sexo'))
											<div class="invalid-feedback">{{ $errors->first('sexo') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('estado_civil') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Estado civil</label>
										{!! Form::select('estado_civil', $estadosCiviles, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione un estado civil']) !!}
										@if ($errors->has('estado_civil'))
											<div class="invalid-feedback">{{ $errors->first('estado_civil') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row" id="idMujerCabezaFamilia">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">¿Mujer cabeza de familia?</label>
										<div>
											@php
												$valid = $errors->has('mujer_cabeza_familia') ? 'is-invalid' : '';
												$estaActivo = empty(old('mujer_cabeza_familia')) ? false : old('mujer_cabeza_familia');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
													{!! Form::radio('mujer_cabeza_familia', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$estaActivo ? 'active' : '' }}">
													{!! Form::radio('mujer_cabeza_familia', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('mujer_cabeza_familia'))
												<div class="invalid-feedback">{{ $errors->first('mujer_cabeza_familia') }}</div>
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
									<div class="form-group">
										@php
											$valid = $errors->has('transferencia_banco_id') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Banco</label>
										{!! Form::select('transferencia_banco_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('transferencia_banco_id'))
											<div class="invalid-feedback">{{ $errors->first('transferencia_banco_id') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('transferencia_numero_cuenta') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Número de cuenta</label>
										{!! Form::text('transferencia_numero_cuenta', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de cuenta', 'data-mask' => '00000000000000000000', 'data-mask-reverse' => true]) !!}
										@if ($errors->has('transferencia_numero_cuenta'))
											<div class="invalid-feedback">{{ $errors->first('transferencia_numero_cuenta') }}</div>
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
										<label class="control-label">Tipo de cuenta</label>
										<div>
											@php
												$valid = $errors->has('transferencia_tipo_cuenta') ? 'is-invalid' : '';
												$tipoCuenta = empty(old('transferencia_tipo_cuenta')) ? 'AHORROS' : old('transferencia_tipo_cuenta');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $tipoCuenta ? 'active' : '' }}">
													{!! Form::radio('transferencia_tipo_cuenta', 'AHORROS', ($tipoCuenta ? true : false), ['class' => [$valid]]) !!}Ahorros
												</label>
												<label class="btn btn-primary {{ !$tipoCuenta ? 'active' : '' }}">
													{!! Form::radio('transferencia_tipo_cuenta', 'CORRIENTE', (!$tipoCuenta ? true : false ), ['class' => [$valid]]) !!}Corriente
												</label>
											</div>
											@if ($errors->has('transferencia_tipo_cuenta'))
												<div class="invalid-feedback">{{ $errors->first('transferencia_tipo_cuenta') }}</div>
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
					<a href="{{ url('socio') }}" class="btn btn-outline-danger">Cancelar</a>
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
		$("select[name='ciudad_nacimiento']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_nacimiento') }}"});
		$("select[name='ciudad_exp_doc_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_exp_doc_id') }}"});
		$("select[name='transferencia_banco_id']").selectAjax("{{ url('banco/listar') }}", {id:"{{ old('transferencia_banco_id') }}"});
		function mujerCabezaFamilia() {
			if($("select[name='sexo'] option:selected").text() == "femenino") {
				$("#idMujerCabezaFamilia").show();
			}
			else {
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

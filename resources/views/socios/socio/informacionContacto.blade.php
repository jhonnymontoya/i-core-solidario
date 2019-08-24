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
			{!! Form::model($socio, ['url' => ['socio', $socio, 'contacto'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'data-maskMoney-removeMask']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							<?php
								$direccionResidencial = null;
								$direccionLaboral = null;
								$tipoVivienda = null;
								$estrato = null;
								$celularResidencial = null;
								$celularLaboral = null;
								$telefonoResidencial = null;
								$telefonoLaboral = null;
								$extencion = null;
								$emailResidencial = null;
								$emailLaboral = null;
								$preferidoResidencial = null;
								$preferidoLaboral = null;
								foreach($socio->tercero->contactos as $contacto)
								{
									if($contacto->tipo_contacto == "RESIDENCIAL")
									{
										$direccionResidencial = $contacto->direccion;
										$tipoVivienda = $contacto->tipo_vivienda_id;
										$estrato = $contacto->estrato;
										$celularResidencial = $contacto->movil;
										$telefonoResidencial = $contacto->telefono;
										$emailResidencial = $contacto->email;
										$preferidoResidencial = $contacto->es_preferido;
									}
									if($contacto->tipo_contacto == "LABORAL")
									{
										$direccionLaboral = $contacto->direccion;
										$celularLaboral = $contacto->movil;
										$telefonoLaboral = $contacto->telefono;
										$extencion = $contacto->extension;
										$emailLaboral = $contacto->email;
										$preferidoLaboral = $contacto->es_preferido;
									}
								}
								$preferidoLaboral = $preferidoResidencial ? false : true;
							?>
							{{-- INICIO FILA --}}
							<div class="row">
								<div class="col-md-12">
									<h4>Residencial</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('ciudad_residencial')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('ciudad_residencial'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Ciudad
										</label>
										<div class="col-sm-8">
											{!! Form::select('ciudad_residencial', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('ciudad_residencial'))
												<span class="help-block">{{ $errors->first('ciudad_residencial') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('direccion_residencial')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('direccion_residencial'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Dirección
										</label>
										<div class="col-sm-10">
											{!! Form::text('direccion_residencial', $direccionResidencial, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Dirección', 'autofocus']) !!}
											@if ($errors->has('direccion_residencial'))
												<span class="help-block">{{ $errors->first('direccion_residencial') }}</span>
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
									<div class="form-group {{ ($errors->has('tipo_vivienda')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_vivienda'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo Vivienda
										</label>
										<div class="col-sm-8">
											{!! Form::select('tipo_vivienda', $tiposViviendas, $tipoVivienda, ['class' => 'form-control', 'placeholder' => 'Tipo de vivienda']) !!}
											@if ($errors->has('tipo_vivienda'))
												<span class="help-block">{{ $errors->first('tipo_vivienda') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('estrato_vivienda')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('estrato_vivienda'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Estrato
										</label>
										<div class="col-sm-10">
											{!! Form::text('estrato_vivienda', $estrato, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Estrato']) !!}
											@if ($errors->has('estrato_vivienda'))
												<span class="help-block">{{ $errors->first('estrato_vivienda') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- FIN CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('celular_residencia')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('celular_residencia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Celular
										</label>
										<div class="col-sm-8">
											{!! Form::text('celular_residencia', $celularResidencial, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Celular', 'data-mask' => '(000) 000-0000']) !!}
											@if ($errors->has('celular_residencia'))
												<span class="help-block">{{ $errors->first('celular_residencia') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('telefono_residencia')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('telefono_residencia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Teléfono
										</label>
										<div class="col-sm-10">
											{!! Form::text('telefono_residencia', $telefonoResidencial, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Teléfono', 'data-mask' => '000-0000']) !!}
											@if ($errors->has('telefono_residencia'))
												<span class="help-block">{{ $errors->first('telefono_residencia') }}</span>
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
									<div class="form-group {{ ($errors->has('email_residencia')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('email_residencia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Correo electrónico
										</label>
										<div class="col-sm-8">
											{!! Form::text('email_residencia', $emailResidencial, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
											@if ($errors->has('email_residencia'))
												<span class="help-block">{{ $errors->first('email_residencia') }}</span>
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
									<div class="form-group {{ ($errors->has('preferencia_envio_residencia')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('preferencia_envio_residencia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Contacto preferido?
										</label>
										<div class="col-sm-8">
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $preferidoResidencial ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_residencia', '1', $preferidoResidencial ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-danger {{ $preferidoResidencial ? '' : 'active' }}">
													{!! Form::radio('preferencia_envio_residencia', '0', $preferidoResidencial ? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('preferencia_envio_residencia'))
												<span class="help-block">{{ $errors->first('preferencia_envio_residencia') }}</span>
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
									<h4>Laboral</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('ciudad_laboral')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('ciudad_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Ciudad
										</label>
										<div class="col-sm-8">
											{!! Form::select('ciudad_laboral', [], null, ['class' => 'form-control']) !!}
											@if ($errors->has('ciudad_laboral'))
												<span class="help-block">{{ $errors->first('ciudad_laboral') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('direccion_laboral')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('direccion_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Dirección
										</label>
										<div class="col-sm-10">
											{!! Form::text('direccion_laboral', $direccionLaboral, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Dirección']) !!}
											@if ($errors->has('direccion_laboral'))
												<span class="help-block">{{ $errors->first('direccion_laboral') }}</span>
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
									<div class="form-group {{ ($errors->has('celular_laboral')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('celular_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Celular
										</label>
										<div class="col-sm-8">
											{!! Form::text('celular_laboral', $celularLaboral, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Celular', 'data-mask' => '(000) 000-0000']) !!}
											@if ($errors->has('celular_laboral'))
												<span class="help-block">{{ $errors->first('celular_laboral') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('telefono_laboral')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('telefono_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Teléfono
										</label>
										<div class="col-sm-10">
											{!! Form::text('telefono_laboral', $telefonoLaboral, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Teléfono', 'data-mask' => '000-0000']) !!}
											@if ($errors->has('telefono_laboral'))
												<span class="help-block">{{ $errors->first('telefono_laboral') }}</span>
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
									<div class="form-group {{ ($errors->has('extension_laboral')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('extension_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Extensión
										</label>
										<div class="col-sm-8">
											{!! Form::text('extension_laboral', $extencion, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Extensión']) !!}
											@if ($errors->has('extension_laboral'))
												<span class="help-block">{{ $errors->first('extension_laboral') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('email_laboral')?'has-error':'') }}">
										<label class="col-sm-2 control-label">
											@if ($errors->has('email_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Correo electrónico
										</label>
										<div class="col-sm-10">
											{!! Form::text('email_laboral', $emailLaboral, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
											@if ($errors->has('email_laboral'))
												<span class="help-block">{{ $errors->first('email_laboral') }}</span>
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
									<div class="form-group {{ ($errors->has('preferencia_envio_laboral')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('preferencia_envio_laboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Contacto preferido?
										</label>
										<div class="col-sm-8">
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $preferidoLaboral ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_laboral', '1', $preferidoLaboral ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-danger {{ $preferidoLaboral ? '' : 'active' }}">
													{!! Form::radio('preferencia_envio_laboral', '0', $preferidoLaboral ? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('preferencia_envio_laboral'))
												<span class="help-block">{{ $errors->first('preferencia_envio_laboral') }}</span>
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
		<?php
			$ciudadResidencial = null;
			$ciudadLaboral = null;
			foreach($socio->tercero->contactos as $contacto)
			{
				if($contacto->tipo_contacto == 'RESIDENCIAL')$ciudadResidencial = $contacto->ciudad_id;
				if($contacto->tipo_contacto == 'LABORAL')$ciudadLaboral= $contacto->ciudad_id;
			}
		?>
		$("select[name='ciudad_residencial']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadResidencial | old('ciudad_residencial') }}"});
		$("select[name='ciudad_laboral']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadLaboral | old('ciudad_laboral') }}"});

		$("input[name='preferencia_envio_residencia']").change(function(){
			if($(this).is(':checked') && $(this).val() == 1)
			{
				$("input[name='preferencia_envio_laboral'][value='1']").prop('checked', false);
				$("input[name='preferencia_envio_laboral'][value='1']").parent().removeClass('active');
				$("input[name='preferencia_envio_laboral'][value='0']").prop('checked', true);
				$("input[name='preferencia_envio_laboral'][value='0']").parent().addClass('active');
			}
			else
			{
				$("input[name='preferencia_envio_laboral'][value='1']").prop('checked', true);
				$("input[name='preferencia_envio_laboral'][value='1']").parent().addClass('active');
				$("input[name='preferencia_envio_laboral'][value='0']").prop('checked', false);
				$("input[name='preferencia_envio_laboral'][value='0']").parent().removeClass('active');
			}
		});

		$("input[name='preferencia_envio_laboral']").change(function(){
			if($(this).is(':checked') && $(this).val() == 1)
			{
				$("input[name='preferencia_envio_residencia'][value='0']").prop('checked', true);
				$("input[name='preferencia_envio_residencia'][value='0']").parent().addClass('active');
				$("input[name='preferencia_envio_residencia'][value='1']").prop('checked', false);
				$("input[name='preferencia_envio_residencia'][value='1']").parent().removeClass('active');
			}
			else
			{
				$("input[name='preferencia_envio_residencia'][value='1']").prop('checked', true);
				$("input[name='preferencia_envio_residencia'][value='1']").parent().addClass('active');
				$("input[name='preferencia_envio_residencia'][value='0']").prop('checked', false);
				$("input[name='preferencia_envio_residencia'][value='0']").parent().removeClass('active');
			}
		});
	});
</script>
@endpush

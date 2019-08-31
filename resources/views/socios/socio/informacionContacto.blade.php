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
			{!! Form::model($socio, ['url' => ['socio', $socio, 'contacto'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
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
							<a class="nav-link active" href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a>
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
									<div class="form-group">
										@php
											$valid = $errors->has('ciudad_residencial') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Ciudad</label>
										{!! Form::select('ciudad_residencial', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('ciudad_residencial'))
											<div class="invalid-feedback">{{ $errors->first('ciudad_residencial') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('direccion_residencial') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Dirección</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('direccion_residencial', $direccionResidencial, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Dirección', 'autofocus']) !!}
											@if ($errors->has('direccion_residencial'))
												<div class="invalid-feedback">{{ $errors->first('direccion_residencial') }}</div>
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
											$valid = $errors->has('tipo_vivienda') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tipo Vivienda</label>
										{!! Form::select('tipo_vivienda', $tiposViviendas, $tipoVivienda, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Tipo de vivienda']) !!}
										@if ($errors->has('tipo_vivienda'))
											<div class="invalid-feedback">{{ $errors->first('tipo_vivienda') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('estrato_vivienda') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Estrato</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('estrato_vivienda', $estrato, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Estrato']) !!}
											@if ($errors->has('estrato_vivienda'))
												<div class="invalid-feedback">{{ $errors->first('estrato_vivienda') }}</div>
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
									<div class="form-group">
										@php
											$valid = $errors->has('celular_residencia') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Celular</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('celular_residencia', $celularResidencial, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Celular', 'data-mask' => '(000) 000-0000']) !!}
											@if ($errors->has('celular_residencia'))
												<div class="invalid-feedback">{{ $errors->first('celular_residencia') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('telefono_residencia') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Teléfono</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('telefono_residencia', $telefonoResidencial, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Teléfono', 'data-mask' => '000-0000']) !!}
											@if ($errors->has('telefono_residencia'))
												<div class="invalid-feedback">{{ $errors->first('telefono_residencia') }}</div>
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
											$valid = $errors->has('email_residencia') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Correo electrónico</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('email_residencia', $emailResidencial, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
											@if ($errors->has('email_residencia'))
												<div class="invalid-feedback">{{ $errors->first('email_residencia') }}</div>
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
										<label class="control-label">¿Contacto preferido?</label>
										<div>
											@php
												$valid = $errors->has('preferencia_envio_residencia') ? 'is-invalid' : '';
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $preferidoResidencial ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_residencia', 1, ($preferidoResidencial ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-danger {{ !$preferidoResidencial ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_residencia', 0, (!$preferidoResidencial ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('preferencia_envio_residencia'))
												<div class="invalid-feedback">{{ $errors->first('preferencia_envio_residencia') }}</div>
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
									<div class="form-group">
										@php
											$valid = $errors->has('ciudad_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Ciudad</label>
										{!! Form::select('ciudad_laboral', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('ciudad_laboral'))
											<div class="invalid-feedback">{{ $errors->first('ciudad_laboral') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('direccion_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Dirección</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('direccion_laboral', $direccionLaboral, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Dirección']) !!}
											@if ($errors->has('direccion_laboral'))
												<div class="invalid-feedback">{{ $errors->first('direccion_laboral') }}</div>
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
											$valid = $errors->has('celular_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Celular</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('celular_laboral', $celularLaboral, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Celular', 'data-mask' => '(000) 000-0000']) !!}
											@if ($errors->has('celular_laboral'))
												<div class="invalid-feedback">{{ $errors->first('celular_laboral') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('telefono_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Teléfono</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('telefono_laboral', $telefonoLaboral, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Teléfono', 'data-mask' => '000-0000']) !!}
											@if ($errors->has('telefono_laboral'))
												<div class="invalid-feedback">{{ $errors->first('telefono_laboral') }}</div>
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
											$valid = $errors->has('extension_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Extensión</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('extension_laboral', $extencion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Extensión']) !!}
											@if ($errors->has('extension_laboral'))
												<div class="invalid-feedback">{{ $errors->first('extension_laboral') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('email_laboral') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Correo electrónico</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('email_laboral', $emailLaboral, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
											@if ($errors->has('email_laboral'))
												<div class="invalid-feedback">{{ $errors->first('email_laboral') }}</div>
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
										<label class="control-label">¿Contacto preferido?</label>
										<div>
											@php
												$valid = $errors->has('preferencia_envio_laboral') ? 'is-invalid' : '';
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $preferidoLaboral ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_laboral', 1, ($preferidoLaboral ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-danger {{ !$preferidoLaboral ? 'active' : '' }}">
													{!! Form::radio('preferencia_envio_laboral', 0, (!$preferidoLaboral ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('preferencia_envio_laboral'))
												<div class="invalid-feedback">{{ $errors->first('preferencia_envio_laboral') }}</div>
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

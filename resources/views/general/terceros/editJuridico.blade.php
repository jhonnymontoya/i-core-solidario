@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Terceros
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Terceros</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		{!! Form::model($tercero, ['url' => ['tercero', $tercero], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar tercero</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">&nbsp;</label>
									<br>
									<dl class="dl-horizontal">
										<dt>Tipo de persona:</dt>
										<dd><span class="label label-primary">{{ $tercero->tipo_tercero }}</span></dd>
									</dl>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('esta_activo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Estado
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										@php
											$estaActivo = $tercero->esta_activo;
											if(old('esta_activo') !== null) {
												$estaActivo = old('esta_activo') === '1' ? true : false;
											}
										@endphp
										<label class="btn btn-primary{{ $estaActivo ? ' active' : '' }}">
											{!! Form::radio('esta_activo', '1', $estaActivo ? true : false) !!}Activo
										</label>
										<label class="btn btn-danger{{ !$estaActivo ? ' active' : '' }}">
											{!! Form::radio('esta_activo', '0', $estaActivo ? false : true) !!}Inactivo
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<span class="help-block">{{ $errors->first('esta_activo') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('tipo_identificacion_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipo_identificacion_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo identificación
									</label>
									{!! Form::select('tipo_identificacion_id', $tiposIdentificacion, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una opción']) !!}
									@if ($errors->has('tipo_identificacion_id'))
										<span class="help-block">{{ $errors->first('tipo_identificacion_id') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-6">
								<div class="row">
									<div class="col-md-10">
										<div class="form-group {{ ($errors->has('numero_identificacion')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('numero_identificacion'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Número de identificación
											</label>
											{!! Form::text('numero_identificacion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación']) !!}
											@if ($errors->has('numero_identificacion'))
												<span class="help-block">{{ $errors->first('numero_identificacion') }}</span>
											@endif
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group {{ ($errors->has('numero_identificacion')?'has-error':'') }}">
											<label class="control-label">
												DV
											</label>
											<br>
											<label class="dv">0</label>
										</div>
									</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-9">
								<div class="form-group {{ ($errors->has('razon_social')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('razon_social'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Razón social
									</label>
									{!! Form::text('razon_social', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Razón social']) !!}
									@if ($errors->has('razon_social'))
										<span class="help-block">{{ $errors->first('razon_social') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('sigla')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('sigla'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Sigla
									</label>
									{!! Form::text('sigla', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Sigla']) !!}
									@if ($errors->has('sigla'))
										<span class="help-block">{{ $errors->first('sigla') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fecha_constitucion')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fecha_constitucion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha de Constitución
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha_constitucion', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_constitucion'))
										<span class="help-block">{{ $errors->first('fecha_constitucion') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('ciudad_constitucion_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('ciudad_constitucion_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Ciudad de constitución
									</label>
									{!! Form::select('ciudad_constitucion_id', [], null, ['class' => 'form-control select2']) !!}
									@if ($errors->has('ciudad_constitucion_id'))
										<span class="help-block">{{ $errors->first('ciudad_constitucion_id') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('numero_matricula')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('numero_matricula'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número matricula mercantil
									</label>
									{!! Form::text('numero_matricula', null, ['class' => 'form-control', 'placeholder' => 'Número matricula mercantil', 'autocomplete' => 'off']) !!}
									@if ($errors->has('numero_matricula'))
										<span class="help-block">{{ $errors->first('numero_matricula') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('matricula_renovada')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('matricula_renovada'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Matricula mercanti renovada?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										@php
											$matriculaRenoada = $tercero->matricula_renovada;
											if(old('matricula_renovada') !== null) {
												$matriculaRenoada = old('matricula_renovada') === '1' ? true : false;
											}
										@endphp
										<label class="btn btn-primary{{ $matriculaRenoada ? ' active' : '' }}">
											{!! Form::radio('matricula_renovada', '1', $matriculaRenoada ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger{{ !$matriculaRenoada ? ' active' : '' }}">
											{!! Form::radio('matricula_renovada', '0', $matriculaRenoada ? false : true) !!}No
										</label>
									</div>
									@if ($errors->has('matricula_renovada'))
										<span class="help-block">{{ $errors->first('matricula_renovada') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('actividad_economica_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('actividad_economica_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Actividad económica
									</label>
									{!! Form::select('actividad_economica_id', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una actividad económica']) !!}
									@if ($errors->has('actividad_economica_id'))
										<span class="help-block">{{ $errors->first('actividad_economica_id') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<h4>Contacto</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('ciudad_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('ciudad_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Ciudad
									</label>
									{!! Form::select('ciudad_id', [], optional($contacto)->ciudad_id, ['class' => 'form-control select2']) !!}
									@if ($errors->has('ciudad_id'))
										<span class="help-block">{{ $errors->first('ciudad_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('direccion')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('direccion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Dirección
									</label>
									{!! Form::text('direccion', optional($contacto)->direccion, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Dirección']) !!}
									@if ($errors->has('direccion'))
										<span class="help-block">{{ $errors->first('direccion') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('telefono')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('telefono'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Teléfono
									</label>
									{!! Form::text('telefono', optional($contacto)->telefono, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Teléfono', 'data-mask' => '000-0000']) !!}
									@if ($errors->has('telefono'))
										<span class="help-block">{{ $errors->first('telefono') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('extension')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('extension'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Extensión
									</label>
									{!! Form::text('extension', optional($contacto)->extension, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Extensión']) !!}
									@if ($errors->has('extension'))
										<span class="help-block">{{ $errors->first('extension') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('movil')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('movil'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Celular
									</label>
									{!! Form::text('movil', optional($contacto)->movil, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Celular', 'data-mask' => '(000) 000-0000']) !!}
									@if ($errors->has('movil'))
										<span class="help-block">{{ $errors->first('movil') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('email')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('email'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Correo electrónico
									</label>
									{!! Form::text('email', optional($contacto)->email, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
									@if ($errors->has('email'))
										<span class="help-block">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tercero') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
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
		@php
			$actividadEconomica = old('actividad_economica_id') !== null ? old('actividad_economica_id') : $tercero->actividad_economica_id;
			$ciudadConstitucion = old('ciudad_constitucion_id') !== null ? old('ciudad_constitucion_id') : $tercero->ciudad_constitucion_id;
			$ciudadId = old('ciudad_id') !== null ? old('ciudad_id') : optional($contacto)->ciudad_id;

		@endphp
		$("select[name='actividad_economica_id']").selectAjax("{{ url('ciiu') }}", {id:"{{ $actividadEconomica }}"});

		$("input[name='numero_identificacion']").on('keyup keypress blur change focus', function(e){
			digitoVerificacion(this.value, "{{ url('tercero/dv') }}", $(".dv"));
		});
		digitoVerificacion($("input[name='numero_identificacion']").val(), "{{ url('tercero/dv') }}", $(".dv"));
		$("select[name='ciudad_constitucion_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadConstitucion }}"});
		$("select[name='ciudad_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadId }}"});
	});
</script>
@endpush

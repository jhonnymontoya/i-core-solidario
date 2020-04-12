@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Terceros
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Terceros</li>
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
		{!! Form::model($tercero, ['url' => ['tercero', $tercero], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
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
									<dd><span class="badge badge-pill badge-primary">{{ $tercero->tipo_tercero }}</span></dd>
								</dl>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							    <label class="control-label">Estado</label>
							    <div>
							        @php
							            $valid = $errors->has('esta_activo') ? 'is-invalid' : '';
							            $estaActivo = empty(old('esta_activo')) ? $tercero->esta_activo : old('esta_activo');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $estaActivo == 1 ? 'active' : '' }}">
							                {!! Form::radio('esta_activo', 1, ($estaActivo == 1 ? true : false), ['class' => [$valid]]) !!}Activo
							            </label>
							            <label class="btn btn-danger {{ $estaActivo == 0 ? 'active' : '' }}">
							                {!! Form::radio('esta_activo', 0, ($estaActivo == 0 ? true : false ), ['class' => []]) !!}Inactivo
							            </label>
							        </div>
							        @if ($errors->has('esta_activo'))
							            <div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('tipo_identificacion_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Tipo identificacion</label>
							    {!! Form::select('tipo_identificacion_id', $tiposIdentificacion, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('tipo_identificacion_id'))
							        <div class="invalid-feedback">{{ $errors->first('tipo_identificacion_id') }}</div>
							    @endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('numero_identificacion') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Número de identificación</label>
							    {!! Form::number('numero_identificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'autofocus']) !!}
							    @if ($errors->has('numero_identificacion'))
							        <div class="invalid-feedback">{{ $errors->first('numero_identificacion') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
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
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('segundo_nombre') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Otros nombres</label>
							    {!! Form::text('segundo_nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Otros nombres']) !!}
							    @if ($errors->has('segundo_nombre'))
							        <div class="invalid-feedback">{{ $errors->first('segundo_nombre') }}</div>
							    @endif
							</div>
						</div>

						<div class="col-md-3">
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
						<div class="col-md-3">
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
					</div>

					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
							    @php
							        $valid = $errors->has('sexo_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Sexo</label>
							    {!! Form::select('sexo_id', $sexos, null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('sexo_id'))
							        <div class="invalid-feedback">{{ $errors->first('sexo_id') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-2">
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
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('ciudad_nacimiento_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Ciudad de nacimiento</label>
							    {!! Form::select('ciudad_nacimiento_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('ciudad_nacimiento_id'))
							        <div class="invalid-feedback">{{ $errors->first('ciudad_nacimiento_id') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							    @php
							        $valid = $errors->has('fecha_expedicion_documento_identidad') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Fecha expedición ID</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">
							                <i class="fa fa-calendar"></i>
							            </span>
							        </div>
							        {!! Form::text('fecha_expedicion_documento_identidad', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
							        @if ($errors->has('fecha_expedicion_documento_identidad'))
							            <div class="invalid-feedback">{{ $errors->first('fecha_expedicion_documento_identidad') }}</div>
							        @endif
							    </div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('ciudad_expedicion_documento_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Ciudad expedición ID</label>
							    {!! Form::select('ciudad_expedicion_documento_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('ciudad_expedicion_documento_id'))
							        <div class="invalid-feedback">{{ $errors->first('ciudad_expedicion_documento_id') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('actividad_economica_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Actividad económica</label>
							    {!! Form::select('actividad_economica_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('actividad_economica_id'))
							        <div class="invalid-feedback">{{ $errors->first('actividad_economica_id') }}</div>
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
							<div class="form-group">
							    @php
							        $valid = $errors->has('ciudad_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Ciudad</label>
							    {!! Form::select('ciudad_id', [], optional($contacto)->ciudad_id, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('ciudad_id'))
							        <div class="invalid-feedback">{{ $errors->first('ciudad_id') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('direccion') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Dirección</label>
							    {!! Form::text('direccion', optional($contacto)->direccion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Dirección']) !!}
							    @if ($errors->has('direccion'))
							        <div class="invalid-feedback">{{ $errors->first('direccion') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('telefono') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Teléfono</label>
							    {!! Form::text('telefono', optional($contacto)->telefono, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Teléfono']) !!}
							    @if ($errors->has('telefono'))
							        <div class="invalid-feedback">{{ $errors->first('telefono') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('extension') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Extensión</label>
							    {!! Form::text('extension', optional($contacto)->extension, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Extensión']) !!}
							    @if ($errors->has('extension'))
							        <div class="invalid-feedback">{{ $errors->first('extension') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('movil') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Celular</label>
							    {!! Form::text('movil', optional($contacto)->movil, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Celular']) !!}
							    @if ($errors->has('movil'))
							        <div class="invalid-feedback">{{ $errors->first('movil') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('email') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Correo electrónico</label>
							    {!! Form::text('email', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
							    @if ($errors->has('email'))
							        <div class="invalid-feedback">{{ $errors->first('email') }}</div>
							    @endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tercero') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
			$ciudadNacimiento = old('ciudad_nacimiento_id') !== null ? old('ciudad_nacimiento_id') : $tercero->ciudad_nacimiento_id;
			$ciudadExpedicionDocumento = old('ciudad_expedicion_documento_id') !== null ? old('ciudad_expedicion_documento_id') : $tercero->ciudad_expedicion_documento_id;
			$ciudadId = old('ciudad_id') !== null ? old('ciudad_id') : optional($contacto)->ciudad_id;

		@endphp
		$("select[name='actividad_economica_id']").selectAjax("{{ url('ciiu') }}", {id:"{{ $actividadEconomica }}"});

		$("select[name='ciudad_nacimiento_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadNacimiento }}"});
		$("select[name='ciudad_expedicion_documento_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadExpedicionDocumento }}"});
		$("select[name='ciudad_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ $ciudadId }}"});
	});
</script>
@endpush

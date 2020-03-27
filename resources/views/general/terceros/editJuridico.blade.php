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
							<div class="row">
								<div class="col-md-10">
									<div class="form-group">
									    @php
									        $valid = $errors->has('numero_identificacion') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Número de identificación</label>
									    {!! Form::text('numero_identificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación', 'autofocus']) !!}
									    @if ($errors->has('numero_identificacion'))
									        <div class="invalid-feedback">{{ $errors->first('numero_identificacion') }}</div>
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
							<div class="form-group">
							    @php
							        $valid = $errors->has('razon_social') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Razón social</label>
							    {!! Form::text('razon_social', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Razón ocial']) !!}
							    @if ($errors->has('razon_social'))
							        <div class="invalid-feedback">{{ $errors->first('razon_social') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
							    @php
							        $valid = $errors->has('sigla') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Sigla</label>
							    {!! Form::text('sigla', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Sigla']) !!}
							    @if ($errors->has('sigla'))
							        <div class="invalid-feedback">{{ $errors->first('sigla') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('fecha_constitucion') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Fecha de Constitución</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">
							                <i class="fa fa-calendar"></i>
							            </span>
							        </div>
							        {!! Form::text('fecha_constitucion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
							        @if ($errors->has('fecha_constitucion'))
							            <div class="invalid-feedback">{{ $errors->first('fecha_constitucion') }}</div>
							        @endif
							    </div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('ciudad_constitucion_id') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Ciudad de constitución</label>
							    {!! Form::select('ciudad_constitucion_id', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('ciudad_constitucion_id'))
							        <div class="invalid-feedback">{{ $errors->first('ciudad_constitucion_id') }}</div>
							    @endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('numero_matricula') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Número matricula mercantil</label>
							    {!! Form::text('numero_matricula', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número matricula mercantil']) !!}
							    @if ($errors->has('numero_matricula'))
							        <div class="invalid-feedback">{{ $errors->first('numero_matricula') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
							    <label class="control-label">¿Matricula mercanti renovada?</label>
							    <div>
							        @php
							            $valid = $errors->has('matricula_renovada') ? 'is-invalid' : '';
							            $matriculaRenoada = empty(old('matricula_renovada')) ? $tercero->matricula_renovada : old('matricula_renovada');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $matriculaRenoada == 1 ? 'active' : '' }}">
							                {!! Form::radio('matricula_renovada', 1, ($matriculaRenoada == 1 ? true : false), ['class' => [$valid]]) !!}Sí
							            </label>
							            <label class="btn btn-danger {{ $matriculaRenoada == 0 ? 'active' : '' }}">
							                {!! Form::radio('matricula_renovada', 0, ($matriculaRenoada == 0 ? true : false ), ['class' => []]) !!}No
							            </label>
							        </div>
							        @if ($errors->has('matricula_renovada'))
							            <div class="invalid-feedback">{{ $errors->first('matricula_renovada') }}</div>
							        @endif
							    </div>
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
							    {!! Form::text('email', optional($contacto)->email, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Correo electrónico']) !!}
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Crear entidad
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Crear entidad</li>
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
			{!! Form::open(['url' => 'entidad', 'method' => 'post', 'role' => 'form']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" href="#">Información básica</a>
						</li>
						<li class="nav-item">
							<a class="nav-link disabled">Imágenes</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
									    @php
									        $valid = $errors->has('razon') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Razón social</label>
									    {!! Form::text('razon', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Razón social', 'autofocus']) !!}
									    @if ($errors->has('razon'))
									        <div class="invalid-feedback">{{ $errors->first('razon') }}</div>
									    @endif
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
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
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-10">
											<div class="form-group">
											    @php
											        $valid = $errors->has('nit') ? 'is-invalid' : '';
											    @endphp
											    <label class="control-label">Número de identificación tributaria</label>
											    {!! Form::text('nit', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria']) !!}
											    @if ($errors->has('nit'))
											        <div class="invalid-feedback">{{ $errors->first('nit') }}</div>
											    @endif
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group">
												<label class="control-label">DV</label>
												<br>
												<label class="dv">0</label>
											</div>
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('actividad_economica') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Actividad económica</label>
									    {!! Form::select('actividad_economica', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
									    @if ($errors->has('actividad_economica'))
									        <div class="invalid-feedback">{{ $errors->first('actividad_economica') }}</div>
									    @endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('fecha_inicio_contabilidad') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Fecha de nicio de contabilidad</label>
									    <div class="input-group">
									        <div class="input-group-prepend">
									            <span class="input-group-text">
									                <i class="fa fa-calendar"></i>
									            </span>
									        </div>
									        {!! Form::text('fecha_inicio_contabilidad', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									        @if ($errors->has('fecha_inicio_contabilidad'))
									            <div class="invalid-feedback">{{ $errors->first('fecha_inicio_contabilidad') }}</div>
									        @endif
									    </div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									    <label class="control-label">¿Usa dependencias?</label>
									    <div>
									        @php
									            $valid = $errors->has('usa_dependencia') ? 'is-invalid' : '';
									            $usaDependencias = empty(old('usa_dependencia')) ? 0 : old('usa_dependencia');
									        @endphp
									        <div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary {{ $usaDependencias == 1 ? 'active' : '' }}">
									                {!! Form::radio('usa_dependencia', 1, ($usaDependencias == 1 ? true : false), ['class' => [$valid]]) !!}Sí
									            </label>
									            <label class="btn btn-danger {{ $usaDependencias == 0 ? 'active' : '' }}">
									                {!! Form::radio('usa_dependencia', 0, ($usaDependencias == 0 ? true : false ), ['class' => []]) !!}No
									            </label>
									        </div>
									        @if ($errors->has('usa_dependencia'))
									            <div class="invalid-feedback">{{ $errors->first('usa_dependencia') }}</div>
									        @endif
									    </div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									    <label class="control-label">¿Usa centrs de costo?</label>
									    <div>
									        @php
									            $valid = $errors->has('usa_centro_costos') ? 'is-invalid' : '';
									            $centoCostos = empty(old('usa_centro_costos')) ? 0 : old('usa_centro_costos');
									        @endphp
									        <div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary {{ $centoCostos == 1 ? 'active' : '' }}">
									                {!! Form::radio('usa_centro_costos', 1, ($centoCostos == 1 ? true : false), ['class' => [$valid]]) !!}Sí
									            </label>
									            <label class="btn btn-danger {{ $centoCostos == 0 ? 'active' : '' }}">
									                {!! Form::radio('usa_centro_costos', 0, ($centoCostos == 0 ? true : false ), ['class' => []]) !!}No
									            </label>
									        </div>
									        @if ($errors->has('usa_centro_costos'))
									            <div class="invalid-feedback">{{ $errors->first('usa_centro_costos') }}</div>
									        @endif
									    </div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
									    @php
									        $valid = $errors->has('pagina_web') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Página web</label>
									    {!! Form::text('pagina_web', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Página web']) !!}
									    @if ($errors->has('pagina_web'))
									        <div class="invalid-feedback">{{ $errors->first('pagina_web') }}</div>
									    @endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('entidad') }}" class="btn btn-outline-danger">Cancelar</a>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.dv {
		display: block;
		width: 50%;
		height: 34px;
		padding: 6px 10px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='actividad_economica']").selectAjax("{{ url('ciiu') }}", {id:"{{ old('actividad_economica') }}"});

		$("input[name='nit']").on('keyup keypress blur change focus', function(e){
			digitoVerificacion(this.value, "{{ url('tercero/dv') }}", $(".dv"));
		});
		digitoVerificacion($("input[name='nit']").val(), "{{ url('tercero/dv') }}", $(".dv"));
	});
</script>
@endpush

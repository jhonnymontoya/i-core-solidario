@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Crear entidad
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Entidad</li>
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

		<div class="row">
			{!! Form::open(['url' => 'entidad', 'method' => 'post', 'role' => 'form']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="{{ url('entidad/create') }}">Información básica</a></li>
						<li role="presentation" class="disabled"><a>Imágenes</a></li>
					</ul>
					<div class="tab-content">
						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('razon')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('razon'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Razón social
									</label>
									{!! Form::text('razon', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Razón social', 'autofocus']) !!}
									@if ($errors->has('razon'))
										<span class="help-block">{{ $errors->first('razon') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
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
							<div class="col-md-6">
								<div class="row">
									<div class="col-md-10">
										<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('nit'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Número de identificación tributaria
											</label>
											{!! Form::text('nit', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria']) !!}
											@if ($errors->has('nit'))
												<span class="help-block">{{ $errors->first('nit') }}</span>
											@endif
										</div>
									</div>
									<div class="col-md-2">
										<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
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
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('actividad_economica')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('actividad_economica'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Actividad económica
									</label>
									{!! Form::select('actividad_economica', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una actividad económica']) !!}
									@if ($errors->has('actividad_economica'))
										<span class="help-block">{{ $errors->first('actividad_economica') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fecha_inicio_contabilidad')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fecha_inicio_contabilidad'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha de inicio de contabilidad
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha_inicio_contabilidad', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_inicio_contabilidad'))
										<span class="help-block">{{ $errors->first('fecha_inicio_contabilidad') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('usa_dependencia')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('usa_dependencia'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Usa dependencias?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary">
											{!! Form::radio('usa_dependencia', '1', false) !!}Sí
										</label>
										<label class="btn btn-danger active">
											{!! Form::radio('usa_dependencia', '0', true) !!}No
										</label>
									</div>
									@if ($errors->has('usa_dependencia'))
										<span class="help-block">{{ $errors->first('usa_dependencia') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('usa_centro_costos')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('usa_centro_costos'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Usa centros de costo?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary">
											{!! Form::radio('usa_centro_costos', '1', false) !!}Sí
										</label>
										<label class="btn btn-danger active">
											{!! Form::radio('usa_centro_costos', '0', true) !!}No
										</label>
									</div>
									@if ($errors->has('usa_centro_costos'))
										<span class="help-block">{{ $errors->first('usa_centro_costos') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('pagina_web')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('pagina_web'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Página web
									</label>
									{!! Form::text('pagina_web', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Página web']) !!}
									@if ($errors->has('pagina_web'))
										<span class="help-block">{{ $errors->first('pagina_web') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('entidad') }}" class="btn btn-danger pull-right">Cancelar</a>
								</div>
							</div>
						</div>
					</div>
					<br>
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

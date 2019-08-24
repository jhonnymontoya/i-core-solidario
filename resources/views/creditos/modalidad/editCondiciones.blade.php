@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Modalidades de créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Modalidades de créditos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'condiciones'], 'method' => 'put', 'role' => 'form']) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar modalidad</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código
								</label>
								{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('es_exclusivo_de_socios')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('es_exclusivo_de_socios'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Exclusiva para socios?
								</label>
								<br>
								<?php
									$es_exclusivo_de_socios = $modalidad->es_exclusivo_de_socios;
									if(old('es_exclusivo_de_socios') == '0')
									{
										$es_exclusivo_de_socios = false;
									}
									elseif(old('es_exclusivo_de_socios') == '1')
									{
										$es_exclusivo_de_socios = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $es_exclusivo_de_socios ? 'active' : ''}}">
										{!! Form::radio('es_exclusivo_de_socios', '1', $es_exclusivo_de_socios ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ $es_exclusivo_de_socios ? '' : 'active'}}">
										{!! Form::radio('es_exclusivo_de_socios', '0', $es_exclusivo_de_socios? false : true) !!}No
									</label>
								</div>
								@if ($errors->has('es_exclusivo_de_socios'))
									<span class="help-block">{{ $errors->first('es_exclusivo_de_socios') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<br>
								<?php
									$esta_activa = $modalidad->esta_activa;
									if(old('esta_activa') == '0')
									{
										$esta_activa = false;
									}
									elseif(old('esta_activa') == '1')
									{
										$esta_activa = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $esta_activa ? 'active' : ''}}">
										{!! Form::radio('esta_activa', '1', $esta_activa ? true : false) !!}Activa
									</label>
									<label class="btn btn-outline-danger {{ $esta_activa ? '' : 'active'}}">
										{!! Form::radio('esta_activa', '0', $esta_activa? false : true) !!}Inactiva
									</label>
								</div>
								@if ($errors->has('esta_activa'))
									<span class="help-block">{{ $errors->first('esta_activa') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								{!! Form::textarea('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<span class="help-block">{{ $errors->first('descripcion') }}</span>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li role="presentation" class="active">
							<a href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active">
							<br>
							<div class="row form-horizontal">
								<div class="col-md-2"></div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('requiereAntiguedadEntidad')?'has-error':'') }}">
										<label class="col-sm-8 control-label">
											@if ($errors->has('requiereAntiguedadEntidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Requiere antigüedad en la entidad?
										</label>
										<div class="col-sm-4">
											<?php
												$requiereAntiguedadEntidad = empty($modalidad->minimo_antiguedad_entidad) ? false : true;
												if(old('requiereAntiguedadEntidad') == '0')
												{
													$requiereAntiguedadEntidad = false;
												}
												elseif(old('requiereAntiguedadEntidad') == '1')
												{
													$requiereAntiguedadEntidad = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $requiereAntiguedadEntidad ? 'active' : ''}}">
													{!! Form::radio('requiereAntiguedadEntidad', '1', $requiereAntiguedadEntidad ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-primary {{ $requiereAntiguedadEntidad ? '' : 'active'}}">
													{!! Form::radio('requiereAntiguedadEntidad', '0', $requiereAntiguedadEntidad? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('requiereAntiguedadEntidad'))
												<span class="help-block">{{ $errors->first('requiereAntiguedadEntidad') }}</span>
											@endif
										</div>
									</div>
								</div>								
								<div class="col-md-5">
									<div class="form-group {{ ($errors->has('minimo_antiguedad_entidad')?'has-error':'') }}">
										<label class="col-sm-6 control-label">
											@if ($errors->has('minimo_antiguedad_entidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Meses de antigüedad requeridos
										</label>
										<div class="col-sm-6">
											@if($requiereAntiguedadEntidad)
												{!! Form::number('minimo_antiguedad_entidad', null, ['class' => 'form-control', 'placeholder' => 'Meses antiguedad', 'autocomplete' => 'off']) !!}
											@else
												{!! Form::number('minimo_antiguedad_entidad', null, ['class' => 'form-control', 'placeholder' => 'Meses antiguedad', 'autocomplete' => 'off', 'readonly']) !!}
											@endif
											@if ($errors->has('minimo_antiguedad_entidad'))
												<span class="help-block">{{ $errors->first('minimo_antiguedad_entidad') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>

							<div class="row form-horizontal">
								<div class="col-md-2"></div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('requiereAntiguedadLaboral')?'has-error':'') }}">
										<label class="col-sm-8 control-label">
											@if ($errors->has('requiereAntiguedadLaboral'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Requiere antigüedad en la empresa?
										</label>
										<div class="col-sm-4">
											<?php
												$requiereAntiguedadLaboral = empty($modalidad->minimo_antiguedad_empresa) ? false : true;
												if(old('requiereAntiguedadLaboral') == '0')
												{
													$requiereAntiguedadLaboral = false;
												}
												elseif(old('requiereAntiguedadLaboral') == '1')
												{
													$requiereAntiguedadLaboral = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $requiereAntiguedadLaboral ? 'active' : ''}}">
													{!! Form::radio('requiereAntiguedadLaboral', '1', $requiereAntiguedadLaboral ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-primary {{ $requiereAntiguedadLaboral ? '' : 'active'}}">
													{!! Form::radio('requiereAntiguedadLaboral', '0', $requiereAntiguedadLaboral? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('requiereAntiguedadLaboral'))
												<span class="help-block">{{ $errors->first('requiereAntiguedadLaboral') }}</span>
											@endif
										</div>
									</div>
								</div>								
								<div class="col-md-5">
									<div class="form-group {{ ($errors->has('minimo_antiguedad_empresa')?'has-error':'') }}">
										<label class="col-sm-6 control-label">
											@if ($errors->has('minimo_antiguedad_empresa'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Meses de antigüedad en la empresa
										</label>
										<div class="col-sm-6">
											@if($requiereAntiguedadLaboral)
												{!! Form::number('minimo_antiguedad_empresa', null, ['class' => 'form-control', 'placeholder' => 'Meses antiguedad', 'autocomplete' => 'off']) !!}
											@else
												{!! Form::number('minimo_antiguedad_empresa', null, ['class' => 'form-control', 'placeholder' => 'Meses antiguedad', 'autocomplete' => 'off', 'readonly']) !!}
											@endif
											@if ($errors->has('minimo_antiguedad_empresa'))
												<span class="help-block">{{ $errors->first('minimo_antiguedad_empresa') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>

							<div class="row form-horizontal">
								<div class="col-md-2"></div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('limiteObligacionesModalidad')?'has-error':'') }}">
										<label class="col-sm-8 control-label">
											@if ($errors->has('limiteObligacionesModalidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Límite de obligaciones vigentes?
										</label>
										<div class="col-sm-4">
											<?php
												$limiteObligacionesModalidad = empty($modalidad->limite_obligaciones) ? false : true;
												if(old('limiteObligacionesModalidad') == '0')
												{
													$limiteObligacionesModalidad = false;
												}
												elseif(old('limiteObligacionesModalidad') == '1')
												{
													$limiteObligacionesModalidad = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $limiteObligacionesModalidad ? 'active' : ''}}">
													{!! Form::radio('limiteObligacionesModalidad', '1', $limiteObligacionesModalidad ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-primary {{ $limiteObligacionesModalidad ? '' : 'active'}}">
													{!! Form::radio('limiteObligacionesModalidad', '0', $limiteObligacionesModalidad? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('limiteObligacionesModalidad'))
												<span class="help-block">{{ $errors->first('limiteObligacionesModalidad') }}</span>
											@endif
										</div>
									</div>
								</div>								
								<div class="col-md-5">
									<div class="form-group {{ ($errors->has('limite_obligaciones')?'has-error':'') }}">
										<label class="col-sm-6 control-label">
											@if ($errors->has('limite_obligaciones'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Número de obligaciones
										</label>
										<div class="col-sm-6">
											@if($limiteObligacionesModalidad)
												{!! Form::number('limite_obligaciones', null, ['class' => 'form-control', 'placeholder' => 'Número de obligaciones', 'autocomplete' => 'off']) !!}
											@else
												{!! Form::number('limite_obligaciones', null, ['class' => 'form-control', 'placeholder' => 'Número de obligaciones', 'autocomplete' => 'off', 'readonly']) !!}
											@endif
											@if ($errors->has('limite_obligaciones'))
												<span class="help-block">{{ $errors->first('limite_obligaciones') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>

							<div class="row form-horizontal">
								<div class="col-md-2"></div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('intervaloSolcitudes')?'has-error':'') }}">
										<label class="col-sm-8 control-label">
											@if ($errors->has('intervaloSolcitudes'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Tiempo de intervalo para nueva solicitud?
										</label>
										<div class="col-sm-4">
											<?php
												$intervaloSolcitudes = empty($modalidad->intervalo_solicitudes) ? false : true;
												if(old('intervaloSolcitudes') == '0')
												{
													$intervaloSolcitudes = false;
												}
												elseif(old('intervaloSolcitudes') == '1')
												{
													$intervaloSolcitudes = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $intervaloSolcitudes ? 'active' : ''}}">
													{!! Form::radio('intervaloSolcitudes', '1', $intervaloSolcitudes ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-primary {{ $intervaloSolcitudes ? '' : 'active'}}">
													{!! Form::radio('intervaloSolcitudes', '0', $intervaloSolcitudes? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('intervaloSolcitudes'))
												<span class="help-block">{{ $errors->first('intervaloSolcitudes') }}</span>
											@endif
										</div>
									</div>
								</div>								
								<div class="col-md-5">
									<div class="form-group {{ ($errors->has('intervalo_solicitudes')?'has-error':'') }}">
										<label class="col-sm-6 control-label">
											@if ($errors->has('intervalo_solicitudes'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Meses de intervalo
										</label>
										<div class="col-sm-6">
											@if($intervaloSolcitudes)
												{!! Form::number('intervalo_solicitudes', null, ['class' => 'form-control', 'placeholder' => 'Meses', 'autocomplete' => 'off']) !!}
											@else
												{!! Form::number('intervalo_solicitudes', null, ['class' => 'form-control', 'placeholder' => 'Meses', 'autocomplete' => 'off', 'readonly']) !!}
											@endif
											@if ($errors->has('intervalo_solicitudes'))
												<span class="help-block">{{ $errors->first('intervalo_solicitudes') }}</span>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('modalidadCredito') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
	textarea{
		height: 150px !important;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='intervaloSolcitudes']").change(function(){
			var intervaloSolcitudes = ($(this).val() == '1' ? true : false);
			$("input[name='intervalo_solicitudes']").prop('readonly', !intervaloSolcitudes);
		});

		$("input[name='limiteObligacionesModalidad']").change(function(){
			var limiteObligacionesModalidad = ($(this).val() == '1' ? true : false);
			$("input[name='limite_obligaciones']").prop('readonly', !limiteObligacionesModalidad);
		});

		$("input[name='requiereAntiguedadLaboral']").change(function(){
			var requiereAntiguedadLaboral = ($(this).val() == '1' ? true : false);
			$("input[name='minimo_antiguedad_empresa']").prop('readonly', !requiereAntiguedadLaboral);
		});

		$("input[name='requiereAntiguedadEntidad']").change(function(){
			var requiereAntiguedadEntidad = ($(this).val() == '1' ? true : false);
			$("input[name='minimo_antiguedad_entidad']").prop('readonly', !requiereAntiguedadEntidad);
		});
	});
</script>
@endpush

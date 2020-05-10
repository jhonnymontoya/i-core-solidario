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
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Exclusiva para socios?</label>
								<div>
									@php
										$valid = $errors->has('es_exclusivo_de_socios') ? 'is-invalid' : '';
										$exclusivoSocios = empty(old('es_exclusivo_de_socios')) ? $modalidad->es_exclusivo_de_socios : old('es_exclusivo_de_socios');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 1, ($exclusivoSocios ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 0, (!$exclusivoSocios ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('es_exclusivo_de_socios'))
										<div class="invalid-feedback">{{ $errors->first('es_exclusivo_de_socios') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Activa?</label>
								<div>
									@php
										$valid = $errors->has('esta_activa') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activa')) ? $modalidad->esta_activa : old('esta_activa');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::textarea('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditConsultaAsociado', $modalidad) }}">Consulta Asociado</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade show active">
							<br>
							<div class="row">
								<div class="col-md-6 text-center">
									<div class="form-group">
										<label class="control-label">¿Requiere antigüedad en la entidad?</label>
										<div>
											@php
												$valid = $errors->has('requiereAntiguedadEntidad') ? 'is-invalid' : '';
												$requiereAntiguedadEntidad = empty($modalidad->minimo_antiguedad_entidad) ? false : true;
												if(old('requiereAntiguedadEntidad') == '0') {
													$requiereAntiguedadEntidad = false;
												}
												elseif(old('requiereAntiguedadEntidad') == '1') {
													$requiereAntiguedadEntidad = true;
												}
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $requiereAntiguedadEntidad ? 'active' : '' }}">
													{!! Form::radio('requiereAntiguedadEntidad', 1, ($requiereAntiguedadEntidad ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$requiereAntiguedadEntidad ? 'active' : '' }}">
													{!! Form::radio('requiereAntiguedadEntidad', 0, (!$requiereAntiguedadEntidad ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('requiereAntiguedadEntidad'))
												<div class="invalid-feedback">{{ $errors->first('requiereAntiguedadEntidad') }}</div>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										@php
											$valid = $errors->has('minimo_antiguedad_entidad') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Meses de antigüedad requeridos</label>
										{!! Form::number('minimo_antiguedad_entidad', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Meses antigüedad', ($requiereAntiguedadEntidad ? '' : 'readonly' )]) !!}
										@if ($errors->has('minimo_antiguedad_entidad'))
											<div class="invalid-feedback">{{ $errors->first('minimo_antiguedad_entidad') }}</div>
										@endif
									</div>
								</div>
								<div class="col-md-1"></div>
							</div>

							<div class="row">
								<div class="col-md-6 text-center">
									<div class="form-group">
										<label class="control-label">¿Requiere antigüedad en la empresa?</label>
										<div>
											@php
												$valid = $errors->has('requiereAntiguedadLaboral') ? 'is-invalid' : '';
												$requiereAntiguedadLaboral = empty($modalidad->minimo_antiguedad_empresa) ? false : true;
												if(old('requiereAntiguedadLaboral') == '0') {
													$requiereAntiguedadLaboral = false;
												}
												elseif(old('requiereAntiguedadLaboral') == '1') {
													$requiereAntiguedadLaboral = true;
												}
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $requiereAntiguedadLaboral ? 'active' : '' }}">
													{!! Form::radio('requiereAntiguedadLaboral', 1, ($requiereAntiguedadLaboral ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$requiereAntiguedadLaboral ? 'active' : '' }}">
													{!! Form::radio('requiereAntiguedadLaboral', 0, (!$requiereAntiguedadLaboral ? true : false ), ['class' => [$valid]]) !!}no
												</label>
											</div>
											@if ($errors->has('requiereAntiguedadLaboral'))
												<div class="invalid-feedback">{{ $errors->first('requiereAntiguedadLaboral') }}</div>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										@php
											$valid = $errors->has('minimo_antiguedad_empresa') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Meses de antigüedad en la empresa</label>
										{!! Form::number('minimo_antiguedad_empresa', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Meses antigüedad', ($requiereAntiguedadLaboral ? '' : 'readonly')]) !!}
										@if ($errors->has('minimo_antiguedad_empresa'))
											<div class="invalid-feedback">{{ $errors->first('minimo_antiguedad_empresa') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 text-center">
									<div class="form-group">
										<label class="control-label">¿Límite de obligaciones vigentes?</label>
										<div>
											@php
												$valid = $errors->has('limiteObligacionesModalidad') ? 'is-invalid' : '';
												$limiteObligacionesModalidad = empty($modalidad->limite_obligaciones) ? false : true;
												if(old('limiteObligacionesModalidad') == '0') {
													$limiteObligacionesModalidad = false;
												}
												elseif(old('limiteObligacionesModalidad') == '1') {
													$limiteObligacionesModalidad = true;
												}
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $limiteObligacionesModalidad ? 'active' : '' }}">
													{!! Form::radio('limiteObligacionesModalidad', 1, ($limiteObligacionesModalidad ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$limiteObligacionesModalidad ? 'active' : '' }}">
													{!! Form::radio('limiteObligacionesModalidad', 0, (!$limiteObligacionesModalidad ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('limiteObligacionesModalidad'))
												<div class="invalid-feedback">{{ $errors->first('limiteObligacionesModalidad') }}</div>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										@php
											$valid = $errors->has('limite_obligaciones') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Número de obligaciones</label>
										{!! Form::number('limite_obligaciones', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de obligaciones', ($limiteObligacionesModalidad ? '' : 'readonly')]) !!}
										@if ($errors->has('limite_obligaciones'))
											<div class="invalid-feedback">{{ $errors->first('limite_obligaciones') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6 text-center">
									<div class="form-group">
										<label class="control-label">¿Tiempo de intervalo para nueva solicitud?</label>
										<div>
											@php
												$valid = $errors->has('intervaloSolcitudes') ? 'is-invalid' : '';
												$intervaloSolcitudes = empty($modalidad->intervalo_solicitudes) ? false : true;
												if(old('intervaloSolcitudes') == '0') {
													$intervaloSolcitudes = false;
												}
												elseif(old('intervaloSolcitudes') == '1') {
													$intervaloSolcitudes = true;
												}
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $intervaloSolcitudes ? 'active' : '' }}">
													{!! Form::radio('intervaloSolcitudes', 1, ($intervaloSolcitudes ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$intervaloSolcitudes ? 'active' : '' }}">
													{!! Form::radio('intervaloSolcitudes', 0, (!$intervaloSolcitudes ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('intervaloSolcitudes'))
												<div class="invalid-feedback">{{ $errors->first('intervaloSolcitudes') }}</div>
											@endif
										</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group">
										@php
											$valid = $errors->has('intervalo_solicitudes') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Meses</label>
										{!! Form::number('intervalo_solicitudes', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Meses', ($intervaloSolcitudes ? '' : 'readonly')]) !!}
										@if ($errors->has('intervalo_solicitudes'))
											<div class="invalid-feedback">{{ $errors->first('intervalo_solicitudes') }}</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
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

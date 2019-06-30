@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cuotas obligatorias
			<small>Socios</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Socios</a></li>
			<li class="active">Cuotas obligatorias</li>
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
		{!! Form::model($cuota, ['url' => ['tipoCuotaObligatoria', $cuota], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Crear nueva cuota obligatoria</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'readonly']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'readonly']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-5">
								<div class="form-group {{ ($errors->has('cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta auxiliar
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::text('cuif_id', $cuota->cuenta->full, ['class' => 'form-control', 'readonly']) !!}
									</div>
									@if ($errors->has('cuif_id'))
										<span class="help-block">{{ $errors->first('cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('es_reintegrable')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('es_reintegrable'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Es reintegrable?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$reintegro = $cuota->es_reintegrable ? true : false;
										?>
										<label class="btn btn-{{ $reintegro ? 'primary' : 'danger' }}">
											{{ $reintegro ? 'Sí' : 'No' }}
										</label>
									</div>
									@if ($errors->has('es_reintegrable'))
										<span class="help-block">{{ $errors->first('es_reintegrable') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('tipo_calculo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipo_calculo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo cálculo
									</label>
									{!! Form::select('tipo_calculo', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor fijo'], null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Seleccione una opción']) !!}
									@if ($errors->has('tipo_calculo'))
										<span class="help-block">{{ $errors->first('tipo_calculo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('valor'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Valor
									</label>
									{!! Form::number('valor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor', 'step' => '0.01']) !!}
									@if ($errors->has('valor'))
										<span class="help-block">{{ $errors->first('valor') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('tope')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tope'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tope
									</label>
									{!! Form::number('tope', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tope', 'step' => '0.01']) !!}
									@if ($errors->has('tope'))
										<span class="help-block">{{ $errors->first('tope') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('esta_activa'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										¿Esta activo?
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<?php
											$activo = trim(old('esta_activa')) == '' ? $cuota->esta_activa : old('esta_activa');
											$activo = $activo ? true : false;
										?>
										<label class="btn btn-primary {{ $activo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', '1', $activo ? true : false) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$activo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', '0', !$activo ? true : false) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<span class="help-block">{{ $errors->first('esta_activa') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tipoCuotaObligatoria') }}" class="btn btn-danger pull-right">Cancelar</a>
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
@endpush

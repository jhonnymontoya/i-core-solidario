@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas obligatorias
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Cuotas obligatorias</li>
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
		{!! Form::model($cuota, ['url' => ['tipoCuotaObligatoria', $cuota], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva cuota obligatoria</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
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
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'readonly']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta auxiliar</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::text('cuif_id', $cuota->cuenta->full, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cuenta auxiliar', 'readonly']) !!}
									@if ($errors->has('cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Es reintegrable?</label>
								<br>
								<div class="btn-group">
									<?php
										$reintegro = $cuota->es_reintegrable ? true : false;
									?>
									<label class="btn btn-{{ $reintegro ? 'primary' : 'danger' }}">
										{{ $reintegro ? 'Sí' : 'No' }}
									</label>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_calculo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo cálculo</label>
								{!! Form::select('tipo_calculo', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor fijo'], null, ['class' => [$valid, 'form-control', 'select2', 'placeholder' => 'Seleccione una opción']]) !!}
								@if ($errors->has('tipo_calculo'))
									<div class="invalid-feedback">{{ $errors->first('tipo_calculo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('valor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor</label>
								{!! Form::number('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor', 'step' => '0.01']) !!}
								@if ($errors->has('valor'))
									<div class="invalid-feedback">{{ $errors->first('valor') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tope') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tope</label>
								{!! Form::text('tope', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tope', 'step' => '0.01']) !!}
								@if ($errors->has('tope'))
									<div class="invalid-feedback">{{ $errors->first('tope') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Esta activo?</label>
								<div>
									@php
										$activo = trim(old('esta_activa')) == '' ? $cuota->esta_activa : old('esta_activa');
										$activo = $activo ? true : false;
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $activo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($activo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$activo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$activo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoCuotaObligatoria') }}" class="btn btn-outline-danger">Cancelar</a>
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

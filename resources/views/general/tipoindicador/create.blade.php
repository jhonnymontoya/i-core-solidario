@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos de indicadores
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Tipos de indicadores</li>
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
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::open(['url' => 'tipoIndicador', 'method' => 'post', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Crear nuevo tipo de indicador</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-5">
						<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('codigo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Código
							</label>
							{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Código', 'autofocus']) !!}
							@if ($errors->has('codigo'))
								<span class="help-block">{{ $errors->first('codigo') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group {{ ($errors->has('periodicidad')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('periodicidad'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Periodicidad de actualización
							</label>
							{!! Form::select('periodicidad', $periodicidades, null, ['class' => 'form-control', 'placeholder' => 'Seleccione una periodicidad']) !!}
							@if ($errors->has('periodicidad'))
								<span class="help-block">{{ $errors->first('periodicidad') }}</span>
							@endif
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group {{ ($errors->has('variable')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('variable'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Variable
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<?php
									$tipoVariable = empty(old('variable')) ? true : false;
									if(empty(old('variable')))
									{
										$tipoVariable = true;
									}
									else if(old('variable') == 'PORCENTAJE')
									{
										$tipoVariable = true;
									}
									else
									{
										$tipoVariable = false;
									}
								?>
								<label class="btn btn-primary {{ $tipoVariable ? 'active' : ''}}">
									{!! Form::radio('variable', 'PORCENTAJE', $tipoVariable ? true : false) !!}%
								</label>
								<label class="btn btn-primary {{ !$tipoVariable ? 'active' : ''}}">
									{!! Form::radio('variable', 'VALOR', !$tipoVariable ? true : false) !!}$
								</label>
							</div>
							@if ($errors->has('variable'))
								<span class="help-block">{{ $errors->first('variable') }}</span>
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
							{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
							@if ($errors->has('descripcion'))
								<span class="help-block">{{ $errors->first('descripcion') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('tipoIndicador') }}" class="btn btn-danger pull-right">Cancelar</a>
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
@endpush

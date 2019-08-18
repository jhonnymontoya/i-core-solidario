@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Parametros institucionales
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Parametros institucionales</li>
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
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($parametro, ['url' => ['parametrosInstitucionales', $parametro], 'method' => 'PUT', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Editar parametro institucional</h3>

				<div class="card-tools pull-right">
					<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('modulo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('modulo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Código
							</label>
							{!! Form::text('modulo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'readonly']) !!}
							@if ($errors->has('modulo'))
								<span class="help-block">{{ $errors->first('modulo') }}</span>
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
							{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'readonly']) !!}
							@if ($errors->has('descripcion'))
								<span class="help-block">{{ $errors->first('descripcion') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					@if($parametro->tipo_parametro == 'VALOR' || $parametro->tipo_parametro == 'VALOR_INDICADOR')
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								valor
							</label>
							{!! Form::text('valor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor']) !!}
							@if ($errors->has('valor'))
								<span class="help-block">{{ $errors->first('valor') }}</span>
							@endif
						</div>
					</div>
					@endif
					@if($parametro->tipo_parametro == 'INDICADOR' || $parametro->tipo_parametro == 'VALOR_INDICADOR')
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('indicador')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('indicador'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Indicador
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<?php
									$indicador = $parametro->indicador;
									if(old('indicador') == '0')
									{
										$indicador = false;
									}
									elseif(old('indicador') == '1')
									{
										$indicador = true;
									}
								?>
								<label class="btn btn-primary {{ $indicador ? 'active' : '' }}">
									{!! Form::radio('indicador', '1', $indicador ? true : false) !!}Sí
								</label>
								<label class="btn btn-primary {{ !$indicador ? 'active' : '' }}">
									{!! Form::radio('indicador', '0', !$indicador ? true : false) !!}No
								</label>
							</div>
							@if ($errors->has('indicador'))
								<span class="help-block">{{ $errors->first('indicador') }}</span>
							@endif
						</div>
					</div>
					@endif
				</div>
			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('parametrosInstitucionales') }}" class="btn btn-danger pull-right">Cancelar</a>
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

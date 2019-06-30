@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Indicadores
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Indicadores</li>
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
		<div class="box box-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($indicador, ['url' => ['indicador', $indicador], 'method' => 'put', 'role' => 'form']) !!}
			<div class="box-header with-border">
				<h3 class="box-title">Actualizar indicador</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<dl class="dl-horizontal">
							<dt>Código</dt>
							<dd>{{ $indicador->tipoIndicador->codigo }}</dd>
						</dl>
					</div>
					<div class="col-md-4">
						<dl class="dl-horizontal">
							<dt>Periodicidad</dt>
							<dd>{{ $indicador->tipoIndicador->periodicidad }}</dd>
						</dl>
					</div>
					<div class="col-md-4">
						<dl class="dl-horizontal">
							<?php
								$variable = "";
								switch ($indicador->tipoIndicador->variable) {
									case 'PORCENTAJE':
										$variable = "%";
										break;
									case 'VALOR':
										$variable = "$";
										break;										
									default:
										$variable = "%";
										break;
								}
							?>
							<dt>Variable</dt>
							<dd>{{ $variable }}</dd>
						</dl>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<p><em>{{ $indicador->tipoIndicador->descripcion }}</em></p>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('fecha_inicio')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('fecha_inicio'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha de inicio
							</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								{!! Form::text('fecha_inicio', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'readonly']) !!}
							</div>
							@if ($errors->has('fecha_inicio'))
								<span class="help-block">{{ $errors->first('fecha_inicio') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('fecha_fin')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('fecha_fin'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha de inicio
							</label>
							<div class="input-group">
								<div class="input-group-addon">
									<i class="fa fa-calendar"></i>
								</div>
								{!! Form::text('fecha_fin', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'readonly']) !!}
							</div>
							@if ($errors->has('fecha_fin'))
								<span class="help-block">{{ $errors->first('fecha_fin') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('valor'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Valor
							</label>
							<div class="input-group">
								<div class="input-group-addon">{{ $variable }}</div>
								{!! Form::text('valor', null, ['class' => 'form-control pull-right', 'placeholder' => $variable, 'autocomplete' => 'off', 'autofocus']) !!}
							</div>
							@if ($errors->has('valor'))
								<span class="help-block">{{ $errors->first('valor') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				{!! Form::submit('Actualizar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('indicador?indicador=' . $indicador->tipoIndicador->id) }}" class="btn btn-danger pull-right">Cancelar</a>
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

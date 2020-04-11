@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Indicadores
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Indicadores</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($indicador, ['url' => ['indicador', $indicador], 'method' => 'put', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Actualizar indicador</h3>
				</div>
				<div class="card-body">
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
							<div class="form-group">
							    @php
							        $valid = $errors->has('fecha_inicio') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Fecha de inicio</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">
							                <i class="fa fa-calendar"></i>
							            </span>
							        </div>
							        {!! Form::text('fecha_inicio', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
							        @if ($errors->has('fecha_inicio'))
							            <div class="invalid-feedback">{{ $errors->first('fecha_inicio') }}</div>
							        @endif
							    </div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
							    @php
							        $valid = $errors->has('fecha_fin') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Fecha fin</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">
							                <i class="fa fa-calendar"></i>
							            </span>
							        </div>
							        {!! Form::text('fecha_fin', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
							        @if ($errors->has('fecha_fin'))
							            <div class="invalid-feedback">{{ $errors->first('fecha_fin') }}</div>
							        @endif
							    </div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
							    @php
							        $valid = $errors->has('valor') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Valor</label>
							    <div class="input-group">
							        <div class="input-group-prepend">
							            <span class="input-group-text">{{ $variable }}</span>
							        </div>
							        {!! Form::text('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => $variable, 'autofocus']) !!}
							        @if ($errors->has('valor'))
							            <div class="invalid-feedback">{{ $errors->first('valor') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Actualizar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('indicador?indicador=' . $indicador->tipoIndicador->id) }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
@endpush

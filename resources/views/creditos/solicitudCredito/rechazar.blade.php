@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito</li>
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
		{!! Form::model($solicitud, ['url' => ['solicitudCredito', $solicitud, 'rechazar'], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-warning card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Rechazar solicitud de crédito</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="alert alert-danger">
								<h4>
									<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
								</h4>
								Esta a punto de rechazar la solicitud de crédito, esta acción no puede ser reversada....
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<dl class="dl-horizontal">
								<dt>Para:</dt>
								<dd>{{ $solicitud->tercero->tipoIdentificacion->codigo }} {{ $solicitud->tercero->numero_identificacion }} - {{ $solicitud->tercero->nombre_corto }}</dd>
								<dt>Fecha de solicitud:</dt>
								<dd>{{ $solicitud->fecha_solicitud }} ({{ $solicitud->fecha_solicitud->diffForHumans() }})</dd>
								<dt>Modalidad de crédito:</dt>
								<dd>{{ $solicitud->modalidadCredito->codigo }} - {{ $solicitud->modalidadCredito->nombre }}</dd>
								<dt>Valor:</dt>
								<dd>${{ number_format($solicitud->valor_credito) }}</dd>
								<dt>Cuotas:</dt>
								<dd>{{ $solicitud->plazo }} ({{ $solicitud->periodicidad }})</dd>
							</dl>
						</div>
					</div>

					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="form-group {{ ($errors->has('observaciones')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('observaciones'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Observaciones
								</label>
								{!! Form::textarea('observaciones', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('observaciones'))
									<span class="help-block">{{ $errors->first('observaciones') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::submit('Rechazar', ['class' => 'btn btn-outline-danger', 'tabindex' => '2']) !!}
					<a href="{{ url('solicitudCredito') }}" class="btn btn-outline-success pull-right" tabindex="1">Cancelar</a>
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

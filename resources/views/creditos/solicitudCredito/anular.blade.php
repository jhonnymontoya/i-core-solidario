@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Solicitudes de crédito
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Solicitudes de crédito</li>
		</ol>
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
		{!! Form::model($solicitud, ['url' => ['solicitudCredito', $solicitud, 'anular'], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h3 class="box-title">Anular solicitud de crédito</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-danger">
									<h4>
										<i class="fa fa-warning"></i>&nbsp;Alerta!
									</h4>
									Esta a punto de anular la solicitud de crédito, esta acción no puede ser reversada....
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
					</div>
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						{!! Form::submit('Anular', ['class' => 'btn btn-danger', 'tabindex' => '2']) !!}
						<a href="{{ url('solicitudCredito') }}" class="btn btn-success pull-right" tabindex="1">Cancelar</a>
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

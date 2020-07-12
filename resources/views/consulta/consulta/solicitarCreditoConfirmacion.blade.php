@extends('layouts.consulta')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Enviar solicitud de crédito</h3>
				</div>
				<div class="card-body">
					<h3>Se ha registrado la solicitud de crédito con los siguientes datos:</h3>
					<br>
					<div class="row">
						<div class="col">
							<strong>Solicitante:</strong>
							<span>{{ $socio->tercero->nombre_completo }}</span>
						</div>
					</div>
					<br>
					<dl class="row">
						<dt class="col-md-2">Número solicitud</dt>
						<dd class="col">{{ $solicitudCredito->id }}</dd>

						<dt class="col-md-2">Modalidad</dt>
						<dd class="col">{{ $solicitudCredito->modalidadCredito->nombre }}</dd>
					</dl>

					<dl class="row">
						<dt class="col-md-2">Fecha</dt>
						<dd class="col">{{ $solicitudCredito->fecha_solicitud }}</dd>

						<dt class="col-md-2">Empresa</dt>
						<dd class="col">{{ $socio->pagaduria->terceroEmpresa->nombre }}</dd>
					</dl>

					<dl class="row">
						<dt class="col-md-2">Valor</dt>
						<dd class="col">${{ number_format($solicitudCredito->valor_solicitud, 0) }}</dd>

						<dt class="col-md-2">Tasa M.V.</dt>
						<dd class="col">{{ number_format($solicitudCredito->tasa, 3) }}%</dd>
					</dl>

					<dl class="row">
						<dt class="col-md-2">Plazo (cuotas)</dt>
						<dd class="col">{{ $solicitudCredito->plazo }}</dd>

						<dt class="col-md-2">Periodicidad</dt>
						<dd class="col">{{ $solicitudCredito->periodicidad }}</dd>
					</dl>

					<div class="row">
						<div class="col">
							<a class="btn btn-outline-success float-right" href="{{ url('consulta/solicitarCredito') }}">Listo</a>
						</div>
					</div>
				</div>
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

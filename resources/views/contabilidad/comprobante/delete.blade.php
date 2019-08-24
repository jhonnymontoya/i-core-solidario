@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
		{!! Form::model($comprobante, ['url' => ['comprobante', $comprobante, 'delete'], 'method' => 'delete', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-danger card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Eliminar comprobante</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="alert alert-danger">
								<h4>
									<i class="fa fa-ban"></i>&nbsp;Alerta!
								</h4>
								Esta a punto de eliminar el comprobante sin contabilizar, esta acción no puede ser reversada...
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<dl class="dl-horizontal">
								<dt>Tipo comprobante:</dt>
								<dd>{{ $comprobante->tipoComprobante->nombre_completo }}</dd>
								<dt>Fecha del comprobante:</dt>
								<dd>{{ $comprobante->fecha_movimiento }} ({{ $comprobante->fecha_movimiento->diffForHumans() }})</dd>
								<dt>Descripción:</dt>
								<dd>{{ $comprobante->descripcion }}</dd>
							</dl>
							<dl class="dl-horizontal">
								<dt>Registros:</dt>
								<dd>{{ $comprobante->detalleMovimientos->count() }}</dd>
							</dl>
							<div class="row">
								<div class="col-md-4">
									<dl class="dl-horizontal">
										<dt>Total débitos:</dt>
										<dd>${{ number_format($comprobante->debitos, 0) }}</dd>
										<dt>Total créditos:</dt>
										<dd>${{ number_format($comprobante->creditos, 0) }}</dd>
										<dt>Total diferencia:</dt>
										<dd>${{ number_format($comprobante->debitos - $comprobante->creditos, 0) }}</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::submit('Eliminar', ['class' => 'btn btn-outline-danger', 'tabindex' => '2']) !!}
					<a href="{{ url('comprobante') }}" class="btn btn-outline-success pull-right" tabindex="1">Cancelar</a>
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

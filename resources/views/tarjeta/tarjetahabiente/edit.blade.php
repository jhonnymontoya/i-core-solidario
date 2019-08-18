@extends('layouts.admin')

@section('content')

{{-- Modal de actualizar cupo --}}
@component('tarjeta.tarjetahabiente.modales.actualizarCupo')
@endcomponent

{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tarjetahabiente
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Tarjetahabiente</li>
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
				<ul>
					@foreach ($errors->all() as $error)
						<li>{{ $error }}</li>
					@endforeach
				</ul>
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('error') }}
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar tarjeta de {{ $tercero->nombre_completo }}</h3>
					</div>
					<div class="card-body">
						@php
							$label = "default";
							switch ($tarjetahabiente->estado) {
								case 'ASIGNADA':
									$label = "info";
									break;
								case 'ACTIVA':
									$label = "success";
									break;
								case 'INACTIVA':
									$label = "warning";
									break;
								case 'BLOQUEADA':
								case 'CANCELADA':
									$label = "danger";
									break;
							}
						@endphp
						<h3>Tarjeta</h3>
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<dl class="dl-horizontal">
									<dt>Número tarjeta</dt>
									<dd>{{ $tarjetahabiente->tarjeta->numeroFormateado }}</dd>
									<dt>Fecha asignación</dt>
									<dd>{{ $tarjetahabiente->fecha_asignacion }}</dd>
									<dt>Estado tarjeta</dt>
									<dd>
										<span class="label label-{{ $label }}">
											{{ $tarjetahabiente->estado }}
										</span>
									</dd>
								</dl>
							</div>
							<div class="col-md-6 col-sm-12">
								<dl class="dl-horizontal">
									<dt>Producto</dt>
									<dd>{{ $tarjetahabiente->producto->nombre }}</dd>
									<dt>Tipo producto</dt>
									<dd>{{ $tarjetahabiente->producto->modalidad }}</dd>
									<dt>Cuota manejo</dt>
									<dd>${{ number_format($tarjetahabiente->producto->valor_cuota_manejo_mes) }}</dd>
								</dl>
							</div>
						</div>
						@if ($tarjetahabiente->producto->credito)
							@php
								$cupoUtilizado = $tarjetahabiente->solicitudCredito->saldoObligacion("31/12/3000");
								$cupoDisponible = $tarjetahabiente->cupo - $cupoUtilizado;
							@endphp
							<h3>Cupo de crédito</h3>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Cupo total</dt>
										<dd>${{ number_format($tarjetahabiente->cupo) }}</dd>
										<dt>Cupo utilizado</dt>
										<dd>${{ number_format($cupoUtilizado) }}</dd>
									</dl>
								</div>
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Cuenta corriente</dt>
										<dd>{{ $tarjetahabiente->numero_cuenta_corriente }}</dd>
										<dt>Cupo disponible</dt>
										<dd>${{ number_format($cupoDisponible) }}</dd>
									</dl>
								</div>
							</div>
						@endif
						@if ($tarjetahabiente->producto->ahorro)
							@php
								$label = "default";
								switch ($tarjetahabiente->cuentaAhorro->estado) {
									case 'APERTURA':
										$label = "info";
										break;
									case 'ACTIVA':
										$label = "success";
										break;
									case 'INACTIVA':
										$label = "warning";
										break;
									case 'CERRADA':
										$label = "danger";
										break;
								}
							@endphp
							<h3>Cuenta ahorros</h3>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Número cuenta</dt>
										<dd>{{ $tarjetahabiente->cuentaAhorro->numero_cuenta }}</dd>
										<dt>Estado cuenta</dt>
										<dd>
											<span class="label label-{{ $label }}">
												{{ $tarjetahabiente->cuentaAhorro->estado }}
											</span>
										</dd>
									</dl>
								</div>
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Tipo cuenta ahorros</dt>
										<dd>{{ $tarjetahabiente->cuentaAhorro->tipoCuentaAhorro->nombre_producto }}</dd>
										<dt>Saldo flexible</dt>
										<dd>${{ number_format($tarjetahabiente->cuentaAhorro->cupo_flexible) }}</dd>
									</dl>
								</div>
							</div>
						@endif
						@if ($tarjetahabiente->producto->vista)
							<h3>Ahorros vista</h3>
							<div class="row">
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Número cuenta</dt>
										<dd>{{ $tarjetahabiente->numero_cuenta_vista }}</dd>
									</dl>
								</div>
								<div class="col-md-6 col-sm-12">
									<dl class="dl-horizontal">
										<dt>Saldo disponible</dt>
										<dd>${{ number_format($tarjetahabiente->tercero->cupoDisponibleVista('31/12/3000')) }}</dd>
									</dl>
								</div>
							</div>
						@endif
						<br>
						<div class="row">
							@if ($tarjetahabiente->producto->credito)
								<div class="col-md-2 col-sm-12">
									<a class="btn btn-default" data-toggle="modal" data-target="#mActualizarCupo" data-cupo="{{ round($tarjetahabiente->cupo, 0) }}" data-tarjeta="{{ $tarjetahabiente->id }}">Actualizar cupo</a>
								</div>
							@endif
							<div class="col-md-2 col-sm-12">
								<a class="btn btn-default disabled" id="btnActualizarEstado">Actualizar estado</a>
							</div>
							<div class="col-md-2 col-sm-12">
								<a class="btn btn-default disabled" id="btnConsultaSaldosRed">Consulta saldos red</a>
							</div>
							<div class="col-md-2 col-sm-12">
								<a class="btn btn-default disabled" id="btnConsultaTarjetaRed">Consulta tarjeta red</a>
							</div>
							<div class="col-md-2 col-sm-12">
								<a class="btn btn-default disabled" id="btnRemplazarTarjeta">Remplazar tarjeta</a>
							</div>
							<div class="col-md-2 col-sm-12">
								<a class="btn btn-default disabled" id="btnCancelarTarjeta">Cancelar tarjeta</a>
							</div>
						</div>
					</div>
					<div class="card-footer">
						<a href="{{ route('tarjetaHabiente.show', $tercero->id) }}" class="btn btn-danger pull-right">Volver</a>
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

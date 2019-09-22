@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						SDAT
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">SDAT</li>
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
		@if (Session::has("error"))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get("error") }}</p>
			</div>
			@php
				Session::forget("error");
			@endphp
		@endif
		{!! Form::open(['route' => ['SDAT.put.saldar', $sdat->id], 'method' => 'put', 'role' => 'form', 'id' => 'frmSaldar']) !!}
		{!! Form::hidden('cuenta', $cuenta->id) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Saldar SDAT</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<h4>Datos depósito</h4>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Tipo:</dt>
								<dd>{{ $sdat->tipoSdat->codigo }}</dd>

								<dt>Valor contituido:</dt>
								<dd>${{ number_format($sdat->valor) }}</dd>

								<dt>Fecha constitución:</dt>
								<dd>{{ $sdat->fecha_constitucion }} ({{ $sdat->fecha_constitucion->diffForHumans() }})</dd>

								<dt>Fecha vencimiento:</dt>
								<dd>{{ $sdat->fecha_vencimiento }} ({{ $sdat->fecha_vencimiento->diffForHumans() }})</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>No. deposito:</dt>
								<dd>{{ $sdat->id }}</dd>

								<dt>Tasa E.A.:</dt>
								<dd>{{ number_format($sdat->tasa, 2) }}%</dd>

								<dt>Plazo (días):</dt>
								<dd>{{ number_format($sdat->plazo) }}</dd>

								@php
									$tercero = $sdat->socio->tercero;
									$nombre = sprintf(
										"%s %s - %s",
										$tercero->tipoIdentificacion->codigo,
										$tercero->numero_identificacion,
										$tercero->nombre_corto
									);
								@endphp

								<dt>Nombre:</dt>
								<dd>{{ $nombre }}</dd>
							</dl>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('fechaDevolucion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha devolución</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fa fa-calendar"></i></span>
									</div>
									{!! Form::text('fechaDevolucion', $fechaDevolucion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Fecha devolución', 'readOnly']) !!}
									@if ($errors->has('fechaDevolucion'))
										<div class="invalid-feedback">{{ $errors->first('fechaDevolucion') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('cuentaNombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text"><i class="fa fa-table"></i></span>
									</div>
									{!! Form::text('cuentaNombre', $cuenta->codigo . " - " . $cuenta->nombre, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cuenta', 'readonly']) !!}
									@if ($errors->has('cuentaNombre'))
										<div class="invalid-feedback">{{ $errors->first('cuentaNombre') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<h4>Valores liquidación</h4>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Saldo depósito:</dt>
								<dd>${{ number_format($datosDevolucion->saldo, 0) }}</dd>

								<dt>Total retefuente:</dt>
								<dd>${{ number_format($datosDevolucion->retefuente_total, 0) }}</dd>

								<dt>Retefuente pendiente:</dt>
								<dd>${{ number_format($datosDevolucion->retefuente_pendiente, 0) }}</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Total intereses:</dt>
								<dd>${{ number_format($datosDevolucion->interes_total, 0) }}</dd>

								<dt>Intereses pendientes:</dt>
								<dd>${{ number_format($datosDevolucion->interes_pendiente, 0) }}</dd>
							</dl>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<h4>Devolución depósito</h4>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<dl>
								<dt class="text-primary">Saldo depósito:</dt>
								<dd class="text-primary">${{ number_format($datosDevolucion->saldo, 0) }}</dd>

								<dt class="text-primary">Intereses causados:</dt>
								<dd class="text-primary">${{ number_format($datosDevolucion->interes_causado, 0) }}</dd>

								<dt class="text-primary">Intereses pendientes:</dt>
								<dd class="text-primary">${{ number_format($datosDevolucion->interes_pendiente, 0) }}</dd>

								<dt class="text-danger">Retefuente pendiente:</dt>
								<dd class="text-danger">${{ number_format($datosDevolucion->retefuente_pendiente, 0) }}</dd>

								<dt class="text-success">Total a reintegrar:</dt>
								<dd class="text-success"><strong>${{ number_format($datosDevolucion->total_devolucion, 0) }}</strong></dd>
							</dl>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Saldar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('SDAT') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
<script type="text/javascript">
	$(function(){
		$("#frmSaldar").submit(function(){
			$submit = $("input[type='submit']");
			$submit.attr("disabled", true);
			$submit.val("Saldando...");
		});
	});
</script>
@endpush

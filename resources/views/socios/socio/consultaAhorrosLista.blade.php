@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Socios
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Socios</li>
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
		<div class="row">
			<div class="col-md-12 text-right">
				<a href="{{ url('socio/consulta') }}?socio={{ $socio->id }}&fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>
		<?php
			$totalCuotaMes = 0;
			$totalIntereses = 0;
			$totalSaldo = 0;
			foreach ($ahorros as $ahorro) {
				$totalCuotaMes += $ahorro->cuotaMes;
				$totalIntereses += $ahorro->intereses;
				$totalSaldo += $ahorro->saldo;
			}
			foreach($sdats as $sdat) {
				$totalSaldo += $sdat->saldo_valor;
			}
		?>
		<div class="container-fluid">
			<h5>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</h5>

			<dl class="row">
				<dt class="col-md-2 col-sm-6">Total cuota mes</dt>
				<dd class="col-md-2 col-sm-6">${{ number_format($totalCuotaMes) }}</dd>

				<dt class="col-md-2 col-sm-6">Total intereses</dt>
				<dd class="col-md-2 col-sm-6">${{ number_format($totalIntereses) }}</dd>

				<dt class="col-md-2 col-sm-6">Total saldo</dt>
				<dd class="col-md-2 col-sm-6">${{ number_format($totalSaldo) }}</dd>
			</dl>
		</div>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Ahorros generales</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($ahorros->where('tipo_ahorro', '<>', 'PROGRAMADO')->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Modalidad</th>
											<th class="text-center">Cuota</th>
											<th>Periodicidad</th>
											<th class="text-center">Cuota mes</th>
											<th class="text-center">Saldo</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Tasa E.A.</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($ahorros->where('tipo_ahorro', '<>', 'PROGRAMADO') as $ahorro) {
												if($ahorro->cuota == 0 && $ahorro->saldo == 0) {
													continue;
												}
												?>
												<tr>
													<td>
														<a href="{{ route('socio.consulta.ahorros', $ahorro->modalidad_ahorro_id) }}?fecha={{ $fechaConsulta }}&socio={{ $socio->id }}">
															{{ $ahorro->codigo }} - {{ $ahorro->nombre }}
														</a>
													</td>
													<td class="text-right">${{ number_format($ahorro->cuota) }}</td>
													<td>{{ $ahorro->periodicidad }}</td>
													<td class="text-right">${{ number_format($ahorro->cuotaMes) }}</td>
													<td class="text-right">${{ number_format($ahorro->saldo) }}</td>
													<td class="text-right">${{ number_format($ahorro->intereses) }}</td>
													<td class="text-right">{{ number_format($ahorro->tasa, 2) }}%</td>
												</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							@else
								<strong>No existen registros para mostrar</strong>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Ahorros programados</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($ahorros->where('tipo_ahorro', 'PROGRAMADO')->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Modalidad</th>
											<th class="text-center">Cuota</th>
											<th>Periodicidad</th>
											<th class="text-center">Cuota mes</th>
											<th class="text-center">Saldo</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Vencimiento</th>
											<th class="text-center">Tasa E.A.</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($ahorros->where('tipo_ahorro', 'PROGRAMADO') as $ahorro) {
												if($ahorro->cuota == 0 && $ahorro->saldo == 0) {
													continue;
												}
												?>
												<tr>
													<td>
														<a href="{{ route('socio.consulta.ahorros', $ahorro->modalidad_ahorro_id) }}?fecha={{ $fechaConsulta }}&socio={{ $socio->id }}">
															{{ $ahorro->codigo }} - {{ $ahorro->nombre }}
														</a>
													</td>
													<td class="text-right">${{ number_format($ahorro->cuota) }}</td>
													<td>{{ $ahorro->periodicidad }}</td>
													<td class="text-right">${{ number_format($ahorro->cuotaMes) }}</td>
													<td class="text-right">${{ number_format($ahorro->saldo) }}</td>
													<td class="text-right">${{ number_format($ahorro->intereses) }}</td>
													<td>{{ $ahorro->vencimiento }}</td>
													<td class="text-right">{{ number_format($ahorro->tasa, 2) }}%</td>
												</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							@else
								<strong>No existen registros para mostrar</strong>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">SDAT</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($sdats->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Número</th>
											<th>Tipo</th>
											<th class="text-center">Valor</th>
											<th>Constitución</th>
											<th class="text-center">Plazo días</th>
											<th>Vencimiento</th>
											<th class="text-center">Tasa E.A.</th>
											<th class="text-center">Saldo</th>
											<th class="text-center">Intereses reconocidos</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($sdats as $sdat) {
												$label = "default";
												switch ($sdat->estado) {
													case 'CONSTITUIDO':
													case 'RENOVADO':
													case 'PRORROGADO':
														$label = "success";
														break;
													default:
														$label = "default";
														continue 2;
														break;
												}
												?>
												<tr>
													<td>{{ $sdat->id }}</td>
													<td>{{ $sdat->codigo }}</td>
													<td class="text-right">{{ $sdat->valor }}</td>
													<td>{{ $sdat->fecha_constitucion }}</td>
													<td class="text-right">{{ $sdat->plazo }}</td>
													<td>{{ $sdat->fecha_vencimiento }}</td>
													<td class="text-right">{{ $sdat->tasa }}</td>
													<td class="text-right">{{ $sdat->saldo }}</td>
													<td class="text-right">{{ $sdat->rendimientos }}</td>
													<td><span class="badge badge-{{ $label }}">{{ $sdat->estado }}</span></td>
												</tr>
												<?php
											}
										?>
									</tbody>
								</table>
							@else
								<strong>No existen registros para mostrar</strong>
							@endif
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-right">
				<a href="{{ url('socio/consulta') }}?socio={{ $socio->id }}&fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>
		<br>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
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
					<h3 class="card-title">Créditos activos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($creditos->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Obligación</th>
											<th>Modalidad</th>
											<th>Desembolso</th>
											<th class="text-center">Valor inicial</th>
											<th class="text-center">Tasa M.V.</th>
											<th class="text-center">Cuota</th>
											<th class="text-center">Saldo capital</th>
											<th class="text-center">Saldo intereses</th>
											<th class="text-center">Saldo total</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$totalCuota = 0;
											$totalSaldoCapital = 0;
											$totalSaldoIntereses = 0;
											foreach($creditos as $credito) {
												$totalCuota += $credito->valor_cuota;
												$totalSaldoCapital += $credito->saldoCapital;
												$totalSaldoIntereses += $credito->saldoIntereses;
												?>
												<tr>
													<td>
														<a href="{{ route('consulta.creditos', $credito->id) }}">
															{{ $credito->numero_obligacion }}
														</a>
													</td>
													<td>{{ $credito->modalidadCredito->nombre }}</td>
													<td>{{ $credito->fecha_desembolso }}</td>
													<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
													<td class="text-right">{{ number_format($credito->tasa, 3) }}%</td>
													<td class="text-right">${{ number_format($credito->valor_cuota, 0) }}</td>
													<td class="text-right">${{ number_format($credito->saldoCapital, 0) }}</td>
													<td class="text-right">${{ number_format($credito->saldoIntereses, 0) }}</td>
													<td class="text-right">${{ number_format($credito->saldoCapital + $credito->saldoIntereses, 0) }}</td>
												</tr>
												<?php
											}
										?>
									</tbody>
									<tfoot>
										<tr>
											<th class="text-right" colspan="5">Totales</th>
											<th class="text-right">${{ number_format($totalCuota) }}</th>
											<th class="text-right">${{ number_format($totalSaldoCapital) }}</th>
											<th class="text-right">${{ number_format($totalSaldoIntereses) }}</th>
											<th class="text-right">${{ number_format($totalSaldoCapital + $totalSaldoIntereses) }}</th>
										</tr>
									</tfoot>
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
					<h3 class="card-title">Codeudas</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($codeudas->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Deudor</th>
											<th>Número obligación</th>
											<th>Desembolso</th>
											<th class="text-center">Valor inicial</th>
											<th class="text-center">Tasa M.V.</th>
											<th class="text-center">Saldo capital</th>
											<th class="text-center">Calificación</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($codeudas as $codeuda) {
												?>
												<tr>
													<td>{{ $codeuda->deudor }}</td>
													<td>{{ $codeuda->numeroObligacion }}</td>
													<td>{{ $codeuda->fechaInicio }}</td>
													<td class="text-right">${{ number_format($codeuda->valorInicial) }}</td>
													<td class="text-right">{{ number_format($codeuda->tasaMV, 3) }}%</td>
													<td class="text-right">${{ number_format($codeuda->saldoCapital) }}</td>
													<td class="text-center">{{ $codeuda->calificacion }}</td>
												</tr>
												<?php
											}
										?>
									</tbody>
									<tfoot>
										<tr>
											<th class="text-right" colspan="5">Total codeudas</th>
											<th class="text-right">${{ number_format($codeudas->sum('saldoCapital')) }}</th>
											<th></th>
										</tr>
									</tfoot>
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
					<h3 class="card-title">Créditos saldados</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($saldados->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Obligación</th>
											<th>Modalidad</th>
											<th>Desembolso</th>
											<th class="text-center">Valor inicial</th>
											<th class="text-center">Tasa M.V.</th>
											<th class="text-center">Valor cuota</th>
											<th>Estado</th>
											<th>Fecha cancelación</th>
										</tr>
									</thead>
									<tbody>
										<?php
											foreach($saldados as $saldado) {
												?>
												<tr>
													<td>
														<a href="{{ route('consulta.creditos', $saldado->id) }}">
															{{ $saldado->numero_obligacion }}
														</a>
													</td>
													<td>{{ $saldado->modalidadCredito->nombre }}</td>
													<td>{{ $saldado->fecha_desembolso }}</td>
													<td class="text-right">${{ number_format($saldado->valor_saldado, 0) }}</td>
													<td class="text-right">{{ number_format($saldado->tasa, 3) }}%</td>
													<td class="text-right">${{ number_format($saldado->valor_cuota, 0) }}</td>
													<td><span class="badge badge-pill badge-secondary">{{ $saldado->estado_solicitud }}</span></td>
													<td>{{ $saldado->fecha_cancelacion }}</td>
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
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
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
					<h3 class="card-title">Recaudos nómina</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($recaudos->count())
								<table class="table table-striped table-hover recaudos">
									<thead>
										<tr>
											<th>Concepto</th>
											<th>Código</th>
											<th class="text-center">Total generado</th>
											<th class="text-center">Total aplicado</th>
											<th class="text-center">Total ajustado</th>
										</tr>
									</thead>
									<tbody>
										<?php
											$totalGenerado = 0;
											$totalAplicado = 0;
											$totalAjustado = 0;
											$numeroPeriodo = '<a href="' . route('consulta.recaudos', $recaudos[0]->controlProceso->id) . "\">" . $recaudos[0]->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudos[0]->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>';
											$titulo = true;
											foreach($recaudos as $recaudo) {
												if($titulo) {
													?>
													<tr style="background-color: #ddd">
														<td colspan="5">{!! $numeroPeriodo !!}</td>
													</tr>
													<?php
													$titulo = false;
												}
												if($numeroPeriodo != '<a href="' . route('consulta.recaudos', $recaudo->controlProceso->id) . "\">" . $recaudo->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudo->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>') {
													?>
													<tr>
														<th>Totales</th>
														<td></td>
														<th class="text-right">${{ number_format($totalGenerado, 0) }}</th>
														<th class="text-right">${{ number_format($totalAplicado, 0) }}</th>
														<th class="text-right">${{ number_format($totalAjustado, 0) }}</th>
													</tr>
													<?php
													$numeroPeriodo = '<a href="' . route('consulta.recaudos', $recaudo->controlProceso->id) . "\">" . $recaudo->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudo->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>';
													?>
													<tr style="background-color: #ddd">
														<td colspan="5">{!! $numeroPeriodo !!}</td>
													</tr>
													<?php
													$totalGenerado = 0;
													$totalAplicado = 0;
													$totalAjustado = 0;
												}
												$totalGenerado += floatval($recaudo->total_generado);
												$totalAplicado += floatval($recaudo->total_aplicado);
												$totalAjustado += floatval($recaudo->total_ajustado);
												?>
												<tr>
													<td>{{ $recaudo->conceptoRecaudo->nombre }}</td>
													<td>{{ $recaudo->conceptoRecaudo->codigo }}</td>
													<td class="text-right">${{ number_format($recaudo->total_generado, 0) }}</td>
													<td class="text-right">${{ number_format($recaudo->total_aplicado, 0) }}</td>
													<td class="text-right">${{ number_format($recaudo->total_ajustado, 0) }}</td>
												</tr>
												<?php
											}
										?>
										<tr>
											<th>Totales</th>
											<td></td>
											<th class="text-right">${{ number_format($totalGenerado, 0) }}</th>
											<th class="text-right">${{ number_format($totalAplicado, 0) }}</th>
											<th class="text-right">${{ number_format($totalAjustado, 0) }}</th>
										</tr>
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
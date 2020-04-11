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
				<a href="{{ route('socio.consulta.recaudos.lista', $socio->id) }}?fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Detalle recaudos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-5">
									<strong>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</strong>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-4">
									<dl>
										<dt>Periodo:</dt>
										<dd>{{ $periodo }}</dd>
									</dl>
								</div>
								<div class="col-md-4">
									<dl>
										<dt>Periodicidad:</dt>
										<dd>{{ $periodicidad }}</dd>
									</dl>
								</div>
								<div class="col-md-4">
									<dl>
										<dt>Pagaduria:</dt>
										<dd>{{ $proceso->pagaduria->nombre }}</dd>
									</dl>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<dl>
										<dt>Número proceso:</dt>
										<dd>{{ $proceso->id }}</dd>
									</dl>
								</div>

								<div class="col-md-4">
									<dl>
										<dt>Estado proceso:</dt>
										<dd>{{ $proceso->estado }}</dd>
									</dl>
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($recaudos->count())
								<table class="table table-hover table-striped">
									<thead>
										<tr>
											<th></th>
											<th colspan="4" class="text-center evenh">Generado</th>
											<th colspan="4" class="text-center oddh">Aplicado</th>
											<th colspan="4" class="text-center evenh">Ajustado</th>
										</tr>
										<tr>
											<th class="oddh">Concepto</th>
											<th class="text-center evenh">Capital</th>
											<th class="text-center evenh">Interes</th>
											<th class="text-center evenh">Seguro</th>
											<th class="text-center evenh">Total</th>
											<th class="text-center oddh">Capital</th>
											<th class="text-center oddh">Interes</th>
											<th class="text-center oddh">Seguro</th>
											<th class="text-center oddh">Total</th>
											<th class="text-center evenh">Capital</th>
											<th class="text-center evenh">Interes</th>
											<th class="text-center evenh">Seguro</th>
											<th class="text-center evenh">Total</th>
										</tr>
									</thead>
									<tbody>
										@php
											$totales = (object) [
												"gc" => 0, "gi" => 0, "gs" => 0, "gt" => 0,
												"ac" => 0, "ai" => 0, "as" => 0, "at" => 0,
												"ajc" => 0, "aji" => 0, "ajs" => 0, "ajt" => 0,
											];
										@endphp
										@foreach($recaudos as $recaudo)
											<?php
												$concepto = '';
												if(!empty($recaudo->modalidadAhorro)) {
													$concepto = $recaudo->modalidadAhorro->codigo . " - " . $recaudo->modalidadAhorro->nombre;
												}
												elseif(!empty($recaudo->solcitudCredito)) {
													$concepto = $recaudo->solcitudCredito->numero_obligacion . " - " . $recaudo->solcitudCredito->modalidadCredito->nombre;
												}
												$totales->gc += $recaudo->capital_generado;
												$totales->gi += $recaudo->intereses_generado;
												$totales->gs += $recaudo->seguro_generado;
												$totales->gt += $recaudo->capital_generado + $recaudo->intereses_generado + $recaudo->seguro_generado;

												$totales->ac += $recaudo->capital_aplicado;
												$totales->ai += $recaudo->intereses_aplicado;
												$totales->as += $recaudo->seguro_aplicado;
												$totales->at += $recaudo->capital_aplicado + $recaudo->intereses_aplicado + $recaudo->seguro_aplicado;

												$totales->ajc += $recaudo->capital_ajustado;
												$totales->aji += $recaudo->intereses_ajustado;
												$totales->ajs += $recaudo->seguro_ajustado;
												$totales->ajt += $recaudo->capital_ajustado + $recaudo->intereses_ajustado + $recaudo->seguro_ajustado;
											?>
											<tr>
												<td class="odd">{{ $concepto }}</td>
												<td class="text-right even">${{ number_format($recaudo->capital_generado, 0) }}</td>
												<td class="text-right even">${{ number_format($recaudo->intereses_generado, 0) }}</td>
												<td class="text-right even">${{ number_format($recaudo->seguro_generado, 0) }}</td>
												<th class="text-right even">${{ number_format($recaudo->capital_generado + $recaudo->intereses_generado + $recaudo->seguro_generado, 0) }}</th>

												<td class="text-right odd">${{ number_format($recaudo->capital_aplicado, 0) }}</td>
												<td class="text-right odd">${{ number_format($recaudo->intereses_aplicado, 0) }}</td>
												<td class="text-right odd">${{ number_format($recaudo->seguro_aplicado, 0) }}</td>
												<th class="text-right odd">${{ number_format($recaudo->capital_aplicado + $recaudo->intereses_aplicado + $recaudo->seguro_aplicado, 0) }}</th>

												<td class="text-right even">${{ number_format($recaudo->capital_ajustado, 0) }}</td>
												<td class="text-right even">${{ number_format($recaudo->intereses_ajustado, 0) }}</td>
												<td class="text-right even">${{ number_format($recaudo->seguro_ajustado, 0) }}</td>
												<th class="text-right even">${{ number_format($recaudo->capital_ajustado + $recaudo->intereses_ajustado + $recaudo->seguro_ajustado, 0) }}</th>
											</tr>
										@endforeach
									</tbody>
									<tfoot>
										<tr>
											<th>Totales:</th>

											<th class="text-right">${{ number_format($totales->gc) }}</th>
											<th class="text-right">${{ number_format($totales->gi) }}</th>
											<th class="text-right">${{ number_format($totales->gs) }}</th>
											<th class="text-right">${{ number_format($totales->gt) }}</th>

											<th class="text-right">${{ number_format($totales->ac) }}</th>
											<th class="text-right">${{ number_format($totales->ai) }}</th>
											<th class="text-right">${{ number_format($totales->as) }}</th>
											<th class="text-right">${{ number_format($totales->at) }}</th>

											<th class="text-right">${{ number_format($totales->ajc) }}</th>
											<th class="text-right">${{ number_format($totales->aji) }}</th>
											<th class="text-right">${{ number_format($totales->ajs) }}</th>
											<th class="text-right">${{ number_format($totales->ajt) }}</th>
										</tr>
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
				<a href="{{ route('socio.consulta.recaudos.lista', $socio->id) }}?fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>
		<br>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.oddh{
		background-color: #BBEEEE;
	}
	.evenh{
		background-color: #EEBBEE;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
	});
</script>
@endpush
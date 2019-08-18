@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Ajustes créditos en lote
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Ajustes créditos en lote</li>
		</ol>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Procesar archivo ajuste créditos en lote</h3>
				<a href="{{ url('ajusteCreditoLote') }}" class="btn btn-danger btn-sm pull-right">Volver</a>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">
								Fecha del proceso
							</label>
							<br>
							{{ $proceso->fecha_proceso }}
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">
								Estado
							</label>
							<br>
							@php
								$label = "label-";
								switch($proceso->estado) {
									case 'PRECARGA':
										$label .= 'default';
										break;
									case 'CARGADO':
										$label .= 'info';
										break;
									case 'DESEMBOLSADO':
										$label .= 'success';
										break;
									case 'ANULADO':
										$label .= 'danger';
										break;
									default:
										$label .= 'default';
										break;
								}
							@endphp
							<span class="label {{ $label }}">{{ $proceso->estado }}</span>
						</div>
					</div>
					<div class="col-md-2">
						<div class="form-group">
							<label class="control-label">
								Cantidad de ajustes
							</label>
							<br>
							{{ $proceso->cantidad_ajustes_creditos }}
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">
								Valor
							</label>
							<br>
							${{ number_format($proceso->total_valor_ajuste, 0) }}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">
								Descripción
							</label>
							<br>
							{{ str_limit($proceso->descripcion, 50) }}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">
								Cuenta contra partida
							</label>
							<br>
							{{ $proceso->cuif->nombre }}
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">
								Tercero contra partida
							</label>
							<br>
							{{ $proceso->tercero->nombre_corto }}
						</div>
					</div>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label">
								Referencia
							</label>
							<br>
							{{ $proceso->referencia }}
						</div>
					</div>
				</div>
				<hr>
				<div class="row">
					<div class="col-md-11 col-md-offset-1">
						<a href="{{ route('ajusteCreditoLoteLimpiar', $proceso->id) }}" class="btn btn-warning">Limpiar carga</a>
						<a href="{{ route('ajusteCreditoLoteContabilizar', $proceso->id) }}" class="btn btn-success">Procesar</a>
						<a href="{{ route('ajusteCreditoLoteAnular', $proceso->id) }}" class="btn btn-danger pull-right">Anular</a>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12 table-responsive">
						<table class="table" id="solicitudes">
							<thead>
								<tr>
									<th>Deudor</th>
									<th>Obligación</th>
									<th class="text-center">Ajuste capital</th>
									<th class="text-center">Ajuste intereses</th>
									<th class="text-center">Ajsute seguro</th>
									<th class="text-center">Total</th>
									<th class="text-center">Nuevo capital</th>
								</tr>
							</thead>
							<tbody>
								@php
									$ajustesCreditos = $proceso->detallesAjusteCreditoLote;
								@endphp
								@foreach ($ajustesCreditos as $ajuste)
									@php
										$tercero = $ajuste->getTercero();
										$obligacion = $ajuste->getSolicitudCredito();
										$capital = $ajuste->getValorCapital();
										$interes = $ajuste->getValorIntereses();
										$seguro = $ajuste->getValorSeguro();
										$total = $ajuste->getValorTotal();
										$saldo = $ajuste->getSaldoObligacion();
										$saldo += $capital;
									@endphp
									<tr>
										<td>{{ $tercero->identificacion . ' ' . $tercero->nombre_corto }}</td>
										<td>{{ $obligacion->numero_obligacion }}</td>
										<td class="text-right">${{ number_format($capital, 0) }}</td>
										<td class="text-right">${{ number_format($interes, 0) }}</td>
										<td class="text-right">${{ number_format($seguro, 0) }}</td>
										<td class="text-right">${{ number_format($total, 0) }}</td>
										<td class="text-right">${{ number_format($saldo, 0) }}</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="card-footer">
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(document).ready(function() {
		$('#solicitudes').DataTable( {
			"paging":   true,
			"ordering": true,
			"info":     false
		});
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito en lote
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito en lote</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Procesar archivo créditos en lote</h3>
					<div class="card-tools">
						<a href="{{ url('procesoCreditoLote') }}" class="btn btn-outline-danger btn-sm pull-right">Volver</a>
					</div>
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
									Modalidad de crédito
								</label>
								<br>
								{{ $proceso->modalidad->nombre }}
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">
									Estado
								</label>
								<br>
								@php
									$label = "badge-";
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
								<span class="badge badge-pill {{ $label }}">{{ $proceso->estado }}</span>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">
									Cantidad solicitudes
								</label>
								<br>
								{{ $proceso->cantidad_solicitudes_creditos }}
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">
									Valor
								</label>
								<br>
								${{ number_format($proceso->total_valor_creditos, 0) }}
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
							<a href="{{ route('procesoCreditoLoteLimpiar', $proceso->id) }}" class="btn btn-outline-warning">Limpiar carga</a>
							<a class="btn disabled btn-outline-primary">Evaluar condiciones</a>
							<a href="{{ route('procesoCreditoLoteDesembolsar', $proceso->id) }}" class="btn btn-outline-success">Desembolsar</a>
							<a href="{{ route('procesoCreditoLoteAnular', $proceso->id) }}" class="btn btn-outline-danger pull-right">Anular</a>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table" id="solicitudes">
								<thead>
									<tr>
										<th>Cliente</th>
										<th>Pagaduría</th>
										<th class="text-center">Valor</th>
										<th>Plazo</th>
										<th>Inicio descuento</th>
										<th class="text-center">Cuota</th>
									</tr>
								</thead>
								<tbody>
									@php
										$solicitudesCreditos = $proceso->getSolicitudesCreditos();
									@endphp
									@foreach ($solicitudesCreditos as $solicitud)
										<tr>
											<td>{{ $solicitud->tercero->nombre_completo }}</td>
											<td>{{ $solicitud->tercero->socio->pagaduria->nombre }}</td>
											<td class="text-right">${{ number_format($solicitud->valor_credito, 0) }}</td>
											<td class="text-right">{{ $solicitud->plazo }}</td>
											<td>{{ $solicitud->fecha_primer_pago }}</td>
											<td class="text-right">${{ number_format($solicitud->valor_cuota, 0) }}</td>
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

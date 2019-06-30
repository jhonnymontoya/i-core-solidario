@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Control cierres de periodos
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Control cierres de periodos</li>
		</ol>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Precierre periodo {{ $periodo->mes }} - {{ $periodo->anio }} Cartera</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						<h3>Previsualización información cierre cartera</h3>
						<a class="btn btn-primary xlsx"><i class="fa fa-file-excel-o"></i> XLSX</a>
						<a class="btn btn-primary csv"><i class="fa fa-file-excel-o"></i> CSV</a>
						<a class="btn btn-primary txt"><i class="fa fa-file-text-o"></i> TXT</a>
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-striped precierre">
									<thead>
										<tr>
											<th class="text-center">Identificación</th>
											<th>Nombre</th>
											<th>Estado</th>
											<th>Empresa</th>
											<th>Obligación</th>
											<th>Desembolso</th>
											<th class="text-center">Valor inicial</th>
											<th class="text-center">Tasa M.V</th>
											<th class="text-center">Plazo</th>
											<th class="text-center">Cuota</th>
											<th class="text-center">Altura</th>
											<th class="text-center">Pendientes</th>
											<th>Codigo modalidad</th>
											<th>Nombre modalidad</th>
											<th class="text-center">Saldo capital</th>
											<th class="text-center">Saldo Intereses</th>
											<th class="text-center">Interes causado</th>
											<th class="text-center">Causado anterior</th>
											<th class="text-center">Saldo seguro</th>
											<th class="text-center">Días mora</th>
											<th class="text-center">Capital vencido</th>
											<th>Tipo Garantía</th>
											<th>Forma pago</th>
											<th>Periodicidad</th>
											<th>Terminación estimada</th>
											<th>Último movimiento</th>
											<th>Calificación anterior</th>
											<th>Calificación actual</th>
											<th>Calificación final</th>
											<th class="text-center">% Deterioro capital</th>
											<th class="text-center">Aportes deterioro</th>
											<th class="text-center">Base deterioro</th>
											<th class="text-center">Deterioro capital</th>
											<th class="text-center">% Deterioro intereses</th>
											<th class="text-center">Deterioro Intereses</th>
											<th>Cancelación</th>
											<th>Estado obligación</th>
											<th>Cuenta capital</th>
										</tr>
									</thead>
									<tbody>
										@foreach ($preCierre as $item)
											@php
												$empresa = is_null($item->pagaduria_id) ? '' : $pagadurias[$item->pagaduria_id];
												$fechaCancelacion = "";
												$fechaUltimoPago = "";
												$fechaTerminacionProgramada = "";
												$fechaDesembolso = "";
												try{
													if($item->fecha_cancelacion)
														$fechaCancelacion = \Carbon\Carbon::createFromFormat('Y-m-d 00:00:00.000', $item->fecha_cancelacion)->startOfDay();
													if($item->fecha_ultimo_pago)
														$fechaUltimoPago = \Carbon\Carbon::createFromFormat('Y-m-d 00:00:00.000', $item->fecha_ultimo_pago)->startOfDay();
													if($item->fecha_terminacion_programada)
														$fechaTerminacionProgramada = \Carbon\Carbon::createFromFormat('Y-m-d 00:00:00.000', $item->fecha_terminacion_programada)->startOfDay();
													if($item->fecha_desembolso)
														$fechaDesembolso = \Carbon\Carbon::createFromFormat('Y-m-d 00:00:00.000', $item->fecha_desembolso)->startOfDay();
												}
												catch(\InvalidArgumentException $e){
													//
												}
											@endphp
											<tr>
												<td class="text-right">{{ $item->tercero_numero_identificacion }}</td>
												<td>{{ $item->tercero_nombre }}</td>
												<td>{{ $item->socio_estado }}</td>
												<td>{{ $empresa }}</td>
												<td>{{ $item->numero_obligacion }}</td>
												<td>{{ $fechaDesembolso }}</td>
												<td class="text-right">${{ number_format($item->valor_credito) }}</td>
												<td class="text-right">{{ number_format($item->tasa, 3) }}%</td>
												<td class="text-right">{{ number_format($item->plazo) }}</td>
												<td class="text-right">${{ number_format($item->valor_cuota) }}</td>
												<td class="text-right">{{ number_format($item->altura_cuota) }}</td>
												<td class="text-right">{{ number_format($item->numero_cuotas_pendientes) }}</td>
												<td>{{ $item->modalidad_codigo }}</td>
												<td>{{ $item->modalidad_nombre }}</td>
												<td class="text-right">${{ number_format($item->saldo_capital) }}</td>
												<td class="text-right">${{ number_format($item->saldo_intereses) }}</td>
												<td class="text-right">${{ number_format($item->interes_causado) }}</td>
												<td class="text-right">${{ number_format($item->interes_causado_anterior) }}</td>
												<td class="text-right">${{ number_format($item->saldo_seguro) }}</td>
												<td class="text-right">{{ number_format($item->dias_vencidos) }}</td>
												<td class="text-right">${{ number_format($item->capital_vencido) }}</td>
												<td>{{ $item->tipo_garantia }}</td>
												<td>{{ $item->forma_pago }}</td>
												<td>{{ $item->periodicidad }}</td>
												<td>{{ $fechaTerminacionProgramada }}</td>
												<td>{{ $fechaUltimoPago }}</td>
												<td>{{ $item->calificacion_periodo_anterior }}</td>
												<td>{{ $item->calificacion_actual }}</td>
												<td>{{ $item->calificacion_final }}</td>
												<td class="text-center">{{ number_format($item->porcentaje_deterioro_capital, 2) }}%</td>
												<td class="text-center">${{ number_format($item->valor_aporte_deterioro) }}</td>
												<td class="text-center">${{ number_format($item->base_deterioro) }}</td>
												<td class="text-center">${{ number_format($item->deterioro_capital) }}</td>
												<td class="text-center">{{ number_format($item->porcentaje_deterioro_intereses, 2) }}%</td>
												<td class="text-center">${{ number_format($item->deterioro_intereses) }}</td>
												<td>{{ $fechaCancelacion }}</td>
												<td>{{ $item->estado_solicitud }}</td>
												<td>{{ $item->cuif_capital }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						<a href="{{ route('cierreModulosDetalle', $periodo->id) }}" class="btn btn-danger pull-right">Volver</a>
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
<script type="text/javascript">
	$(function(){
		var instancia = $(".precierre");
		var instancia = new TableExport(instancia, {
			headers: true,
			footers: true,
			filename: '{{ str_slug('preCierre' . date('ymd'), '-') }}',
			bootstrap: false,
			exportButtons: false
		});
		var data = instancia.getExportData()['tableexport-1'];
		$(".xlsx").click(function(e){
			e.preventDefault();
			instancia.export2file(data["xlsx"].data, data["xlsx"].mimeType, data["xlsx"].filename, data["xlsx"].fileExtension);
		});
		$(".csv").click(function(e){
			e.preventDefault();
			instancia.export2file(data["csv"].data, data["csv"].mimeType, data["csv"].filename, data["csv"].fileExtension);
		});
		$(".txt").click(function(e){
			e.preventDefault();
			instancia.export2file(data["txt"].data, data["txt"].mimeType, data["txt"].filename, data["txt"].fileExtension);
		});
	});
</script>
@endpush

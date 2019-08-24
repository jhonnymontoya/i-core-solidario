@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Estadísticos
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Estadísticos</li>
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

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Reportes - Estadísticos</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::all(), ['url' => 'reportes/estadisticos', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<br>
					<div class="row form-horizontal">
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('tipo_reporte')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('tipo_reporte'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Reporte
								</label>
								<div class="col-sm-8">
									{!! Form::select('tipo_reporte', ['ASOCIADOS' => 'Asociados', 'AHORROS' => 'Ahorros', 'CARTERA' => 'Cartera'], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione uno']) !!}
									@if ($errors->has('tipo_reporte'))
										<span class="help-block">{{ $errors->first('tipo_reporte') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('fecha_consulta')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('fecha_consulta'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha
								</label>
								<div class="col-sm-8 input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<?php
										$fechaConsulta = date('Y/m');
										$fechaConsulta = Request::has('fecha_consulta') ? Request::get('fecha_consulta') : $fechaConsulta;
									?>
									{!! Form::text('fecha_consulta', $fechaConsulta, ['class' => 'form-control', 'placeholder' => 'yyyy/mm' ]) !!}
									@if ($errors->has('fecha_consulta'))
										<span class="help-block">{{ $errors->first('fecha_consulta') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="btn-group">
								<button type="submit" class="btn btn-outline-success"><i class="fa fa-play"></i> Procesar</button>
							</div>
						</div>
					</div>
					{!! Form::close() !!}
					<hr>
					{!! $data !!}
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
	$("input[name='fecha_consulta']").datepicker( {
		format: "yyyy/mm",
		viewMode: "months",
		minViewMode: "months",
		autoclose: "true"
	});

	$(function(){
	@if (Request::has('tipo_reporte'))
		@switch(Request::get('tipo_reporte'))
			@case('ASOCIADOS')
				var comparativo = new Morris.Bar({
					element: 'afiliacionesPorMes',
					resize: true,
					data: afiliacionesPorMes,
					barColors: ['#f56954', '#00a65a'],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: labels,
					hideHover: 'auto'
				});
		    @case('AHORROS')
		    	var saldosPorModalidad = new Morris.Donut({
					element: 'saldosPorModalidad',
					resize: true,
					colors: ["#3c8dbc", "#f56954", "#00a65a"],
					data: saldosPorModalidadData,
					hideHover: 'auto'
				});

				var comparativo = new Morris.Bar({
					element: 'comparativoAnual',
					resize: true,
					data: comparativoAhorros,
					barColors: ['#f56954', '#00a65a'],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: labels,
					hideHover: 'auto'
				});

				var variacionAhorros = new Morris.Bar({
					element: 'variacionAhorro',
					resize: true,
					data: variacionAhorroData,
					barColors: ['#f56954', '#00a65a'],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: labels,
					hideHover: 'auto'
				});

				$("select[name='modalidad']").change(function(event){
					seleccion = $(this).val();
					imprimirTabla(seleccion);
				});
				function limpiarTabla() {
					$("#topDiez").find("tbody").empty();
					$("#topDiez").find("tfoot").empty();
				}
				function imprimirTabla(seleccion) {
					limpiarTabla();
					data = topDiez[seleccion];
					$.each(data.DATA, function(index, value){
						var tr = $("<tr>").append(
								$("<td>").text(value.nombre)
							).append(
								$("<td>").addClass("text-right").text("$" + value.value)
							).append(
								$("<td>").addClass("text-right").text(value.participacion + "%")
							);
						$("#topDiez").find("tbody").append(tr);
					});
					var tr = $("<tr>").append(
							$("<th>").text("Total top 10:")
						).append(
							$("<th>").addClass("text-right").text("$" + data.totalSaldo)
						).append(
							$("<th>").addClass("text-right").text(data.totalParticipacion + "%")
						);
					$("#topDiez").find("tfoot").append(tr);
					if(seleccion != 0) {
						var tr = $("<tr>").append(
								$("<th>").text("Total modalidad:")
							).append(
								$("<th>").addClass("text-right").text("$" + data.totalModalidad)
							).append(
								$("<th>").addClass("text-right").text(data.participacionModalidad + "%")
							);
						$("#topDiez").find("tfoot").append(tr);
					}
					var tr = $("<tr>").append(
							$("<th>").text("Total ahorros:")
						).append(
							$("<th>").addClass("text-right").text("$" + data.totalAhorro)
						);
					$("#topDiez").find("tfoot").append(tr);
				}
				imprimirTabla(0);
		        @break
		    @case('CARTERA')
		    	var saldosPorModalidad = new Morris.Donut({
					element: 'saldosPorModalidad',
					resize: true,
					colors: ["#3c8dbc", "#f56954", "#00a65a"],
					data: saldosPorModalidadData,
					hideHover: 'auto'
				});
				var comparativo = new Morris.Bar({
					element: 'comparativoAnual',
					resize: true,
					data: comparativoCartera,
					barColors: ['#f56954', '#00a65a'],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: labels,
					hideHover: 'auto'
				});
				var colocaciones = new Morris.Bar({
					element: 'comparativoColocaciones',
					resize: true,
					data: comparativoColocaciones,
					barColors: ['#f56954', '#00a65a'],
					xkey: 'y',
					ykeys: ['a', 'b'],
					labels: labels,
					hideHover: 'auto'
				});
				$("select[name='modalidad']").change(function(event){
					seleccion = $(this).val();
					imprimirTabla(seleccion);
				});
				function limpiarTabla() {
					$("#topQuince").find("tbody").empty();
					$("#topQuince").find("tfoot").empty();
				}
				function imprimirTabla(seleccion) {
					limpiarTabla();
					data = topQuince[seleccion];
					$.each(data.DATA, function(index, value){
						var tr = $("<tr>").append(
								$("<td>").text(value.nombre)
							).append(
								$("<td>").addClass("text-right").text("$" + value.ahorros)
							).append(
								$("<td>").addClass("text-right").text("$" + value.cartera)
							).append(
								$("<td>").addClass("text-right").text(value.participacionModalidad + "%")
							).append(
								$("<td>").addClass("text-right").text(value.participacionCartera + "%")
							);
						$("#topQuince").find("tbody").append(tr);
					});
					var tr = $("<tr>").append(
							$("<th>").text("Total top 15:")
						).append(
							$("<th>").addClass("text-right").text("$" + data.ahorros)
						).append(
							$("<th>").addClass("text-right").text("$" + data.cartera)
						).append(
							$("<th>").addClass("text-right").text(data.participacionModalidad + "%")
						).append(
							$("<th>").addClass("text-right").text(data.participacionTotalCartera + "%")
						);
					$("#topQuince").find("tfoot").append(tr);
					if(seleccion != 0) {
						var tr = $("<tr>").append(
								$("<th>").text("Total modalidad:")
							).append(
								$("<th>").addClass("text-right").text("$" + data.totalModalidadAhorro)
							).append(
								$("<th>").addClass("text-right").text("$" + data.totalModalidadCartera)
							).append(
								$("<th>").addClass("text-right").text("")
							).append(
								$("<th>").addClass("text-right").text(data.totalParticipacionCartera + "%")
							);
						$("#topQuince").find("tfoot").append(tr);
					}
					var tr = $("<tr>").append(
							$("<th>").text("Total cartera:")
						).append(
							$("<th>").addClass("text-right").text("$" + data.totalAhorros)
						).append(
							$("<th>").addClass("text-right").text("$" + data.totalCartera)
						);
					$("#topQuince").find("tfoot").append(tr);
				}
				imprimirTabla(0);
				var consolidadoColocacionesAnio = $('#consolidadoColocacionesAnio').get(0).getContext('2d');
				var consolidadoColocacionesAnioConfig = {
					type: 'line',
					data: {
						labels  : labelsConsolidadoColocacionesAnioConfig,
						datasets: [{
								label: 'Año actual',
								backgroundColor: 'rgb(17, 65, 219)',
								borderColor: 'rgb(17, 65, 219)',
								data: dataActual,
								fill: false
						},{
								label: 'Año anterior',
								backgroundColor: 'rgb(225, 50, 34)',
								borderColor: 'rgb(225, 50, 34)',
								data: dataAnterior,
								fill: false
							}]
					},
					options: {
						responsive: true,
						tooltips: {
							mode: 'index',
							intersect: false,
						},
						hover: {
							mode: 'nearest',
							intersect: true
						}
					}
				};
				consolidadoColocacionesAnio = new Chart(consolidadoColocacionesAnio, consolidadoColocacionesAnioConfig);
		        @break		
		    @default
		@endswitch
		
	@endif		
	});
</script>
@endpush
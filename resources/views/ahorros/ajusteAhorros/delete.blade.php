@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas voluntarias
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Cuotas voluntarias</li>
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
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Cuotas voluntarias</h3>
				</div>
				<div class="card-body">
					{!! Form::open(['url' => ['cuotaVoluntaria', $cuota], 'method' => 'delete', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="row">
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-danger">
									<h4>
										<i class="fa fa-ban"></i>&nbsp;Alerta!
									</h4>
									Esta a punto de eliminar la cuota de ahorro.
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table">
								<thead>
									<tr>
										<th>Modalidad</th>
										<th>Variable</th>
										<th class="text-right">Valor</th>
										<th class="text-right">Monto cuota</th>
										<th>Periodicidad</th>
										<th>Periodo inicial</th>
										<th>Periodo final</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$tipo_calculo = "";
										$montoCuota = 0;
										$valor = 0;
										switch ($cuota->factor_calculo) {
											case 'PORCENTAJESUELDO':
												$tipo_calculo = "% Sueldo";
												$valor = number_format($cuota->valor) . '%';
												$montoCuota = ($socio->sueldo_mes * $cuota->valor) / 100;
												$montoCuota = number_format($montoCuota);
												break;
											case 'PORCENTAJESMMLV':
												$tipo_calculo = "% SMMLV";
												$valor = number_format($cuota->valor) . '%';
												break;
											case 'VALORFIJO':
												$tipo_calculo = "Valor fijo";
												$valor = '$' . number_format($cuota->valor);
												$montoCuota = $cuota->valor;
												$montoCuota = number_format($montoCuota);
												break;												
											default:
												break;
										}
										$periodicidad = "";
										switch($cuota->periodicidad)
										{
											case 'DIARIO': $periodicidad = 'Diario'; break;
											case 'SEMANAL': $periodicidad = 'Semanal'; break;
											case 'DECADAL': $periodicidad = 'Decadal'; break;
											case 'CATORCENAL': $periodicidad = 'Catorcenal'; break;
											case 'QUINCENAL': $periodicidad = 'Quincenal'; break;
											case 'MENSUAL': $periodicidad = 'Mensual'; break;
											case 'BIMESTRAL': $periodicidad = 'Bimestral'; break;
											case 'TRIMESTRAL': $periodicidad = 'Trimestral'; break;
											case 'CUATRIMESTRAL': $periodicidad = 'Cuatrimestral'; break;
											case 'SEMESTRAL': $periodicidad = 'Semestral'; break;
											case 'ANUAL': $periodicidad = 'Anual'; break;
										}
									?>
									<tr>
										<td>{{ $cuota->modalidadAhorro->codigo . ' - ' . $cuota->modalidadAhorro->nombre }}</td>
										<td>{{ $tipo_calculo }}</td>
										<td class="text-right">{{ $valor }}</td>
										<td class="text-right">${{ $montoCuota }}</td>
										<td>{{ $periodicidad }}</td>
										<td>{{ empty($cuota->periodo_inicial) ? '' : $cuota->periodo_inicial->toFormattedDateString() }}</td>
										<td>{{ empty($cuota->periodo_final) ? '' : $cuota->periodo_final->toFormattedDateString() }}</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Confirmar', ['class' => 'btn btn-danger']) !!}
						<a href="{{ url('cuotaVoluntaria?socio=' . $cuota->socio->id) }}" class="btn btn-success pull-right">Volver</a>
					</div>
					{!! Form::close() !!}
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
@endpush
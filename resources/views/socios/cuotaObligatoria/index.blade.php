@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cuotas obligatorias
			<small>Socios</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Socios</a></li>
			<li class="active">Cuotas obligatorias</li>
		</ol>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		<br>
		<div class="box box-primary">
			<div class="box-header with-border">
				<h3 class="box-title">Cuotas obligatorias</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('socio'), ['url' => 'cuotaObligatoria', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-11">
						<div class="form-group">
							<label class="col-sm-2 control-label">
								@if ($errors->has('tipo_comprobante_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione socio
							</label>
							<div class="col-sm-8">
								{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
							</div>
						</div>
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if($socio)
					<br>
					<div class="row">
						<div class="col-md-12">
							<label>Cuotas obligatorias para:</label> {{$socio->tercero->nombre_completo}}
							<a href="{{ route('cuotaObligatoriaCreate', $socio) }}" class="btn btn-success pull-right">Editar</a>
						</div>
					</div>
					<br><br>
					@if($socio->cuotasObligatorias->count())
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Cuota</th>
											<th>Factor</th>
											<th class="text-right">Valor</th>
											<th class="text-right">Cuota mensual</th>
											<th class="text-center">Validación</th>
										</tr>
									</thead>
									<tbody>
										@php
											$total = 0;
										@endphp
										@foreach($socio->cuotasObligatorias as $cuota)
											<?php
												$tipo_calculo = "";
												$montoCuota = 0;
												$valor = 0;
												switch ($cuota->tipo_calculo) {
													case 'PORCENTAJESUELDO':
														$tipo_calculo = "% Sueldo";
														$montoCuota = ($socio->sueldo_mes * $cuota->valor) / 100;
														$total += $montoCuota;
														$montoCuota = number_format($montoCuota);
														$valor = number_format($cuota->valor, 2) . "%";
														break;
													case 'PORCENTAJESMMLV':
														$tipo_calculo = "% SMMLV";
														$valor = number_format($cuota->valor, 2) . "%";
														$total += $cuota->valor;
														break;
													case 'VALORFIJO':
														$tipo_calculo = "Valor fijo";
														$montoCuota = $cuota->valor;
														$total += $montoCuota;
														$montoCuota = number_format($montoCuota);
														$valor = "$" . number_format($cuota->valor, 0);
														break;												
													default:
														break;
												}
											?>
											<tr>
												<td>{{ $cuota->modalidadAhorro->nombre }}</td>
												<td>{{ $tipo_calculo }}</td>
												<td class="text-right">{{ $valor }}</td>
												<td class="text-right">${{ $montoCuota }}</td>
												<td class="text-center">
													@if($cuota->es_reglamentaria)
														<label class="label label-success">Reglamentaria</label>
													@else
														<label class="label label-danger">No reglamentaria</label>
													@endif
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">
								<em>Total cuotas obligatorias mes: <strong id="id_total">${{ number_format($total) }}</strong></em>
							</div>
						</div>
					@else
						<div class="row">
							<div class="col-md-12">
								<p>Sin cuotas obligatorias</p>
							</div>
						</div>
					@endif
				@endif
			</div>
			<div class="box-footer">
				@if($socio)
					<span class="label label-primary">{{ $socio->cuotasObligatorias->count() }}</span>&nbsp;elementos.
				@endif
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
		$("select[name='socio']").selectAjax("{{ url('socio/getSocio') }}", {id:"{{Request::get('socio')}}"});
	});
</script>
@endpush
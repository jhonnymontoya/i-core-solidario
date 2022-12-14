@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas obligatorias
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Cuotas obligatorias</li>
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
					<h3 class="card-title">Cuotas obligatorias</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('socio'), ['url' => 'cuotaObligatoria', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row form-horizontal">
						<div class="col-md-11">
							<div class="form-group row">
								<label class="col-sm-2 control-label">
									@if ($errors->has('tipo_comprobante_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Seleccione socio
								</label>
								<div class="col-sm-10">
									{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
								</div>
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}
					@if($socio)
						<br>
						<div class="row">
							<div class="col-md-12">
								<label>Cuotas obligatorias para:</label> {{$socio->tercero->nombre_completo}}
								<a href="{{ route('cuotaObligatoriaCreate', $socio) }}" class="btn btn-outline-success float-right">Editar</a>
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
															<label class="badge badge-pill badge-success">Reglamentaria</label>
														@else
															<label class="badge badge-pill badge-danger">No reglamentaria</label>
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
				<div class="card-footer">
					@if($socio)
						<span class="badge badge-pill badge-primary">{{ $socio->cuotasObligatorias->count() }}</span>&nbsp;elementos.
					@endif
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
		$("select[name='socio']").selectAjax("{{ url('socio/getSocio') }}", {id:"{{Request::get('socio')}}"});
	});
</script>
@endpush
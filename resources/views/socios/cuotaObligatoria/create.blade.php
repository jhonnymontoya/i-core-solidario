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
					@if($socio)
						<br>
						<div class="row">
							<div class="col-md-12">
								<label>Editando cuotas obligatorias para:</label> {{$socio->tercero->nombre_completo}}
							</div>
						</div>
						<br><br>
						{!! Form::model($socio, ['url' => ['cuotaObligatoria', $socio], 'method' => 'put', 'role' => 'form']) !!}
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Tipo cuota</th>
											<th>Factor cuota</th>
											<th class="text-right">Valor cálculo</th>
											<th class="text-right">Cuota mensual</th>
										</tr>
									</thead>
									<tbody>
										<?php $total = 0; ?>
										@foreach($tiposCuotasObligatorias as $tipoCuotaObligatoria)
											<?php
												$tipo_calculo = "";
												$montoCuota = 0;
												$valor = 0;
												switch ($tipoCuotaObligatoria->tipo_calculo) {
													case 'PORCENTAJESUELDO':
														$tipo_calculo = "% Sueldo";
														$montoCuota = ($socio->sueldo_mes * $tipoCuotaObligatoria->valor) / 100;
														$total += $montoCuota;
														$montoCuota = number_format($montoCuota);
														$valor = number_format($tipoCuotaObligatoria->valor, 2);
														break;
													case 'PORCENTAJESMMLV':
														$tipo_calculo = "% SMMLV";
														$valor = number_format($tipoCuotaObligatoria->valor, 2);
														break;
													case 'VALORFIJO':
														$tipo_calculo = "Valor fijo";
														$montoCuota = $tipoCuotaObligatoria->valor;
														$total += $montoCuota;
														$montoCuota = number_format($montoCuota);
														$valor = round($tipoCuotaObligatoria->valor);
														break;
													default:
														break;
												}
											?>
											{!! Form::hidden('tipoCuota[]', $tipoCuotaObligatoria->id) !!}
											<tr>
												<td>{{ $tipoCuotaObligatoria->codigo }} - {{ $tipoCuotaObligatoria->nombre }}</td>
												<td>
													<div class="form-group">
														@php
															$valid = $errors->has('factor[]') ? 'is-invalid' : '';
														@endphp
														{!! Form::select('factor[]', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor fijo'], $tipoCuotaObligatoria->tipo_calculo, ['class' => [$valid, 'form-control', 'select2'], 'autocomplete' => 'off', 'data-id' => $tipoCuotaObligatoria->id, 'id' => 'factor' . $tipoCuotaObligatoria->id]) !!}
														@if ($errors->has('factor[]'))
															<div class="invalid-feedback">{{ $errors->first('factor[]') }}</div>
														@endif
													</div>
												</td>
												<td class="text-right">
													<div class="form-group">
														@php
															$valid = $errors->has('valor[]') ? 'is-invalid' : '';
														@endphp
														{!! Form::text('valor[]', $valor, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Valor', 'data-id' => $tipoCuotaObligatoria->id, 'id' => 'valor' . $tipoCuotaObligatoria->id]) !!}
														@if ($errors->has('valor[]'))
															<div class="invalid-feedback">{{ $errors->first('valor[]') }}</div>
														@endif
													</div>
												</td>
												<td class="text-right" id="monto_cuota{{ $tipoCuotaObligatoria->id }}">${{ $montoCuota }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-12 text-right">
								{!! Form::submit('Confirmar', ['class' => 'btn btn-outline-success']) !!}
								&nbsp;<a href="{{ url('cuotaObligatoria?socio=' . $socio->id) }}" class="btn btn-outline-danger">Cancelar</a>
							</div>
						</div>
						{!! Form::close() !!}
					@endif
				</div>
				<div class="card-footer">
					@if($socio)
						<span class="badge badge-pill badge-primary">{{ $tiposCuotasObligatorias->count() }}</span>&nbsp;elementos.
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
		var sueldoMes = {{ $socio->sueldo_mes }};
		$("#id_total").html(sueldoMes);

		$("input[name='valor[]']").on('keyup change', function(e){
			calcular($(this).data("id"));
		});

		$("select[name='factor[]']").on('change', function(e){
			calcular($(this).data("id"));
		});

		function calcular(id)
		{
			valor = $("#valor" + id).val();
			factor = $("#factor" + id).find('option:selected').val();
			total = 0;
			calculo = 0;

			switch (factor) {
				case 'PORCENTAJESUELDO':
					calculo = (sueldoMes * valor) / 100;
					break;
				case 'PORCENTAJESMMLV':
					break;
				case 'VALORFIJO':
					calculo = valor;
					break;												
				default:
					break;
			}
			calculo = calculo || 0;
			calculo = $().formatoMoneda(Math.round(calculo));
			$("#monto_cuota" + id).html("$" + calculo);
		}
	});
</script>
@endpush
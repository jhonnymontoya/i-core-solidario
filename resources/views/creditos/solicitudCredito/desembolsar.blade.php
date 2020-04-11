@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito</li>
					</ol>
				</div>
			</div>
		</div>
	</section>
@php
	//dd($errors);
@endphp
	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
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
			{!! Form::model($solicitud, ['url' => ['solicitudCredito', $solicitud, 'desembolsar'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask', 'name' => 'solicitud_credito']) !!}
			{!! Form::hidden('modalidad', $solicitud->modalidadCredito->id) !!}
			{!! Form::hidden('solicitante', $solicitud->tercero->id) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Aprobar solicitud de crédito</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Modalidad de crédito</label>
								{!! Form::text('mod', $solicitud->modalidadCredito->codigo . ' - ' . $solicitud->modalidadCredito->nombre, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'Modalidad de crédito', 'readonly']) !!}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Solicitante</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-male"></i>
										</span>
									</div>
									@php
										$nombreMostar = $solicitud->tercero->tipoIdentificacion->codigo . ' ' . $solicitud->tercero->numero_identificacion . ' - ' . $solicitud->tercero->nombre_corto;
									@endphp
									<a href="{{ url('socio/consulta') }}?socio={{ $solicitud->tercero->socio->id }}&fecha={{ $solicitud->fecha_solicitud }}" target="_blank" class="form-control" style="background-color: #eee;" >{{ $nombreMostar }} <small><i class="fas fa-external-link-alt"></i></small></a>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Fecha solicitud</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_solicitud', $solicitud->fecha_solicitud, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">Fecha aprobación</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_aprobacion', $solicitud->fecha_aprobacion, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Pagaduría</label>
								@php
									$socio = optional($solicitud->tercero)->socio;
									$pagaduria = empty($socio) ? '' : $socio->pagaduria->nombre;
								@endphp
								{!! Form::text('pagaduria', $pagaduria, ['class' => 'form-control', 'placeholder' => 'Pagaduría', 'autocomplete' => 'off', 'readonly']) !!}
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Periodicidad pagaduría</label>
								@php
									$periodicidad = empty($socio) ? '' : $socio->pagaduria->periodicidad_pago;
								@endphp
								{!! Form::text('periodicidadPagaduria', $periodicidad, ['class' => 'form-control', 'placeholder' => 'Pagaduría', 'autocomplete' => 'off', 'readonly']) !!}
							</div>
						</div>
					</div>
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-12">
							<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
						</div>
					</div>
					{{-- FIN FILA --}}
					<hr>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('valor_credito') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor solicitud</label>
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text">$</span></div>
									{!! Form::text('valor_credito', $solicitud->valor_credito, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Valor solicitud', 'data-maskMoney', 'readonly']) !!}
									@if ($errors->has('valor_credito'))
										<div class="invalid-feedback">{{ $errors->first('valor_credito') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Tasa M.V.</label>
								<div class="input-group">
									{!! Form::text('tasa', number_format($solicitud->tasa, 2), ['class' => ['form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Tasa M.V.', 'readonly']) !!}
									<div class="input-group-append"><span class="input-group-text">%</span></div>
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<label class="control-label">&nbsp;</label>
							<br>
							<a href="{{ route('solicitudCreditoConsolidacion', $solicitud) }}" class="btn btn-outline-primary">Recoger obligaciones vigentes</a>
						</div>
					</div>

					<div></div>
					<br>
					<hr>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('plazo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Número de cuotas</label>
								{!! Form::number('plazo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de cuotas', 'min' => '1', 'step' => '1', 'atofocus']) !!}
								@if ($errors->has('plazo'))
									<div class="invalid-feedback">{{ $errors->first('plazo') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('forma_pago') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Forma de pago</label>
								{!! Form::select('forma_pago', ['NOMINA' => 'Nómina', 'PRIMA' => 'Prima', 'CAJA' => 'Caja'], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('forma_pago'))
									<div class="invalid-feedback">{{ $errors->first('forma_pago') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('periodicidad') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Periodicidad de pago</label>
								{!! Form::select('periodicidad', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una periodicidad']) !!}
								@if ($errors->has('periodicidad'))
									<div class="invalid-feedback">{{ $errors->first('periodicidad') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_primer_pago') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha primer pago</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::select('fecha_primer_pago', $programaciones, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('fecha_primer_pago'))
										<div class="invalid-feedback">{{ $errors->first('fecha_primer_pago') }}</div>
									@endif
								</div>
							</div>
						</div>

						@if($solicitud->modalidadCredito->tipo_cuota == 'CAPITAL')
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_primer_pago_intereses') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">fecha primer pago intereses</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::select('fecha_primer_pago_intereses', $programaciones, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('fecha_primer_pago_intereses'))
										<div class="invalid-feedback">{{ $errors->first('fecha_primer_pago_intereses') }}</div>
									@endif
								</div>
							</div>
						</div>
						@endif

						@if($solicitud->modalidadCredito->acepta_cuotas_extraordinarias)
						<div class="col-md-4">
							<label class="control-label">&nbsp;</label>
							<br>
							<a href="{{ route('solicitudCredito.cuotasExtraordinarias', $solicitud->id) }}" class="btn btn-outline-primary">Agregar cuotas extraordinarias</a>
						</div>
						@endif
					</div>

					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_desembolso') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha desembolso</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_desembolso', $solicitud->fecha_solicitud, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_desembolso'))
										<div class="invalid-feedback">{{ $errors->first('fecha_desembolso') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<label class="control-label">&nbsp;</label>
							<br>
							{!! Form::submit('Desembolsar', ['class' => 'btn btn-outline-success']) !!}
							<a href="{{ url('solicitudCredito') }}" class="btn btn-outline-danger pull-right">Volver</a>
						</div>
					</div>

					@if($solicitud->amortizaciones->count())
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('observaciones') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Observaciones</label>
								{!! Form::textarea('observaciones', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Observaciones', 'style' => 'height:100px;']) !!}
								@if ($errors->has('observaciones'))
									<div class="invalid-feedback">{{ $errors->first('observaciones') }}</div>
								@endif
							</div>
						</div>
					</div>
					@endif

					<hr>
					<div class="row">
						<div class="col-md-12">
							<a href="#" id="verCondiciones" class="btn btn-outline-info btn-sm">Ver condiciones</a>
							<a href="#" id="verAmortizacion" class="btn btn-outline-info btn-sm">Ocultar amortización</a>
							<a href="#" id="verDocumentacion" class="btn btn-outline-info btn-sm">Actualizar documentación</a>
							<a href="{{ route('solicitudCreditoGarantias', $solicitud->id) }}" class="btn btn-outline-info btn-sm">Garantías</a>
							<a href="{{ route('solicitudCreditoAnular', $solicitud) }}" class="btn btn-outline-warning btn-sm">Anular solicitud</a>
						</div>
					</div>

					<div id="condiciones" style="display: none;" data-visible="false">
						<br>
						<div class="row">
							<div class="col-md-12">
								<h3>Condiciones</h3>
							</div>
						</div>
						<div class="row" style="margin-left:20px; margin-right:20px;">
							<div class="col-md-12 table-responsive">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Condición</th>
											<th>Valor parámetro</th>
											<th>Valor solicitud</th>
											<th>Cumple</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($solicitud->cumplimientoCondiciones as $condicion)
											@if($condicion->condicion == 'Cupo')
												<tr>
													<td>{{ $condicion->condicion }}</td>
													<td>{{ number_format($condicion->valor_parametro, 0) }}</td>
													<td>{{ number_format($condicion->valor_solicitud, 0) }}</td>
													<td>
														<?php
															$cumple = $condicion->cumple_parametro;
															$aprobado = false;
															if(!$cumple) {
																$cumple = empty($condicion->es_aprobada) ? false : true;
																$aprobado = $cumple;
															}
														?>
														<span class="badge badge-pill badge-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
															<?php
																$aprobar = false;
																if($cumple) {
																	if($aprobado) {
																		$aprobar = true;
																		echo "aprobado";
																	}
																	else {
																		echo "Sí";
																	}
																}
																else {
																	$aprobar = true;
																	echo "No";
																}
															?>
														</span>
													</td>
													<td>
														@if($aprobar)
															<a href="#" data-id="{{ $condicion->id }}" class="btn btn-outline-{{ $aprobado ? 'danger' : 'success' }} btn-sm b-accion" onclick="alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar' }}</a>
														@endif
													</td>
												</tr>
											@else
												<tr>
													<td>{{ $condicion->condicion }}</td>
													<td>
														@if($condicion->condicion == 'Plazo')
															{{ number_format($condicion->valor_parametro, 0) }}M
														@elseif($condicion->condicion == 'Endeudamiento')
															{{ number_format($condicion->valor_parametro, 0) }}%
														@else
															{{ number_format($condicion->valor_parametro, 0) }}
														@endif
													</td>
													<td>
														@if($condicion->condicion == 'Plazo')
															{{ number_format($condicion->valor_solicitud, 0) }}M
														@elseif($condicion->condicion == 'Endeudamiento')
															{{ number_format($condicion->valor_solicitud, 0) }}%
														@else
															{{ number_format($condicion->valor_solicitud, 0) }}
														@endif
													</td>
													<td>
														<?php
															$cumple = $condicion->cumple_parametro;
															$aprobado = false;
															if(!$cumple) {
																$cumple = empty($condicion->es_aprobada) ? false : true;
																$aprobado = $cumple;
															}
														?>
														<span class="badge badge-pill badge-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
															<?php
																$aprobar = false;
																if($cumple) {
																	if($aprobado) {
																		$aprobar = true;
																		echo "aprobado";
																	}
																	else {
																		echo "Sí";
																	}
																}
																else {
																	$aprobar = true;
																	echo "No";
																}
															?>
														</span>
													</td>
													<td>
														@if($aprobar)
															@if($condicion->condicion != 'Documentación')
																<a href="#" data-id="{{ $condicion->id }}" class="btn btn-outline-{{ $aprobado ? 'danger' : 'success' }} btn-sm b-accion" onclick="alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar' }}</a>
															@else
																<a href="#" data-id="{{ $condicion->id }}" class="btn btn-outline-{{ $aprobado ? 'danger' : 'success'}} btn-sm b-accion" onclick="javascript:alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar'}}</a>
															@endif
														@endif
													</td>
												</tr>
											@endif
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>

					@if($solicitud->amortizaciones->count())
					<div id="amortizacion" style="display: block;" data-visible="true">
						<br>
						<div class="row">
							<div class="col-md-12">
								<h3>Amortización</h3>
							</div>
						</div>
						<div class="row" style="margin-left: 30px; margin-right: 30px;">
							<div class="col-md-3">
								<div class="row">
									<div class="col-md-8"><label>Tasa seguro cartera</label></div>
									<div class="col-md-4">{{ empty($solicitud->seguroCartera) ? 0 : number_format($solicitud->seguroCartera->tasa_mes, 4) }}%</div>
								</div>
							</div>
							<div class="col-md-5">
								<div class="row">
									<div class="col-md-8"><label>Porcentaje capital en extraordinarias</label></div>
									<div class="col-md-4">{{ number_format($solicitud->porcentajeCapitalEnExtraordinarias(), 2) }}%</div>
								</div>
							</div>
							<div class="col-md-4">
								<div class="row">
									<div class="col-md-6"><label>Tasa E.A.</label></div>
									<div class="col-md-6">
										<?php
											$tasaEA = ($solicitud->tasa / 100) + 1;
											$tasaEA = pow($tasaEA, 12) - 1;
											$tasaEA = number_format($tasaEA * 100, 2);
										?>
										{{ $tasaEA }}%
									</div>
								</div>
							</div>
						</div>
						<br>
						<div class="row" style="margin-left:20px; margin-right:20px;">
							<div class="col-md-12 table-responsive">
								<table id="tablaAmortizacion" class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Cuota</th>
											<th>Naturaleza cuota</th>
											<th>Forma pago</th>
											<th>Fecha pago</th>
											<th class="text-center">Capital</th>
											<th class="text-center">Intereses</th>
											<th class="text-center">Seguro cartera</th>
											<th class="text-center">Total cuota</th>
											<th class="text-center">Nuevo saldo</th>
										</tr>
									</thead>
									<tbody>
										@foreach($solicitud->amortizaciones as $amortizacion)
											<tr>
												<td>{{ $amortizacion->numero_cuota }}</td>
												<td>{{ $amortizacion->naturaleza_cuota }}</td>
												<td>{{ $amortizacion->forma_pago }}</td>
												<td>{{ $amortizacion->fecha_cuota }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_capital, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_intereses, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->abono_seguro_cartera, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->total_cuota, 0) }}</td>
												<td class="text-right">${{ number_format($amortizacion->nuevo_saldo_capital, 0) }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					@endif

					<div id="documentacion" style="display: none;" data-visible="false">
						<br>
						<div class="row">
							<div class="col-md-12">
								<h3>Documentación</h3>
							</div>
						</div>
						<div class="row" style="margin-left:20px; margin-right:20px;">
							<div class="col-md-12 table-responsive">
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Documento</th>
											<th>Obligatorio</th>
											<th>Cumple</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach($solicitud->documentos as $documento)
											<tr>
												<td>{{ $documento->documento }}</td>
												<td>{{ $documento->obligatorio ? 'Si' : 'Opcional' }}</td>
												<td>
													<?php
														$cumple = $documento->pivot->cumple;
													?>
													<span class="badge badge-pill badge-documento-{{ $documento->id }} label-{{ $cumple ? 'success' : 'danger' }}">{{ $cumple ? 'Sí' : 'No' }}</span>
												</td>
												<td>
													<a href="#" data-id="{{ $documento->id }}" class="btn btn-outline-{{ $cumple ? 'danger' : 'success' }} btn-sm b-accion" onclick="javascript:alternarDocumento(this)">{{ $cumple ? 'No cumple' : 'Cumple' }}</a>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				</div>

				<div class="card-footer">
				</div>
			</div>
			{!! Form::close() !!}
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
		$(window).load(function(){
			$("input[name='valor_credito']").maskMoney('mask');
			@if($solicitud->amortizaciones->count())
			$('#tablaAmortizacion').DataTable({"scrollY": '340px', "scrollCollapse": true, "paging": false, "ordering": false, "info": false, "searching": false});
			$("#amortizacion").data("visible", false);
			$("#amortizacion").hide();
			@endif
		});

		$(".select2").select2();

		$("#verAmortizacion").click(function(e){
			e.preventDefault();
			if($("#amortizacion").data("visible")){
				$(this).text('Ver amortización');
				$("#amortizacion").data("visible", false);
				$("#amortizacion").hide();
			}
			else {
				$(this).text('Ocultar amortización');
				$("#amortizacion").data("visible", true);
				$("#amortizacion").show();
			}
		});

		$("#verCondiciones").click(function(e){
			e.preventDefault();
			if($("#condiciones").data("visible")){
				$(this).text('Ver condiciones');
				$("#condiciones").data("visible", false);
				$("#condiciones").hide();
			}
			else {
				$(this).text('Ocultar condiciones');
				$("#condiciones").data("visible", true);
				$("#condiciones").show();
			}
		});

		$("#verDocumentacion").click(function(e){
			e.preventDefault();
			if($("#documentacion").data("visible")){
				$(this).text('Actualizar documentacion');
				$("#documentacion").data("visible", false);
				$("#documentacion").hide();
			}
			else {
				$(this).text('Ocultar documentacion');
				$("#documentacion").data("visible", true);
				$("#documentacion").show();
			}
		});
		$(".b-accion").click(function(e){
			e.preventDefault();
		});
		$("#calcularAmortizacion").click(function(e){
			e.preventDefault();
			$("input[name='valor_solicitud']").maskMoney('unmask');
			var $data = $("form[name='solicitud_credito']").serialize();
			$("input[name='valor_solicitud']").maskMoney('mask');
			$.ajax({
				url: 'calcularAmortizacion',
				type: 'GET',
				data: $data
			}).done(function(data){
				console.log(data);
			}).fail(function(data){
				var $error = jQuery.parseJSON(data.responseText);
				error($error);
			});
		});
		<?php
			if($solicitud->modalidadCredito->es_tasa_condicionada) {
				$condicion = $solicitud->modalidadCredito->condicionesModalidad()->whereTipoCondicion('TASA')->first();

				if(!empty($condicion)) {
					if($condicion->condicionado_por == 'MONTO') {
						?>
						$("input[name='valor_credito']").on("keyup", function(e){
							$valor = $(this).maskMoney('cleanvalue');
							getTasaCondicionada($valor);
						});
						<?php
					}
					elseif($condicion->condicionado_por == 'PLAZO') {
						?>
						$("input[name='plazo']").on("keyup", function(e){
							$valor = $(this).val();
							$periodicidad = $("select[name='periodicidad']").val();
							getTasaCondicionada($valor, $periodicidad);
						});
						$("select[name='periodicidad']").on("change", function(e){
							$valor = $("input[name='plazo']").val();
							$periodicidad = $(this).val();
							getTasaCondicionada($valor, $periodicidad);
						});
						<?php
					}

					?>
					function getTasaCondicionada(valor, periodicidad) {
						$data = "valor=" + valor + "&periodicidad=" + periodicidad;
						$.ajax({
							url: 'getTasaCondicionada',
							type: 'GET',
							data: $data
						}).done(function(data){
							$("input[name='tasa']").val(data.tasa);
						}).fail(function(data){
							$("input[name='tasa']").val(0);
						});
					}
					<?php
				}
			}
		?>
	});
	function alternarCondicion(obj) {
		var $obj = $(obj);
		$.ajax({
			url: '{{ route('solicitudCreditoAlternarCondicion', $solicitud->id) }}',
			type: 'GET',
			data: 'condicion=' + $obj.data('id')
		}).done(function(data){
			if(data.estado){
				$obj.removeClass("btn-outline-success");
				$obj.addClass("btn-outline-danger");
				$obj.text("Desaprobar");
				$(".label-" + $obj.data('id')).removeClass("label-danger");
				$(".label-" + $obj.data('id')).addClass("label-success");
				$(".label-" + $obj.data('id')).text("Aprobado");
			}
			else
			{
				$obj.removeClass("btn-outline-danger");
				$obj.addClass("btn-outline-success");
				$obj.text("Aprobar");
				$(".label-" + $obj.data('id')).removeClass("label-success");
				$(".label-" + $obj.data('id')).addClass("label-danger");
				$(".label-" + $obj.data('id')).text("No");
			}
		}).fail(function(data){});
	}

	function alternarDocumento(obj) {
		var $obj = $(obj);
		$.ajax({
			url: '{{ route('solicitudCreditoAlternarDocumento', $solicitud->id) }}',
			type: 'GET',
			data: 'documento=' + $obj.data('id')
		}).done(function(data){
			if(data.estado){
				$obj.removeClass("btn-outline-success");
				$obj.addClass("btn-outline-danger");
				$obj.text("No cumple");
				$(".label-documento-" + $obj.data('id')).removeClass("label-danger");
				$(".label-documento-" + $obj.data('id')).addClass("label-success");
				$(".label-documento-" + $obj.data('id')).text("Sí");
			}
			else
			{
				$obj.removeClass("btn-outline-danger");
				$obj.addClass("btn-outline-success");
				$obj.text("Cumple");
				$(".label-documento-" + $obj.data('id')).removeClass("label-success");
				$(".label-documento-" + $obj.data('id')).addClass("label-danger");
				$(".label-documento-" + $obj.data('id')).text("No");
			}
		}).fail(function(data){});
	}
</script>
@endpush

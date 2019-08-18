@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Solicitudes de crédito
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Solicitudes de crédito</li>
		</ol>
	</section>

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

		<div class="row">
			{!! Form::model($solicitud, ['url' => ['solicitudCredito', $solicitud, 'aprobar'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask', 'name' => 'solicitud_credito']) !!}
			{!! Form::hidden('modalidad', $solicitud->modalidadCredito->id) !!}
			{!! Form::hidden('solicitante', $solicitud->tercero->id) !!}
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Aprobar solicitud de crédito</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('modalidad')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('modalidad'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Modalidad de crédito
									</label>
									{!! Form::text('modalidad_nombre', $solicitud->modalidadCredito->codigo . ' - ' . $solicitud->modalidadCredito->nombre, ['class' => 'form-control', 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off', 'readonly']) !!}
									@if ($errors->has('modalidad'))
										<span class="help-block">{{ $errors->first('modalidad') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('solicitante')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('solicitante'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Solicitante
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-male"></i></span>
										@php
											$nombreMostar = $solicitud->tercero->tipoIdentificacion->codigo . ' ' . $solicitud->tercero->numero_identificacion . ' - ' . $solicitud->tercero->nombre_corto;
										@endphp
										<a href="{{ url('socio/consulta') }}?socio={{ $solicitud->tercero->socio->id }}&fecha={{ $solicitud->fecha_solicitud }}" target="_blank" class="form-control" style="background-color: #eee;" >{{ $nombreMostar }} <small><i class="fa fa-external-link"></i></small></a>
									</div>
									@if ($errors->has('solicitante'))
										<span class="help-block">{{ $errors->first('solicitante') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fecha_solicitud')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fecha_solicitud'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha solicitud
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::text('fecha_solicitud', $solicitud->fecha_solicitud, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'readonly']) !!}
									</div>
									@if ($errors->has('fecha_solicitud'))
										<span class="help-block">{{ $errors->first('fecha_solicitud') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">
										Pagaduría
									</label>
									@php
										$socio = optional($solicitud->tercero)->socio;
										$pagaduria = empty($socio) ? '' : $socio->pagaduria->nombre;
									@endphp
									{!! Form::text('pagaduria', $pagaduria, ['class' => 'form-control', 'placeholder' => 'Pagaduría', 'autocomplete' => 'off', 'readonly']) !!}
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">
										Periodicidad pagaduría
									</label>
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

						<div class="row form-horizontal">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('valor_credito')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('valor_credito'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Valor solicitud
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('valor_credito', null, ['class' => 'form-control text-right', 'autofocus', 'data-maskMoney', 'readonly']) !!}
									</div>
									@if ($errors->has('valor_credito'))
										<span class="help-block">{{ $errors->first('valor_credito') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('tasa')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('tasa'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tasa M.V.
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon">%</span>
										{!! Form::text('tasa', number_format($solicitud->tasa, 2), ['class' => 'form-control', 'readonly']) !!}
									</div>
									@if ($errors->has('tasa'))
										<span class="help-block">{{ $errors->first('tasa') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<a href="{{ route('solicitudCreditoConsolidacion', $solicitud) }}" class="btn btn-primary">Recoger obligaciones vigentes</a>
							</div>
						</div>

						<div></div>
						<br>
						<hr>
						<div class="row form-horizontal">
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('plazo')?'has-error':'') }}">
									<label class="col-md-7 control-label">
										@if ($errors->has('plazo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número de cuotas
									</label>
									<div class="col-md-5">
										{!! Form::text('plazo', null, ['class' => 'form-control', 'autofocus', 'min' => '1', 'step' => '1']) !!}
										@if ($errors->has('plazo'))
											<span class="help-block">{{ $errors->first('plazo') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('forma_pago')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('forma_pago'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Forma de pago
									</label>
									<div class="col-md-6">
										{!! Form::select('forma_pago', ['NOMINA' => 'Nómina', 'PRIMA' => 'Prima', 'CAJA' => 'Caja'], null, ['class' => 'form-control select2']) !!}
										@if ($errors->has('forma_pago'))
											<span class="help-block">{{ $errors->first('forma_pago') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('periodicidad')?'has-error':'') }}">
									<label class="col-md-4 control-label">
										@if ($errors->has('periodicidad'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Periodicidad de pago
									</label>
									<div class="col-md-8">
										{!! Form::select('periodicidad', $periodicidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una periodicidad']) !!}
										@if ($errors->has('periodicidad'))
											<span class="help-block">{{ $errors->first('periodicidad') }}</span>
										@endif
									</div>
								</div>
							</div>

						</div>

						<div class="row form-horizontal">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fecha_primer_pago')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('fecha_primer_pago'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha primer pago
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::select('fecha_primer_pago', $programaciones, null, ['class' => 'form-control pull-right select2']) !!}
									</div>
									@if ($errors->has('fecha_primer_pago'))
										<span class="help-block">{{ $errors->first('fecha_primer_pago') }}</span>
									@endif
								</div>
							</div>

							@if($solicitud->modalidadCredito->tipo_cuota == 'CAPITAL')
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fecha_primer_pago_intereses')?'has-error':'') }}">
									<label class="col-md-7 control-label">
										@if ($errors->has('fecha_primer_pago_intereses'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha primer pago intereses
									</label>
									<div class="col-md-5 input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::select('fecha_primer_pago_intereses', $programaciones, null, ['class' => 'form-control pull-right select2']) !!}
									</div>
									@if ($errors->has('fecha_primer_pago_intereses'))
										<span class="help-block">{{ $errors->first('fecha_primer_pago_intereses') }}</span>
									@endif
								</div>
							</div>
							@endif

							@if($solicitud->modalidadCredito->acepta_cuotas_extraordinarias)
							<div class="col-md-4">
								<a href="{{ route('solicitudCredito.cuotasExtraordinarias', $solicitud->id) }}" class="btn btn-primary">Agregar cuotas extraordinarias</a>
							</div>
							@endif					

						</div>

						<div class="row form-horizontal">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fecha_aprobacion')?'has-error':'') }}">
									<label class="col-md-6 control-label">
										@if ($errors->has('fecha_aprobacion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha aprobación
									</label>
									<div class="col-md-6 input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::text('fecha_aprobacion', $solicitud->fecha_solicitud, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_aprobacion'))
										<span class="help-block">{{ $errors->first('fecha_aprobacion') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-8">
								{!! Form::submit('Aprobar solicitud', ['class' => 'btn bg-olive']) !!}
								<a href="{{ url('solicitudCredito') }}" class="btn btn-danger pull-right">Volver</a>
							</div>
						</div>

						@if($solicitud->amortizaciones->count())
						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('observaciones')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('observaciones'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Observaciones
									</label>
									{!! Form::textarea('observaciones', null, ['class' => 'form-control', 'placeholder' => 'Observaciones', 'style' => 'height:100px;']) !!}
									@if ($errors->has('observaciones'))
										<span class="help-block">{{ $errors->first('observaciones') }}</span>
									@endif
								</div>
							</div>
						</div>
						@endif

						<hr>
						<div class="row form-horizontal">
							<div class="col-md-12">
								<a id="verCondiciones" class="btn btn-info btn-xs">Ver condiciones</a>
								<a id="verAmortizacion" class="btn btn-info btn-xs">Ocultar amortización</a>
								<a id="verDocumentacion" class="btn btn-info btn-xs">Actualizar documentación</a>
								<a href="{{ route('solicitudCreditoGarantias', $solicitud->id) }}" class="btn btn-info btn-xs">Garantías</a>
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
									<table class="table table-hover">
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
														<td>
															{{ number_format($condicion->valor_parametro, 0) }}
														</td>
														<td>{{ number_format($condicion->valor_solicitud, 0) }}</td>
														<td>
															<?php
																$cumple = $condicion->cumple_parametro;
																$aprobado = false;
																if(!$cumple)
																{
																	$cumple = empty($condicion->es_aprobada) ? false : true;
																	$aprobado = $cumple;
																}
															?>
															<span class="label label-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																<?php
																	$aprobar = false;
																	if($cumple)
																	{
																		if($aprobado)
																		{
																			$aprobar = true;
																			echo "aprobado";
																		}
																		else
																		{
																			echo "Sí";
																		}
																	}
																	else
																	{
																		$aprobar = true;
																		echo "No";
																	}
																?>
															</span>
														</td>
														<td>
															@if($aprobar)
																<a data-id="{{ $condicion->id }}" class="btn btn-{{ $aprobado ? 'danger' : 'success' }} btn-xs" onclick="alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar' }}</a>
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
																if(!$cumple)
																{
																	$cumple = empty($condicion->es_aprobada) ? false : true;
																	$aprobado = $cumple;
																}
															?>
															<span class="label label-{{$condicion->id }} label-{{ $cumple ? 'success' : 'danger' }}">
																<?php
																	$aprobar = false;
																	if($cumple)
																	{
																		if($aprobado)
																		{
																			$aprobar = true;
																			echo "aprobado";
																		}
																		else
																		{
																			echo "Sí";
																		}
																	}
																	else
																	{
																		$aprobar = true;
																		echo "No";
																	}
																?>
															</span>
														</td>
														<td>
															@if($aprobar)
																@if($condicion->condicion != 'Documentación')
																	<a data-id="{{ $condicion->id }}" class="btn btn-{{ $aprobado ? 'danger' : 'success' }} btn-xs" onclick="alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar' }}</a>
																@else
																	<a data-id="{{ $condicion->id }}" class="btn btn-{{ $aprobado ? 'danger' : 'success'}} btn-xs" onclick="javascript:alternarCondicion(this);">{{ $aprobado ? 'Desaprobar' : 'Aprobar'}}</a>
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
									<table id="tablaAmortizacion" class="table table-hover">
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
									<table class="table table-hover">
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
														<span class="label label-documento-{{ $documento->id }} label-{{ $cumple ? 'success' : 'danger' }}">{{ $cumple ? 'Sí' : 'No' }}</span>
													</td>
													<td>
														<a data-id="{{ $documento->id }}" class="btn btn-{{ $cumple ? 'danger' : 'success' }} btn-xs" onclick="javascript:alternarDocumento(this)">{{ $cumple ? 'No cumple' : 'Cumple' }}</a>
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
			@endif
		});

		$(".select2").select2();

		$("#verAmortizacion").click(function(e){
			if($("#amortizacion").data("visible")){
				$(this).text('Ver amortización');
				$("#amortizacion").data("visible", false);
				$("#amortizacion").hide();
			}
			else
			{
				$(this).text('Ocultar amortización');
				$("#amortizacion").data("visible", true);
				$("#amortizacion").show();
			}
		});

		$("#verCondiciones").click(function(e){
			if($("#condiciones").data("visible")){
				$(this).text('Ver condiciones');
				$("#condiciones").data("visible", false);
				$("#condiciones").hide();
			}
			else
			{
				$(this).text('Ocultar condiciones');
				$("#condiciones").data("visible", true);
				$("#condiciones").show();
			}
		});

		$("#verDocumentacion").click(function(e){
			if($("#documentacion").data("visible")){
				$(this).text('Actualizar documentacion');
				$("#documentacion").data("visible", false);
				$("#documentacion").hide();
			}
			else
			{
				$(this).text('Ocultar documentacion');
				$("#documentacion").data("visible", true);
				$("#documentacion").show();
			}
		});

		$("#calcularAmortizacion").click(function(e){
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
			if($solicitud->modalidadCredito->es_tasa_condicionada)
			{
				$condicion = $solicitud->modalidadCredito->condicionesModalidad()->whereTipoCondicion('TASA')->first();

				if(!empty($condicion))
				{
					if($condicion->condicionado_por == 'MONTO')
					{
						?>
						$("input[name='valor_credito']").on("keyup", function(e){
							$valor = $(this).maskMoney('cleanvalue');
							getTasaCondicionada($valor);
						});
						<?php
					}
					elseif($condicion->condicionado_por == 'PLAZO')
					{
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
	function alternarCondicion(obj)
	{
		var $obj = $(obj);
		$.ajax({
			url: '{{ route('solicitudCreditoAlternarCondicion', $solicitud->id) }}',
			type: 'GET',
			data: 'condicion=' + $obj.data('id')
		}).done(function(data){
			if(data.estado){
				$obj.removeClass("btn-success");
				$obj.addClass("btn-danger");
				$obj.text("Desaprobar");
				$(".label-" + $obj.data('id')).removeClass("label-danger");
				$(".label-" + $obj.data('id')).addClass("label-success");
				$(".label-" + $obj.data('id')).text("Aprobado");
			}
			else
			{
				$obj.removeClass("btn-danger");
				$obj.addClass("btn-success");
				$obj.text("Aprobar");
				$(".label-" + $obj.data('id')).removeClass("label-success");
				$(".label-" + $obj.data('id')).addClass("label-danger");
				$(".label-" + $obj.data('id')).text("No");
			}
		}).fail(function(data){});
	}

	function alternarDocumento(obj)
	{
		var $obj = $(obj);
		$.ajax({
			url: '{{ route('solicitudCreditoAlternarDocumento', $solicitud->id) }}',
			type: 'GET',
			data: 'documento=' + $obj.data('id')
		}).done(function(data){
			if(data.estado){
				$obj.removeClass("btn-success");
				$obj.addClass("btn-danger");
				$obj.text("No cumple");
				$(".label-documento-" + $obj.data('id')).removeClass("label-danger");
				$(".label-documento-" + $obj.data('id')).addClass("label-success");
				$(".label-documento-" + $obj.data('id')).text("Sí");
			}
			else
			{
				$obj.removeClass("btn-danger");
				$obj.addClass("btn-success");
				$obj.text("Cumple");
				$(".label-documento-" + $obj.data('id')).removeClass("label-success");
				$(".label-documento-" + $obj.data('id')).addClass("label-danger");
				$(".label-documento-" + $obj.data('id')).text("No");
			}
		}).fail(function(data){});
	}
</script>
@endpush

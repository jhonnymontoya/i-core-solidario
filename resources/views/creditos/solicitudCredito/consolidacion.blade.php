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

	<section class="content">
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Consolidar créditos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
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
						<div class="col-md-4">
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
					</div>
					<div class="row">
						<div class="col-md-12 text-right">
							<?php
								switch($solicitud->estado_solicitud) {
									case 'BORRADOR':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoEdit', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									case 'RADICADO':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									case 'APROBADO':
										?>
										<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
										<?php
										break;
									
									default:
										break;
								}
							?>
						</div>
					</div>
					<hr>
					<ul class="nav nav-pills mb-3" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#creditosVigentes" role="tab" aria-selected="true">Créditos vigentes</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#resumenLiquidacion" role="tab" aria-selected="false">Resumen liquidación</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade show active" id="creditosVigentes">
							<br>
							@if($creditosVigentes->count())
								<div class="row">
									<div class="col-md-12">
										<h4>Créditos vigentes</h4>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12 table-responsive">
										<table class="table table-striped table-hover">
											<thead>
												<tr>
													<th>Número obligación</th>
													<th class="text-center">Valor inicial</th>
													<th>Fecha desembolso</th>
													<th class="text-center">Valor cuota</th>
													<th class="text-center">Saldo capital</th>
													<th class="text-center">Saldo Intereses</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@foreach ($creditosVigentes as $creditoVigente)
													@php
														$capital = $creditoVigente->saldoObligacion('01/01/3000');
														$intereses = $creditoVigente->saldoInteresObligacion($solicitud->fecha_solicitud);
														$consolidado = empty($creditoVigente->obligacionesQueConsolidan()->creditoQueConsolida($solicitud->id)->first()) ? false : true;
														$recaudo = optional($creditoVigente->proximoRecaudo())->capital_generado;
														$recaudo = empty($recaudo) ? 0 : $recaudo;
													@endphp
													<tr>
														<td>{{ $creditoVigente->numero_obligacion }}</td>
														<td class="text-right">${{ number_format($creditoVigente->valor_credito) }}</td>
														<td>{{ $creditoVigente->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($creditoVigente->valor_cuota) }}</td>
														<td class="text-right">${{ number_format($capital) }}</td>
														<td class="text-right">${{ number_format($intereses) }}</td>
														<td>
															<a href="#" class="btn btn-sm btn-outline-secondary {{ $consolidado ? 'disabled' : '' }}" title="Cancelación total del saldo a la fecha" data-toggle="modal" data-target="#confirmacion" data-opcion="saldoTotal" data-credito="{{ $creditoVigente->id }}" data-capital="{{ $capital }}" data-intereses="{{ $intereses }}">Saldo total</a>
															<a href="#" class="btn btn-sm btn-outline-secondary {{ $consolidado ? 'disabled' : '' }}" title="Cancelación total teniendo en cuenta el recaudo en proceso" data-toggle="modal" data-target="#confirmacion" data-opcion="incluidoRecaudo" data-credito="{{ $creditoVigente->id }}" data-capital="{{ $capital }}" data-intereses="{{ $intereses }}" data-recaudo="{{ $recaudo }}">Incluido recaudo</a>
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
							@else
								<div class="row">
									<div class="col-md-12">
										<h4>No hay créditos disponibles para mostrar</h4>
									</div>
								</div>
							@endif
						</div>
						<div role="tabpanel" class="tab-pane fade" id="resumenLiquidacion">
							<br>
							<br>
							@if($creditosRecogidos->count())
								<div class="row">
									<div class="col-md-12">
										<h4>Créditos recogidos</h4>
									</div>
								</div>
								<br>
								<div class="row">
									<div class="col-md-12 table-responsive">
										<table class="table table-striped table-hover">
											<thead>
												<tr>
													<th>Número obligación</th>
													<th>Fecha inicio crédito</th>
													<th class="text-center">Capital recogido</th>
													<th class="text-center">Interes por pagar</th>
													<th class="text-center">Total consolidación obligación</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@php
													$total = 0;
												@endphp
												@foreach ($creditosRecogidos as $creditoRecogido)
													@php
														$total += $creditoRecogido->total;
													@endphp
													<tr>
														<td>{{ $creditoRecogido->creditoConsolidado->numero_obligacion }}</td>
														<td>{{ $creditoRecogido->creditoConsolidado->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($creditoRecogido->pago_capital) }}</td>
														<td class="text-right">${{ number_format($creditoRecogido->pago_intereses) }}</td>
														<td class="text-right">${{ number_format($creditoRecogido->total) }}</td>
														<td>
															{!! Form::open(['route' => ['solicitudCreditoDeleteConsolidacion', $solicitud], 'method' => 'delete', 'role' => 'form']) !!}
															{!! Form::hidden('credito', $creditoRecogido->id) !!}
															{!! Form::submit('Eliminar', ['class' => 'btn btn-outline-danger btn-sm']) !!}
															{!! Form::close() !!}
														</td>
													</tr>
												@endforeach
											</tbody>
											<tfoot>
												<tr>
													<th colspan="3"></th>
													<td>Total recogido:</td>
													<td class="text-right">${{ number_format($total) }}</td>
												</tr>
												<tr>
													<th colspan="3"></th>
													<th>Exedente para desembolso:</th>
													<th class="text-right">${{ number_format($solicitud->valor_credito - $total) }}</th>
													<th><a href="#" class="btn btn-outline-success btn-sm" data-toggle="modal" data-target="#desembolso">Modificar</a></th>
												</tr>
											</tfoot>
										</table>
									</div>
								</div>
							@else
								<div class="row">
									<div class="col-md-12">
										<h4>No hay créditos recogidos para mostrar</h4>
									</div>
								</div>
							@endif
						</div>
					</div>
				</div>

				<div class="card-footer text-right">
					<?php
						switch($solicitud->estado_solicitud) {
							case 'RADICADO':
								?>
								<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoAprobar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;
							case 'APROBADO':
								?>
								<a class="btn btn-outline-danger pull-right" href="{{ route('solicitudCreditoDesembolsar', $solicitud) }}" title="Volver a solicitud">Volver a solicitud</a>
								<?php
								break;							
							default:
								break;
						}
					?>
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="titulo">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="titulo"><strong>tipoCredito</strong></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			{!! Form::open(['route' => ['solicitudCreditoPutConsolidacion', $solicitud], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			{!! Form::hidden('credito', 0) !!}
			{!! Form::hidden('tipo_consolidacion', '') !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<h4 class="descripcion"></h4>
					</div>
				</div>
				<hr>
				<h4>Liquidación consolidación</h4>
				<br>
				<div class="row">
					<div class="col-md-12">
						<dl class="dl-horizontal">
							<dt>Capital a recoger</dt>
							<dd class="capitalRecoger"></dd>
						</dl>
						<dl class="dl-horizontal">
							<dt>Pago intereses</dt>
							<dd class="pagoIntereses"></dd>
						</dl>
						<dl class="dl-horizontal">
							<dt>Total consolidado</dt>
							<dd class="total"></dd>
						</dl>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! Form::submit('Recoger', ['class' => 'btn btn-outline-success']) !!}
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
<div class="modal fade" id="desembolso" tabindex="-1" role="dialog" aria-labelledby="titulo">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="titulo"><strong>Nuevo valor a desembolsar</strong></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			{!! Form::open(['route' => ['solicitudCredito.put.consolidacion.modificar', $solicitud], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							@php
								$valid = $errors->has('valorDesembolso') ? 'is-invalid' : '';
							@endphp
							<label class="control-label">Ingresar nuevo valor</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">$</span>
								</div>
								{!! Form::text('valorDesembolso', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Ingresar nuevo valor', 'autofocus', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
								@if ($errors->has('valorDesembolso'))
									<div class="invalid-feedback">{{ $errors->first('valorDesembolso') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer text-right">
				{!! Form::submit('Modificar', ['class' => 'btn btn-outline-success']) !!}
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
			</div>
			{!! Form::close() !!}
		</div>
	</div>
</div>
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(window).load(function(){
			$("input[name='valor_credito']").maskMoney('mask');
		});
		$('#confirmacion').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget);
			var opcion = button.data('opcion');
			var credito = button.data('credito');
			var modal = $(this);
			var titulo = "";
			var tipo = "";
			var descripcion = "";
			var capital = button.data('capital');
			var intereses = button.data('intereses');
			switch(opcion) {
				case 'saldoTotal':
					titulo = 'Saldo total';
					descripcion = "Cancelación total del saldo a la fecha";
					tipo = "SALDOTOTAL";
					break;
				case 'incluidoRecaudo':
					titulo = 'Incluido recaudo';
					descripcion = "Cancelación total teniendo en cuenta el recaudo en proceso";
					tipo = "INCLUIDORECAUDO";
					capital -= button.data('recaudo');
					break;
				case 'parcial':
					titulo = 'Parcial';
					descripcion = "Indicar valor parcial para el crédito";
					tipo = "PARCIAL";
					break;
			}
			modal.find('.modal-title strong').text(titulo);
			modal.find('.descripcion').text(descripcion);
			modal.find('.capitalRecoger').text("$" + $().formatoMoneda(capital));
			modal.find('.pagoIntereses').text("$" + $().formatoMoneda(intereses));
			modal.find('.total').text("$" + $().formatoMoneda(capital + intereses));
			modal.find('input[name="credito"]').val(credito);
			modal.find('input[name="tipo_consolidacion"]').val(tipo);
		})
	});
</script>
@endpush

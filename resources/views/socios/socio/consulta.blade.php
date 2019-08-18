@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Socios
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Socios</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		@php
			$fecha = Request::has('fecha') ? Request::get('fecha') : date('d/m/Y');
			$fecha = empty($fecha) ? date('d/m/Y') : $fecha;
		@endphp
		<div class="card card-primary">
			<div class="card-header with-border">
				<h3 class="card-title">Consulta</h3>
				@if ($socio)
					<a class="btn btn-xs btn-primary pull-right" href="{{ route('reportesReporte', 6) }}?numeroIdentificacion={{ $socio->tercero->numero_identificacion }}&fechaConsulta={{ implode('/', array_reverse(explode('/', $fecha))) }}" target="_blank"><i class="fa fa-print"></i> Imprimir</a>
				@endif
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only('socio', 'fecha'), ['url' => 'socio/consulta', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('socio')?'has-error':'') }}">
							<label class="col-sm-4 control-label">
								@if ($errors->has('socio'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione socio
							</label>
							<div class="col-sm-8">
								{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
								@if ($errors->has('socio'))
									<span class="help-block">{{ $errors->first('socio') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group {{ ($errors->has('fecha')?'has-error':'') }}">
							<label class="col-sm-4 control-label">
								@if ($errors->has('fecha'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha consulta
							</label>
							<div class="col-sm-8">
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									{!! Form::text('fecha', $fecha, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('fecha'))
									<span class="help-block">{{ $errors->first('fecha') }}</span>
								@endif
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
					<h4>Datos básicos</h4>
					<br>
					<div class="row">
						<div class="col-md-9 col-md-offset-1">
							<div class="row">
								<div class="col-md-2"><strong>Nombre:</strong></div>
								<div class="col-md-5">{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</div>

								<div class="col-md-2"><strong>Pagaduría:</strong></div>
								<div class="col-md-3">{{ empty($socio->pagaduria) ? '' : $socio->pagaduria->nombre }}</div>
							</div>

							<?php
								$antiguedad = 'No aplica';
								if($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD')
								{
									$antiguedad = $socio->fecha_antiguedad != null? $socio->fecha_antiguedad->diffForHumans() : 'Sin antigüedad';
								}
							?>
							<div class="row">
								<div class="col-md-2"><strong>Antigüedad:</strong></div>
								<div class="col-md-5">{{ $antiguedad }}</div>

								<div class="col-md-2"><strong>Fecha nacimiento:</strong></div>
								<div class="col-md-3">{{ empty($socio->tercero->fecha_nacimiento) ? '' : $socio->tercero->fecha_nacimiento }}</div>
							</div>

							<?php
								$label = "default";
								switch ($socio->estado) {
									case 'ACTIVO':
										$label = 'green';
										break;
									case 'NOVEDAD':
										$label = 'orange';
										break;
									case 'RETIRO':
										$label = 'maroon';
										break;
									case 'LIQUIDADO':
										$label = 'red';
										break;
									case 'PROCESO':
										$label = 'light-blue';
										break;
								}
							?>
							<div class="row">
								<div class="col-md-2"><strong>Fecha afiliación:</strong></div>
								<div class="col-md-5">{{ $socio->fecha_afiliacion }}</div>

								<div class="col-md-2"><strong>Estado:</strong></div>
								<div class="col-md-3"><span class="label bg-{{ $label }}">{{ $socio->estado }}</span></div>
							</div>

							<?php
								$contacto = $socio->tercero->getContacto();
							?>
							<div class="row">
								<div class="col-md-2"><strong>Email:</strong></div>
								<div class="col-md-5">{{ empty($contacto) ? 'Sin información' : $contacto->email }}</div>

								<div class="col-md-2"><strong>Ingreso empresa:</strong></div>
								<div class="col-md-3">{{ $socio->fecha_ingreso }}</div>
							</div>

							<div class="row">
								<div class="col-md-2"><strong>Teléfono:</strong></div>
								<div class="col-md-5">{{ empty($contacto) ? 'Sin información' : ($contacto->movil ?: $contacto->telefono) }}</div>

								<div class="col-md-2"><strong>Sueldo:</strong></div>
								<div class="col-md-3">${{ number_format($socio->sueldo_mes) }}</div>
							</div>

							<div class="row">
								<?php
									$label = "bg-";
									$porcentaje = $socio->endeudamiento();
									if($porcentaje <= $porcentajeMaximoEndeudamientoPermitido) {
										$label .= 'green';
									}
									else {
										$label .= 'red';
									}
								?>
								<div class="col-md-2"><strong>Endeudamiento:</strong></div>
								<div class="col-md-5"><span class="label {{ $label }}">{{ number_format($porcentaje, 2) }}%</span></div>

								@if ($socio->estado == 'RETIRO' || $socio->estado == 'LIQUIDADO' )
									<div class="col-md-2"><strong>Fecha retiro:</strong></div>
									<div class="col-md-3">{{ $socio->fecha_retiro }}</div>
								@endif
							</div>

							<br><br><br>
							<div class="row">
								<div class="col-md-6">
									@php
										$saldo = $socio->tercero->cupoDisponible($fecha);
									@endphp
									<h4 class="text-{{ $saldo <= 0 ? 'danger' : 'primary' }}"><strong>Cupo disponible: ${{ number_format($saldo) }}</strong></h4>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-6"><strong>Último periodo aplicado:</strong></div>
										<div class="col-md-6">
											<?php
												if(!is_null($recaudoAplicado)) {
													?>
													<span class="label label-success">{{ $recaudoAplicado->numero_periodo }}</span> {{ $recaudoAplicado->fecha_recaudo }}
													<?php
												}
											?>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="widget-user-image">
								<img class="profile-user-img img-responsive img-circle" src="{{ asset('storage/asociados/' . (empty($socio->avatar)?'avatar-160x160.png':$socio->avatar) ) }}" alt="{{ $socio->nombre_corto }}" />
							</div>
						</div>
					</div>

					<br>
					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation" class="active">
							<a href="#estadoCuenta" aria-controls="estadoCuenta" role="tab" data-toggle="tab">Estado cuenta</a>
						</li>
						<li role="presentation">
							<a href="#ahorros" aria-controls="ahorros" role="tab" data-toggle="tab">Ahorros</a>
						</li>
						<li role="presentation">
							<a href="#creditos" aria-controls="creditos" role="tab" data-toggle="tab">Créditos</a>
						</li>
						<li role="presentation">
							<a href="#recaudoNomina" aria-controls="recaudoNomina" role="tab" data-toggle="tab">Recaudo nómina</a>
						</li>
						<li role="presentation">
							<a href="#simulador" aria-controls="simulador" role="tab" data-toggle="tab">Simulador</a>
						</li>
						<li role="presentation">
							<a href="#documentacion" aria-controls="documentacion" role="tab" data-toggle="tab">Documentación</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active" id="estadoCuenta">
							<br>
							<?php
								$fechaConsulta = \Carbon\Carbon::createFromFormat('d/m/Y', $fecha);
								$creditos = $socio->tercero
									->solicitudesCreditos()
									->where('fecha_desembolso', '<=', $fechaConsulta)
									->estado('DESEMBOLSADO')
									->get();

								$totalAhorros = 0;
								$totalCreditos = 0;
								$netosSaldos = 0;
								foreach($ahorros as $ahorro)$totalAhorros += $ahorro->saldo;
								foreach($creditos as $credito)$totalCreditos += $credito->saldoObligacion($fecha);
								$netosSaldos = $totalAhorros - $totalCreditos;
							?>
							<div class="row" style="margin-left: 30px; margin-right: 30px;">
								<div class="col-md-3">
									<div class="row">
										<div class="col-md-8"><label>Total ahorros</label></div>
										<div class="col-md-4">${{ number_format($totalAhorros, 0) }}</div>
									</div>
								</div>
								<div class="col-md-5">
									<div class="row">
										<div class="col-md-8"><label>Total capital de crédito</label></div>
										<div class="col-md-4">${{ number_format($totalCreditos, 0) }}</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="row">
										<div class="col-md-6"><label>Netos saldos</label></div>
										<div class="col-md-6">${{ number_format($netosSaldos, 0) }}</div>
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Ahorros
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($ahorros->count())
										<table class="table table-hover">
											@php
												$saldo = 0;
												$intereses = 0;
												$cuotaMensual = 0;
											@endphp
											<thead>
												<tr>
													<th>Modalidad</th>
													<th class="text-center">Saldo</th>
													<th class="text-center">Intereses</th>
													<th class="text-center">Cuota mensual</th>
												</tr>
											</thead>
											<tbody>
												@foreach($ahorros as $ahorro)
													@php
														$tmp = \App\Helpers\ConversionHelper::conversionValorPeriodicidad($ahorro->cuota, $ahorro->periodicidad, 'MENSUAL');
														$saldo += $ahorro->saldo;
														$intereses += $ahorro->intereses;
														$cuotaMensual += $tmp;
													@endphp
													<tr>
														<td>{{ $ahorro->codigo }} - {{ $ahorro->nombre }}</td>
														<td class="text-right">${{ number_format($ahorro->saldo, 0) }}</td>
														<td class="text-right">${{ number_format($ahorro->intereses, 0) }}</td>
														<td class="text-right">${{ number_format($tmp, 0) }}</td>
													</tr>
												@endforeach
											</tbody>
											<tfoot>
												<tr>
													<th class="text-right">Totales:</th>
													<th class="text-right">${{ number_format($saldo) }}</th>
													<th class="text-right">${{ number_format($intereses) }}</th>
													<th class="text-right">${{ number_format($cuotaMensual) }}</th>
												</tr>
											</tfoot>
										</table>
									@else
										<br>
										<label>No hay registros de ahorros para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Créditos
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($creditos->count())
										@php
											$valorCapital = 0;
											$saldoIntereses = 0;
											$valorTotal = 0;
											$totalValorCuota = 0;
										@endphp
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Obligación</th>
													<th>Modalidad</th>
													<th>Fecha inicio</th>
													<th class="text-center">Valor inicial</th>
													<th class="text-center">Tasa M.V.</th>
													<th class="text-center">Valor cuota</th>
													<th class="text-center">Saldo capital</th>
													<th class="text-center">Saldo intereses</th>
													<th class="text-center">Saldo total</th>
												</tr>
											</thead>
											<tbody>
												@foreach($creditos as $credito)
													@php
														$tmpSaldoCapital = $credito->saldoObligacion($fecha);
														$tmpSaldoIntereses = $credito->saldoInteresObligacion($fecha);
														$tmpSaldoTotal = $credito->saldoObligacion($fecha);
														$valorCapital += $tmpSaldoCapital;
														$saldoIntereses += $tmpSaldoIntereses;
														$valorTotal += $tmpSaldoCapital + $tmpSaldoIntereses;

														$valorCuota = 0;
														$valorCuota = $credito->valor_cuota;
														$totalValorCuota += $valorCuota;
													@endphp
													<tr>
														<td>{{ $credito->numero_obligacion }}</td>
														<td>{{ $credito->modalidadCredito->nombre }}</td>
														<td>{{ $credito->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
														<td class="text-right">{{ number_format($credito->tasa, 3) }}%</td>
														<td class="text-right">${{ number_format($valorCuota, 0) }}</td>
														<td class="text-right">${{ number_format($tmpSaldoCapital, 0) }}</td>
														<td class="text-right">${{ number_format($tmpSaldoIntereses, 0) }}</td>
														<td class="text-right">${{ number_format($tmpSaldoCapital + $tmpSaldoIntereses, 0) }}</td>
													</tr>
												@endforeach
											</tbody>
											<tfoot>
												<tr>
													<th colspan="5" class="text-right">Totales:</th>
													<th class="text-right">${{ number_format($totalValorCuota) }}</th>
													<th class="text-right">${{ number_format($valorCapital) }}</th>
													<th class="text-right">${{ number_format($saldoIntereses) }}</th>
													<th class="text-right">${{ number_format($valorTotal) }}</th>
												</tr>
											</tfoot>
										</table>
									@else
										<br>
										<label>No hay registros de créditos para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="ahorros">
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Ahorros generales
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($ahorros->where('tipo_ahorro', '<>', 'PROGRAMADO')->count())
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Modalidad</th>
													<th class="text-center">Saldo</th>
													<th class="text-center">Cuota</th>
													<th class="text-right">Periodicidad</th>
													<th class="text-right">Tasa E.A.</th>
												</tr>
											</thead>
											<tbody>
												@foreach($ahorros->where('tipo_ahorro', '<>', 'PROGRAMADO') as $ahorro)
													<tr>
														<td>
															<a href="{{ route('socioConsultaAhorros', $ahorro->modalidad_ahorro_id) . '?fecha=' . $fecha . '&socio=' . $socio->id }}">
																{{ $ahorro->codigo }} - {{ $ahorro->nombre }}
															</a>
														</td>
														<td class="text-right">${{ number_format($ahorro->saldo, 0) }}</td>
														<td class="text-right">${{ number_format($ahorro->cuota, 0) }}</td>
														<td class="text-right">{{ $ahorro->periodicidad }}</td>
														<td class="text-right">{{ number_format($ahorro->tasa, 2) }}%</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<br>
										<label>No hay registros de ahorros para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Ahorros programado
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($ahorros->where('tipo_ahorro', 'PROGRAMADO')->count())
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Modalidad</th>
													<th class="text-center">Saldo</th>
													<th class="text-center">Cuota</th>
													<th class="text-right">Periodicidad</th>
													<th class="text-right">Vencimiento</th>
													<th class="text-right">Tasa E.A.</th>
													<th></th>
												</tr>
											</thead>
											<tbody>
												@foreach($ahorros->where('tipo_ahorro', 'PROGRAMADO') as $ahorro)
													<tr>
														<td>
															<a href="{{ route('socioConsultaAhorros', $ahorro->modalidad_ahorro_id) . '?fecha=' . $fecha . '&socio=' . $socio->id }}">
																{{ $ahorro->codigo }} - {{ $ahorro->nombre }}
															</a>
														</td>
														<td class="text-right">${{ number_format($ahorro->saldo, 0) }}</td>
														<td class="text-right">${{ number_format($ahorro->cuota, 0) }}</td>
														<td class="text-right">{{ $ahorro->periodicidad }}</td>
														<td class="text-right">{{ $ahorro->vencimiento }}</td>
														<td class="text-right">{{ number_format($ahorro->tasa, 2) }}%</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<br>
										<label>No hay registros de ahorros para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Servicio de deposito de ahorro a término SDAT
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 table-responsive">
									@if ($sdats->count())
										<table class="table table-striped">
											<thead>
												<tr>
													<th>Número</th>
													<th>Tipo</th>
													<th class="text-center">Valor</th>
													<th>Fecha constitución</th>
													<th class="text-center">Plazo días</th>
													<th>Fecha Vencimiento</th>
													<th>Tasa E.A.</th>
													<th class="text-center">Saldo</th>
													<th class="text-center">Intereses causados</th>
													<th>Estado</th>
												</tr>
											</thead>
											<tbody>
												<?php
													foreach($sdats as $sdat) {
														$label = "default";
														switch ($sdat->estado) {
															case 'CONSTITUIDO':
																$label = "success";
																break;
															case 'RENOVADO':
																$label = "success";
																break;
															case 'PRORROGADO':
																$label = "success";
																break;
															default:
																$label = "default";
																continue 2;
																break;
														}
														?>
														<tr>
															<td>{{ $sdat->id }}</td>
															<td>{{ $sdat->codigo }}</td>
															<td class="text-right">{{ $sdat->valor }}</td>
															<td>{{ $sdat->fecha_constitucion }}</td>
															<td class="text-right">{{ $sdat->plazo }}</td>
															<td>{{ $sdat->fecha_vencimiento }}</td>
															<td class="text-right">{{ $sdat->tasa }}</td>
															<td class="text-right">{{ $sdat->saldo }}</td>
															<td class="text-right">{{ $sdat->rendimientos }}</td>
															<td><span class="label label-{{ $label }}">{{ $sdat->estado }}</span></td>
														</tr>
														<?php
													}
												?>
											</tbody>
										</table>
									@else
										<label>No hay registros de SDAT para mostrar</label>
									@endif
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="creditos">
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Créditos activos
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($creditos->count())
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Obligación</th>
													<th>Modalidad</th>
													<th>Fecha inicio</th>
													<th class="text-center">Valor inicial</th>
													<th class="text-center">Tasa M.V.</th>
													<th class="text-center">Valor cuota</th>
													<th class="text-center">Saldo capital</th>
													<th class="text-center">Saldo intereses</th>
													<th class="text-center">Saldo total</th>
												</tr>
											</thead>
											<tbody>
												@foreach($creditos as $credito)
													<tr>
														<td>
															<a href="{{ route('socioConsultaCreditos', $credito->id) . '?fecha=' . $fecha . '&socio=' . $socio->id }}">
																{{ $credito->numero_obligacion }}
															</a>
														</td>
														<td>{{ $credito->modalidadCredito->nombre }}</td>
														<td>{{ $credito->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
														<td class="text-right">{{ number_format($credito->tasa, 3) }}%</td>
														<td class="text-right">
															<?php
																$valorCuota = 0;
																$valorCuota = $credito->valor_cuota;
															?>
															${{ number_format($valorCuota, 0) }}
														</td>
														<td class="text-right">${{ number_format($credito->saldoObligacion($fecha), 0) }}</td>
														<td class="text-right">${{ number_format($credito->saldoInteresObligacion($fecha), 0) }}</td>
														<td class="text-right">${{ number_format($credito->saldoObligacion($fecha) + $credito->saldoInteresObligacion($fecha), 0) }}</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<br>
										<label>No hay registros de créditos para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Codeudas
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									@php
										$codeudas = $socio->tercero->codeudas()->whereHas('solicitudCredito', function($q){
											return $q->whereEstadoSolicitud('DESEMBOLSADO');
										})->get();
									@endphp
									@if($codeudas->count())
										<table class="table">
											<thead>
												<th>Deudor</th>
												<th>Número obligación</th>
												<th>Fecha inicio</th>
												<th class="text-center">Valor inicial</th>
												<th class="text-center">Tasa M.V.</th>
												<th class="text-center">Saldo capital</th>
												<th class="text-center">Calificación</th>
											</thead>
											<tbody>
												@foreach ($codeudas as $codeuda)
													<tr>
														<td>{{ $codeuda->solicitudCredito->tercero->tipoIdentificacion->codigo }} {{ $codeuda->solicitudCredito->tercero->numero_identificacion }} - {{ $codeuda->solicitudCredito->tercero->nombre_corto }}</td>
														<td>{{ $codeuda->solicitudCredito->numero_obligacion }}</td>
														<td>{{ $codeuda->solicitudCredito->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($codeuda->solicitudCredito->valor_credito) }}</td>
														<td class="text-right">{{ number_format($codeuda->solicitudCredito->tasa, 3) }}%</td>
														<td class="text-right">${{ number_format($codeuda->solicitudCredito->saldoObligacion($fecha)) }}</td>
														<td class="text-center">{{ $codeuda->solicitudCredito->calificacion_obligacion }}</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<label>No hay registros para mostrar</label>
									@endif
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="alert alert-info">
										Créditos saldados
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@php
										$creditos = $socio->tercero
												->solicitudesCreditos()
												->where('fecha_desembolso', '<=', $fechaConsulta)
												->where('fecha_cancelación', '>=', $fechaConsulta->copy()->subYear())
												->estado('SALDADO')
												->get();
									@endphp
									@if($creditos->count())
										<table class="table table-hover">
											<thead>
												<tr>
													<th>Obligación</th>
													<th>Modalidad</th>
													<th>Fecha inicio</th>
													<th class="text-center">Valor inicial</th>
													<th class="text-center">Tasa M.V.</th>
													<th class="text-center">Valor cuota</th>
													<th>Estado</th>
													<th>Fecha cancelación</th>
												</tr>
											</thead>
											<tbody>
												@foreach($creditos as $credito)
													<tr>
														<td>
															<a href="{{ route('socioConsultaCreditos', $credito->id) . '?fecha=' . $fecha . '&socio=' . $socio->id }}">
																{{ $credito->numero_obligacion }}
															</a>
														</td>
														<td>{{ $credito->modalidadCredito->nombre }}</td>
														<td>{{ $credito->fecha_desembolso }}</td>
														<td class="text-right">${{ number_format($credito->valor_credito, 0) }}</td>
														<td class="text-right">{{ number_format($credito->tasa, 3) }}%</td>
														<td class="text-right">
															<?php
																$valorCuota = 0;
																$valorCuota = $credito->valor_cuota;
															?>
															${{ number_format($valorCuota, 0) }}
														</td>
														<td><span class="label label-default">{{ $credito->estado_solicitud }}</span></td>
														<td>{{ $credito->fecha_cancelacion }}</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									@else
										<br>
										<label>No hay registros de créditos para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="recaudoNomina">
							<br>
							<div class="row">
								<div class="col-md-11 table-responsive">
									@if($recaudos->count())
										<table class="table table-hover recaudos">
											<thead>
												<tr>
													<th>Periodo</th>
													<th>Concepto</th>
													<th>Código</th>
													<th class="text-center">Total generado</th>
													<th class="text-center">Total aplicado</th>
													<th class="text-center">Total ajustado</th>
												</tr>
											</thead>
											<tbody>
												<?php
													$totalGenerado = 0;
													$totalAplicado = 0;
													$totalAjustado = 0;
													$numeroPeriodo = '<a href="' . route('socioConsultaRecaudos', [$socio->id, $recaudos[0]->controlProceso->id]) . '">' . $recaudos[0]->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudos[0]->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>';
												?>
												@foreach($recaudos as $recaudo)
													<?php
														if($numeroPeriodo != '<a href="' . route('socioConsultaRecaudos', [$socio->id, $recaudo->controlProceso->id]) . '">' . $recaudo->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudo->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>')
														{
															?>
															<tr>
																<td>{!! $numeroPeriodo !!}</td>
																<th>Totales</th>
																<td></td>
																<th class="text-right">${{ number_format($totalGenerado, 0) }}</th>
																<th class="text-right">${{ number_format($totalAplicado, 0) }}</th>
																<th class="text-right">${{ number_format($totalAjustado, 0) }}</th>
															</tr>
															<?php
															$numeroPeriodo = '<a href="' . route('socioConsultaRecaudos', [$socio->id, $recaudo->controlProceso->id]) . '">' . $recaudo->controlProceso->calendarioRecaudo->numero_periodo . '.' . $recaudo->controlProceso->calendarioRecaudo->fecha_recaudo . '</a>';
															$totalGenerado = 0;
															$totalAplicado = 0;
															$totalAjustado = 0;
														}
														$totalGenerado += floatval($recaudo->total_generado);
														$totalAplicado += floatval($recaudo->total_aplicado);
														$totalAjustado += floatval($recaudo->total_ajustado);
													?>
													<tr>
														<td><a href="{{ route('socioConsultaRecaudos', [$socio->id, $recaudo->controlProceso->id]) }}">{{ $recaudo->controlProceso->calendarioRecaudo->numero_periodo }}.{{ $recaudo->controlProceso->calendarioRecaudo->fecha_recaudo }}</a></td>
														<td>{{ $recaudo->conceptoRecaudo->nombre }}</td>
														<td>{{ $recaudo->conceptoRecaudo->codigo }}</td>
														<td class="text-right">${{ number_format($recaudo->total_generado, 0) }}</td>
														<td class="text-right">${{ number_format($recaudo->total_aplicado, 0) }}</td>
														<td class="text-right">${{ number_format($recaudo->total_ajustado, 0) }}</td>
													</tr>
												@endforeach
												<tr>
													<td>{!! $numeroPeriodo !!}</td>
													<th>Totales</th>
													<td></td>
													<th class="text-right">${{ number_format($totalGenerado, 0) }}</th>
													<th class="text-right">${{ number_format($totalAplicado, 0) }}</th>
													<th class="text-right">${{ number_format($totalAjustado, 0) }}</th>
												</tr>
											</tbody>
										</table>
									@else
										<br>
										<label>No hay registros de recaudos para mostrar</label>
										<br><br>
									@endif
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="simulador">
							<br>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">
											Modalidad de crédito
										</label>
										{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off']) !!}
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">
											Valor crédito
										</label>
										<div class="input-group">
											<span class="input-group-addon">$</span>
											{!! Form::text('valorCredito', null, ['class' => 'form-control text-right', 'autofocus', 'data-maskMoney']) !!}
										</div>
									</div>
								</div>
								<div class="col-md-2">
									<div class="form-group">
										<label class="control-label">
											Plazo
										</label>
										{!! Form::text('plazo', null, ['class' => 'form-control text-right', 'autofocus']) !!}
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">
											Periodicidad de pago
										</label>
										{!! Form::select('periodicidad', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una periodicidad']) !!}
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<a class="btn btn-primary simular">Simular</a>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
								</div>
							</div>
							<div class="row tableSimulador" style="display:none;">
								<div class="col-md-12">
									<br>
									<div class="row">
										<div class="col-md-2 text-right"><label>Fecha crédito:</label></div>
										<div class="col-md-2 fechaCredito"></div>
										<div class="col-md-2 text-right"><label>Tasa:</label></div>
										<div class="col-md-2 tasa"></div>
									</div>
									<div class="row">
										<div class="col-md-12 col-md-12 table-responsive">
											<table class="table">
												<thead>
													<tr>
														<th>Cuota</th>
														<th>Fecha pago</th>
														<th class="text-center">Capital</th>
														<th class="text-center">Intereses</th>
														<th class="text-center">Total cuota</th>
														<th class="text-center">Nuevo saldo</th>
													</tr>
												</thead>
												<tbody>
												</tbody>
											</table>
										</div>
									</div>	
								</div>
							</div>
						</div>
						<div role="tabpanel" class="tab-pane fade" id="documentacion">
							<br>
							<div class="row">
								<div class="col-md-10 col-md-offset-1 col-sm-12">
									<ul class="list-unstyled">
										<li>
											<strong>
												<a href="#" data-toggle="modal" data-target="#mct">
													<i class="fa fa-file-pdf-o"></i> Certificado tributario
												</a>:
											</strong> Descargue el certificado tributario
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
				@endif
			</div>
			<div class="card-footer">
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@if($socio)
	<div class="modal fade" id="mct" tabindex="-1" role="dialog" aria-labelledby="mLabel">
		{!! Form::open(["route" => ["socioConsulta.documentacion", $socio->id], "method" => "get", "target" => "_blank"]) !!}
		{!! Form::hidden("certificado", "certificadoTributario") !!}
		<div class="modal-dialog" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title" id="mLabel">Certificado tributario</h4>
				</div>
				<div class="modal-body">
					<div class="row">
						<div class="col-md-12">
							Descargue el certificado tributario
						</div>
					</div>
					<br>
					<div class="row form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								<label class="col-sm-3 control-label">Seleccione año</label>
								<div class="col-sm-9">
									<?php
										$anios = [];
										$entidad = Auth::getSession()->get('entidad');
										$anioInicio = 2018;
										if($entidad->fecha_inicio_contabilidad->year > $anioInicio) {
											$anioInicio = $entidad->fecha_inicio_contabilidad->year;
										}
										$anioActual = date("Y") - 1;
										while($anioActual >= $anioInicio) {
											$anios[$anioActual] = $anioActual;
											$anioActual--;
										}
										if(!count($anios)) $anios[date("Y")] = date("Y");
									?>
									{!! Form::select('anio', $anios, null, ['class' => 'form-control']) !!}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					{!! Form::submit("Descargar", ["class" => "btn btn-success"]) !!}
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
@endif
@endsection

@push('style')
<style type="text/css">
	.profile-user-img {
		width: 250px;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='socio']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('socio/getSocioConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});

		@if(Request::has('socio') && !empty(Request::get('socio')))

			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ Request::get('socio') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio']"));
					$("select[name='socio']").val(element.id).trigger("change");
				}
			});

			$(".simular").click(function(){
				var $data = "socio={{ Request::get('socio') }}&fechaConsulta={{ $fecha }}&modalidad=";
				$data += $("select[name='modalidad']").val() + "&valorCredito=";
				$data += $("input[name='valorCredito']").maskMoney("cleanvalue") + "&plazo=";
				$data += $("input[name='plazo']").maskMoney("cleanvalue") + "&periodicidad=";
				$data += $("select[name='periodicidad']").val();
				$.ajax({
					url: '{{ url('socio/consulta/simularCredito') }}',
					dataType: 'json',
					data: $data
				}).done(function(data){
					$(".tableSimulador").show();
					$(".tableSimulador").find("tbody").empty();
					$(".tableSimulador").find(".fechaCredito").text(data.fechaCredito);
					$(".tableSimulador").find(".tasa").text(data.tasa + "% M.V.");
					jQuery.each(data.amortizacion, function(index, value){
						$tr = $("<tr>");
						$('<td>').text(value.numeroCuota).appendTo($tr);
						$('<td>').text(value.fechaCuota).appendTo($tr);
						$('<td>').text("$" + value.capital).addClass("text-right").appendTo($tr);
						$('<td>').text("$" + value.intereses).addClass("text-right").appendTo($tr);
						$('<td>').text("$" + value.total).addClass("text-right").appendTo($tr);
						$('<td>').text("$" + value.nuevoSaldoCapital).addClass("text-right").appendTo($tr);
						$tr.appendTo($(".tableSimulador").find("tbody"));
					});
				}).fail(function(data){
					$(".tableSimulador").hide();
					var $error = jQuery.parseJSON(data.responseText);
					$("#error").html($error.error);
					$("#error").show();
					$("#error").fadeOut(5000);
					//error($error);
				});
			});
		@endif

		$(document).ready(function() {
			$('.recaudos').DataTable({
				"columnDefs": [
					{ "visible": false, "targets": 0 }
				],
				"drawCallback": function ( settings ) {
					var api = this.api();
					var rows = api.rows( {page:'current'} ).nodes();
					var last = null;

					api.column(0, {page:'current'} ).data().each( function ( group, i ) {
						if ( last !== group ) {
							$(rows).eq( i ).before(
								'<tr class="group" style="background-color: #ddd !important;"><th colspan="5">'+group+'</th></tr>'
							);
							last = group;
						}
					} );
				},
				"displayLength": 25,
				"paging": true,
				"ordering": false,
				"info": false
			});
		});

		$("select[name='modalidad']").on("change", function(){
			var $modalidad = $("select[name='modalidad'] option:selected").val();
			$("select[name='periodicidad']").find('option').remove().end().append('<option>Seleccione una periodicidad</option>');
			$.ajax({
				url: '{{ url('socio/consulta/obtenerPeriodicidadesPorModalidad') }}',
				dataType: 'json',
				data: {modalidad: $modalidad}
			}).done(function(data){
				jQuery.each(data, function(index, value){
					$('<option>').val(index).text(value).appendTo($("select[name='periodicidad']"));
				});
			});
		});
	});
	function error(data)
	{
		$msg = "";
		$.each(data, function (key, subData){
			$.each(subData, function (index, childData) {
				$msg += childData + "<br>";
			});
		})

		$("#error").html($msg);
		$("#error").show();
		$("#error").fadeOut(5000);
	}
</script>
@endpush
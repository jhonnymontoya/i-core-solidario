@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Ajuste ahorros
						<small>Ahoroos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahoroos</a></li>
						<li class="breadcrumb-item active">Ajuste ahorros</li>
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
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Ajuste ahorros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('socio'), ['url' => 'ajusteAhorros', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-11">
							<div class="form-group {{ ($errors->has('socio')?'has-error':'') }}">
								<label class="col-sm-2 control-label">
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
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if($socio)
						<br>
						{!! Form::model(Request::only('socio'), ['url' => 'ajusteAhorros/ajuste', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask', 'id' => 'formProcesar']) !!}
						{!! Form::hidden('socio', Request::get('socio')) !!}
						<div class="row">
							<div class="col-md-12">
								<label>Ajuste para:</label> <strong>{{$socio->tercero->nombre_completo}}</strong>
								@if($socio->estado != 'ACTIVO')
									<span class="badge badge-pill badge-warning">SOCIO NO ACTIVO</span>
								@endif
							</div>
						</div>
						<br><br>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fechaAjuste')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fechaAjuste'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha ajuste
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										@php
											$fechaAjuste = date('d/m/Y');
											$fechaAjuste = Request::has('fechaAjuste') ? Request::get('fechaAjuste') : $fechaAjuste;
											$fechaAjuste = empty(old('fechaAjuste')) ? $fechaAjuste : old('fechaAjuste');
										@endphp
										{!! Form::text('fechaAjuste', $fechaAjuste, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fechaAjuste'))
										<span class="help-block">{{ $errors->first('fechaAjuste') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('modalidadId')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('modalidadId'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Seleccione modalidad
									</label>
									{!! Form::select('modalidadId', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione modalidad']) !!}
									@if ($errors->has('modalidadId'))
										<span class="help-block">{{ $errors->first('modalidadId') }}</span>
									@endif
								</div>
							</div>
						</div>
						<br>
						<div class="row">
							<div class="col-md-3 col-sm-12">
								<div class="row">
									<div class="col-md-6"><label>Valor cuota:</label></div>
									<div class="col-md-6"><span id="valorCuota">$0</span></div>
								</div>
							</div>
							<div class="col-md-3 col-sm-12">
								<div class="row">
									<div class="col-md-6"><label>Periodicidad:</label></div>
									<div class="col-md-6"><span id="periodicidad"></span></div>
								</div>
							</div>
						</div>
						<br><br>
						<div class="row form-horizontal">

							<div class="col-md-3">
								<div class="form-group">
									<label class="col-md-8 text-right">
										Saldo ahorro
									</label>
									<div class="col-md-4 text-right" id="saldo">$0</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('valorAjuste')?'has-error':'') }}">
									<label class="col-md-4 control-label">
										@if ($errors->has('valorAjuste'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Ajuste
									</label>
									<div class="col-md-8 input-group">
										<div class="input-group-addon">$</div>
										{!! Form::text('valorAjuste', 0, ['class' => 'form-control text-right', 'placeholder' => '0', 'autocomplete' => 'off', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
									</div>
									@if ($errors->has('valorAjuste'))
										<span class="help-block">{{ $errors->first('valorAjuste') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-1 col-md-offset-1">
								<div class="form-group {{ ($errors->has('naturalezaAjusteAhorros')?'has-error':'') }}">
									<div class="btn-group" data-toggle="buttons">
										<?php
											$naturaleza = trim(old('naturalezaAjusteAhorros')) == '' ? 'AUMENTO' : old('naturalezaAjusteAhorros');
											$naturaleza = $naturaleza == 'AUMENTO' ? true : false;
										?>
										<label class="btn btn-outline-primary {{ $naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteAhorros', 'AUMENTO', $naturaleza ? true : false) !!}<i class="fa fa-arrow-up"></i>
										</label>
										<label class="btn btn-outline-primary {{ !$naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteAhorros', 'DECREMENTO', !$naturaleza ? true : false) !!}<i class="fa fa-arrow-down"></i>
										</label>
									</div>
									@if ($errors->has('naturalezaAjusteAhorros'))
										<span class="help-block">{{ $errors->first('naturalezaAjusteAhorros') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label class="col-md-6 text-right">
										Nuevo saldo
									</label>
									<div class="col-md-3 text-right nuevoSaldoAhorros">$0</div>
								</div>
							</div>
						</div>

						<div class="row form-horizontal">

							<div class="col-md-3">
								<div class="form-group">
									<label class="col-md-8 text-right">
										Saldo intereses
									</label>
									<div class="col-md-4 text-right" id="saldoIntereses">$0</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('valorAjusteIntereses')?'has-error':'') }}">
									<label class="col-md-4 control-label">
										@if ($errors->has('valorAjusteIntereses'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Ajuste
									</label>
									<div class="col-md-8 input-group">
										<div class="input-group-addon">$</div>
										{!! Form::text('valorAjusteIntereses', 0, ['class' => 'form-control text-right', 'placeholder' => '0', 'autocomplete' => 'off', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
									</div>
									@if ($errors->has('valorAjusteIntereses'))
										<span class="help-block">{{ $errors->first('valorAjusteIntereses') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-1 col-md-offset-1">
								<div class="form-group {{ ($errors->has('naturalezaAjusteIntereses')?'has-error':'') }}">
									<div class="btn-group" data-toggle="buttons">
										<?php
											$naturaleza = trim(old('naturalezaAjusteIntereses')) == '' ? 'AUMENTO' : old('naturalezaAjusteIntereses');
											$naturaleza = $naturaleza == 'AUMENTO' ? true : false;
										?>
										<label class="btn btn-outline-primary {{ $naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteIntereses', 'AUMENTO', $naturaleza ? true : false) !!}<i class="fa fa-arrow-up"></i>
										</label>
										<label class="btn btn-outline-primary {{ !$naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteIntereses', 'DECREMENTO', !$naturaleza ? true : false) !!}<i class="fa fa-arrow-down"></i>
										</label>
									</div>
									@if ($errors->has('naturalezaAjusteIntereses'))
										<span class="help-block">{{ $errors->first('naturalezaAjusteIntereses') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label class="col-md-6 text-right">
										Nuevo saldo
									</label>
									<div class="col-md-3 text-right nuevoSaldoIntereses">$0</div>
								</div>
							</div>
						</div>
						<div class="row form-horizontal">

							<div class="col-md-3">
								<div class="form-group">
									<label class="col-md-8 text-right">
										Total ajuste:
									</label>
									<div class="col-md-4 text-right totalAjuste">
										$0
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('cuifId')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cuifId'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta contrapartida
									</label>
									{!! Form::select('cuifId', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione modalidad']) !!}
									@if ($errors->has('cuifId'))
										<span class="help-block">{{ $errors->first('cuifId') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('terceroContrapartidaId')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('terceroContrapartidaId'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tercero contrapartida
									</label>
									{!! Form::select('terceroContrapartidaId', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un tercero']) !!}
									@if ($errors->has('terceroContrapartidaId'))
										<span class="help-block">{{ $errors->first('terceroContrapartidaId') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('referencia')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('referencia'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Referencia
									</label>
									{!! Form::text('referencia', null, ['class' => 'form-control', 'placeholder' => 'Referencia']) !!}
									@if ($errors->has('referencia'))
										<span class="help-block">{{ $errors->first('referencia') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('observaciones')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('observaciones'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Observaciones
									</label>
									{!! Form::textarea('observaciones', null, ['class' => 'form-control', 'placeholder' => 'Observaciones']) !!}
									@if ($errors->has('observaciones'))
										<span class="help-block">{{ $errors->first('observaciones') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12">							
								<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#confirmacion">Continuar</button>
								<a href="{{ url('ajusteAhorros') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
							</div>
						</div>


						<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
										<h4 class="modal-title" id="tituloConfirmacion">Ajuste ahorro</h4>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-10 col-md-offset-1">
												<div class="alert alert-warning">
													<h4>
														<i class="fa fa-warning"></i>&nbsp;Alerta!
													</h4>
													Confirme los datos antes de grabar el ajuste de ahorros
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-10 col-md-offset-1">
												<dl class="dl-horizontal">
													<dt>Socio:</dt>
													<dd>{{ $socio->tercero->nombre_completo }}</dd>
													<dt>Fecha ajuste:</dt>
													<dd id="fechaAjuste"></dd>
													<dt>Modalidad:</dt>
													<dd id="modalidad"></dd>
												</dl>
												<div class="row">
													<div class="col-md-4">
														<dl class="dl-horizontal">
															<dt>Saldo ahorro:</dt>
															<dd id="saldoConfirmacion"></dd>
															<dt>Saldo intereses:</dt>
															<dd id="saldoInteresesConfirmacion"></dd>
														</dl>
													</div>
												</div>

												<div class="row">
													<div class="col-md-4">
														<dl class="dl-horizontal">
															<dt>Nuevo ahorro:</dt>
															<dd id="nuevoAhorro"></dd>
															<dt>Nuevo interes:</dt>
															<dd id="nuevoIntereses"></dd>
														</dl>
													</div>
												</div>

												<div class="row">
													<div class="col-md-4">
														<dl class="dl-horizontal">
															<dt>Valor ajuste:</dt>
															<dd id="valorAjuste"></dd>
														</dl>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<a class="btn btn-outline-success" id="continuar">Ajustar</a>
										{{--{!! Form::submit('Ajustar', ['class' => 'btn btn-outline-success']) !!}--}}
										<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
									</div>
								</div>
							</div>
						</div>
						{!! Form::close() !!}
					@endif
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
	$(function(){
		$("#continuar").click(function(){
			$("#continuar").addClass("disabled");
			$("#formProcesar").submit();
		});

		$("select[name='socio']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: 'socio/getSocioConParametros',
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
		
		@if(Request::has('socio'))
			var saldoAhorros = 0;
			var saldoIntereses = 0;

			$("select[name='cuifId']").select2({
				allowClear: true,
				placeholder: "Seleccione una opción",
				ajax: {
					url: 'cuentaContable/getCuentaConParametros',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							modulo: '1,2',
							estado: '1',
							tipoCuenta: 'AUXILIAR'
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

			@if(!empty(old('cuifId')))
				$.ajax({url: 'cuentaContable/getCuentaConParametros', dataType: 'json', data: {id: {{ old('cuifId') }} }}).done(function(data){
					if(data.total_count == 1)
					{
						element = data.items[0];
						$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuifId']"));
						$("select[name='cuifId']").val(element.id).trigger("change");
					}
				});
			@endif


			$.ajax({url: 'socio/getSocioConParametros', dataType: 'json', data: {id: {{ Request::get('socio') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio']"));
					$("select[name='socio']").val(element.id).trigger("change");
				}
			});

			$("input[name='fechaAjuste']").on('changeDate', function(){
				getSaldos();
			});

			$("select[name='modalidadId']").on('change', function(){
				getSaldos();
			});

			function getSaldos() {
				if($("select[name='modalidadId'] option:selected").val() > 0) {
					var data = {socioId: {{ Request::get('socio') }}, modalidadAhorroId: $("select[name='modalidadId'] option:selected").val(), fechaSaldo: $("input[name='fechaAjuste']").val()};

					$.ajax({url: 'ajusteAhorros/saldosPorModalidad', dataType: 'json', data: data}).done(function(data){
						$("#saldo").text("$" + data.saldo);
						$(".nuevoSaldoAhorros").text("$" + data.saldo);
						$("#saldoIntereses").text("$" + data.intereses);
						$(".nuevoSaldoIntereses").text("$" + data.intereses);
						$("#valorCuota").text("$" + data.cuota);
						$("#periodicidad").text(data.periodicidad);
						saldoAhorros = data.ahorrosSinFormato;
						saldoIntereses = data.interesesSinFormato;
						$("input[name='valorAjuste']").attr('disabled', false);
						if(data.codigoModalidad != 'APO') {
							$("input[name='valorAjusteIntereses']").attr('disabled', false);
						}
						else {
							$("input[name='valorAjusteIntereses']").attr('disabled', true);
							$("input[name='valorAjusteIntereses']").val(0);
						}
						totalAjuste();
					});
				}
				else {
					$("#saldo").text("$0");
					$(".nuevoSaldoAhorros").text("$0");
					$("#saldoIntereses").text("$0");
					$(".nuevoSaldoIntereses").text("$0");
					$("#valorCuota").text("$0");
					$("#periodicidad").text("");
					saldoAhorros = 0
					saldoIntereses = 0;
					$("input[name='valorAjuste']").attr('disabled', true);
					$("input[name='valorAjusteIntereses']").attr('disabled', true);
					$("input[name='valorAjuste']").val(0);
					$("input[name='valorAjusteIntereses']").val(0);
					totalAjuste(intereses = false);
				}
			}
			getSaldos();

			$("input[name='valorAjuste']").on('change', function(event){
				totalAjuste();
			});
			$("input[name='naturalezaAjusteAhorros']").on('change', function(event){
				totalAjuste();
			});
			$("input[name='valorAjusteIntereses']").on('change', function(event){
				totalAjuste();
			});
			$("input[name='naturalezaAjusteIntereses']").on('change', function(event){
				totalAjuste();
			});

			function ajusteAhorros(){
				var $ajuste = 0;
				var $valorAjuste = $("input[name='valorAjuste']").maskMoney('cleanvalue');
				if($("input[name='naturalezaAjusteAhorros']:checked").val() == 'AUMENTO'){
					$ajuste = saldoAhorros + $valorAjuste;
				}
				else{
					$ajuste = saldoAhorros - $valorAjuste;
					$valorAjuste = -$valorAjuste;
				}
				$(".nuevoSaldoAhorros").text("$" + $("input[name='valorAjuste']").maskMoney('maskvalue', $ajuste));
				return $valorAjuste;
			}

			function ajusteIntereses(){
				var $ajuste = 0;
				var $valorAjuste = $("input[name='valorAjusteIntereses']").maskMoney('cleanvalue');
				if($("input[name='naturalezaAjusteIntereses']:checked").val() == 'AUMENTO'){
					$ajuste = saldoIntereses + $valorAjuste;
				}
				else{
					$ajuste = saldoIntereses - $valorAjuste;
					$valorAjuste = -$valorAjuste;
				}
				$(".nuevoSaldoIntereses").text("$" + $("input[name='saldoIntereses']").maskMoney('maskvalue', $ajuste));
				return $valorAjuste;
			}

			function totalAjuste(ahorro = true, intereses = true){
				var $totalAjuste = ahorro ? ajusteAhorros() : 0;
				$totalAjuste += intereses ? ajusteIntereses() : 0;
				$(".totalAjuste").text("$" + $("input[name='valorAjuste']").maskMoney('maskvalue', $totalAjuste));
			}

			totalAjuste();

			$("select[name='terceroContrapartidaId']").select2({
				allowClear: false,
				placeholder: "Seleccione un tercero",
				ajax: {
					url: "{{ url('tercero/getTerceroConParametros') }}",
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							page: params.page,
							estado: 'ACTIVO'
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

			@php
				$terceroContraPartida = old('terceroContrapartidaId');
				$terceroContraPartida = empty($terceroContraPartida) ? optional($socio)->tercero_id : $terceroContraPartida;
			@endphp

			@if(!empty($terceroContraPartida))
				$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ $terceroContraPartida }} }}).done(function(data){
					if(data.total_count == 1)  {
						element = data.items[0];
						$('<option>').val(element.id).text(element.text).appendTo($("select[name='terceroContrapartidaId']"));
						$("select[name='terceroContrapartidaId']").val(element.id).trigger("change");
					}
				});
			@endif

			$('#confirmacion').on('show.bs.modal', function (event){
				var confirmacion = $(this);

				confirmacion.find('#fechaAjuste').text($("input[name='fechaAjuste']").val());
				if($("select[name='modalidadId'] option:selected").val() == '') {
					confirmacion.find('#modalidad').text('Por favor, seleccione una modalidad');
				}
				else {
					confirmacion.find('#modalidad').text($("select[name='modalidadId'] option:selected").text());
				}
				$("input[name='valorAjuste']").maskMoney('mask');
				$("input[name='valorAjusteIntereses']").maskMoney('mask');
				confirmacion.find('#saldoConfirmacion').text("$" + $("input[name='valorAjuste']").val());
				confirmacion.find('#saldoInteresesConfirmacion').text("$" + $("input[name='valorAjusteIntereses']").val());

				confirmacion.find('#nuevoAhorro').text($(".nuevoSaldoAhorros").text());
				confirmacion.find('#nuevoIntereses').text($(".nuevoSaldoIntereses").text());

				confirmacion.find('#valorAjuste').text($(".totalAjuste").text());
			});
		@endif
	});
</script>
@endpush
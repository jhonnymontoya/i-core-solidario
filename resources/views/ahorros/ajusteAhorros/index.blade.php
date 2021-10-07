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
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
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
				@if (Session::has('codigoComprobante'))
					<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ Session::get('codigoComprobante') }}&numeroComprobante={{ Session::get('numeroComprobante') }}" title="Imprimir comprobante" target="_blank">
						{{ Session::get('message') }}
					</a>
					<i class="fas fa-external-link-alt"></i>
				@else
					{{ Session::get('message') }}
				@endif
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
					{!! Form::model(Request::only('socio'), ['url' => 'ajusteAhorros', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-11">
							<div class="form-group {{ ($errors->has('socio')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('socio'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Seleccione socio
								</label>
								{!! Form::select('socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio']) !!}
								@if ($errors->has('socio'))
									<span class="help-block">{{ $errors->first('socio') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<label class="control-label">&nbsp;</label>
							<br>
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
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
								<div class="form-group">
									@php
										$valid = $errors->has('fechaAjuste') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Fecha ajuste</label>
									<div class="input-group">
										<div class="input-group-prepend">
											<span class="input-group-text">
												<i class="fa fa-calendar"></i>
											</span>
										</div>
										@php
											$fechaAjuste = date('d/m/Y');
											$fechaAjuste = Request::has('fechaAjuste') ? Request::get('fechaAjuste') : $fechaAjuste;
											$fechaAjuste = empty(old('fechaAjuste')) ? $fechaAjuste : old('fechaAjuste');
										@endphp
										{!! Form::text('fechaAjuste', $fechaAjuste, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										@if ($errors->has('fechaAjuste'))
											<div class="invalid-feedback">{{ $errors->first('fechaAjuste') }}</div>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group">
									@php
										$valid = $errors->has('modalidadId') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Seleccione modalidad</label>
									{!! Form::select('modalidadId', $modalidades, null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Seleccione modalidad']) !!}
									@if ($errors->has('modalidadId'))
										<div class="invalid-feedback">{{ $errors->first('modalidadId') }}</div>
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
						<div class="row">
							<div class="col-md-3">
								<div class="form-group">
									<label class="text-right">
										Saldo ahorro
									</label>
									<div class="text-right" id="saldo">$0</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									@php
										$valid = $errors->has('valorAjuste') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Ajuste</label>
									<div class="input-group">
										<div class="input-group-prepend"><span class="input-group-text">$</span></div>
										{!! Form::text('valorAjuste', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Ajuste', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
										@if ($errors->has('valorAjuste'))
											<div class="invalid-feedback">{{ $errors->first('valorAjuste') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">&nbsp;</label>
									<div>
										@php
											$valid = $errors->has('naturalezaAjusteAhorros') ? 'is-invalid' : '';
											$naturaleza = empty(old('naturalezaAjusteAhorros')) ? 'AUMENTO' : old('naturalezaAjusteAhorros');
										@endphp
										<div class="btn-group btn-group-toggle" data-toggle="buttons">
											<label class="btn btn-primary {{ $naturaleza == 'AUMENTO' ? 'active' : '' }}">
												{!! Form::radio('naturalezaAjusteAhorros', 'AUMENTO', ($naturaleza == 'AUMENTO' ? true : false), ['class' => [$valid]]) !!}<i class="fa fa-arrow-up"></i>
											</label>
											<label class="btn btn-primary {{ $naturaleza == 'DECREMENTO' ? 'active' : '' }}">
												{!! Form::radio('naturalezaAjusteAhorros', 'DECREMENTO', ($naturaleza == 'DECREMENTO' ? true : false ), ['class' => [$valid]]) !!}<i class="fa fa-arrow-down"></i>
											</label>
										</div>
										@if ($errors->has('naturalezaAjusteAhorros'))
											<div class="invalid-feedback">{{ $errors->first('naturalezaAjusteAhorros') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label class="text-right">
										Nuevo saldo
									</label>
									<div class="text-right nuevoSaldoAhorros">$0</div>
								</div>
							</div>
						</div>

						<div class="row">

							<div class="col-md-3">
								<div class="form-group">
									<label class="text-right">
										Saldo intereses
									</label>
									<div class="text-right" id="saldoIntereses">$0</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									@php
										$valid = $errors->has('valorAjusteIntereses') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Ajuste</label>
									<div class="input-group">
										<div class="input-group-prepend"><span class="input-group-text">$</span></div>
										{!! Form::text('valorAjusteIntereses', 0, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => '0', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
										@if ($errors->has('valorAjusteIntereses'))
											<div class="invalid-feedback">{{ $errors->first('valorAjusteIntereses') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-2">
								<div class="form-group">
									<label class="control-label">&nbsp;</label>
									<div>
										@php
											$valid = $errors->has('naturalezaAjusteIntereses') ? 'is-invalid' : '';
											$naturaleza = empty(old('naturalezaAjusteIntereses')) ? 'AUMENTO' : old('naturalezaAjusteIntereses');
										@endphp
										<div class="btn-group btn-group-toggle" data-toggle="buttons">
											<label class="btn btn-primary {{ $naturaleza == 'AUMENTO' ? 'active' : '' }}">
												{!! Form::radio('naturalezaAjusteIntereses', 'AUMENTO', ($naturaleza == 'AUMENTO' ? true : false), ['class' => [$valid]]) !!}<i class="fa fa-arrow-up"></i>
											</label>
											<label class="btn btn-primary {{ $naturaleza == 'DECREMENTO' ? 'active' : '' }}">
												{!! Form::radio('naturalezaAjusteIntereses', 'DECREMENTO', ($naturaleza == 'DECREMENTO' ? true : false ), ['class' => [$valid]]) !!}<i class="fa fa-arrow-down"></i>
											</label>
										</div>
										@if ($errors->has('naturalezaAjusteIntereses'))
											<div class="invalid-feedback">{{ $errors->first('naturalezaAjusteIntereses') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="col-md-3">
								<div class="form-group">
									<label class="text-right">Nuevo saldo</label>
									<div class="text-right nuevoSaldoIntereses">$0</div>
								</div>
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-6">
								<div class="form-group row">
									<label class="col-md-3 text-right">Total ajuste:</label>
									<div class="col-md-4 text-right totalAjuste">$0</div>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									@php
										$valid = $errors->has('cuifId') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Cuenta contrapartida</label>
									{!! Form::select('cuifId', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('cuifId'))
										<div class="invalid-feedback">{{ $errors->first('cuifId') }}</div>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									@php
										$valid = $errors->has('terceroContrapartidaId') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Tercero contrapartida</label>
									{!! Form::select('terceroContrapartidaId', [], null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione un tercero']) !!}
									@if ($errors->has('terceroContrapartidaId'))
										<div class="invalid-feedback">{{ $errors->first('terceroContrapartidaId') }}</div>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group">
									@php
										$valid = $errors->has('referencia') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Referencia</label>
									{!! Form::text('referencia', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Referencia']) !!}
									@if ($errors->has('referencia'))
										<div class="invalid-feedback">{{ $errors->first('referencia') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group">
									@php
										$valid = $errors->has('observaciones') ? 'is-invalid' : '';
									@endphp
									<label class="control-label">Observaciones</label>
									{!! Form::textarea('observaciones', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Observaciones']) !!}
									@if ($errors->has('observaciones'))
										<div class="invalid-feedback">{{ $errors->first('observaciones') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-12 text-right">
								<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#confirmacion">Continuar</button>
								<a href="{{ url('ajusteAhorros') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
							</div>
						</div>


						<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" id="tituloConfirmacion">Ajuste ahorro</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-12">
												<div class="alert alert-warning">
													<h4>
														<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
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
										<a href="#" class="btn btn-outline-success" id="continuar">Ajustar</a>
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
		$("#continuar").click(function(e){
			e.preventDefault();
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

@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Ajuste créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Ajuste créditos</li>
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
		{!! Form::model($obligacion, ['url' => ['ajusteCreditos', $obligacion], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask', 'id' => 'formProcesar']) !!}
		{!! Form::hidden('fechaAjuste', "$fecha") !!}
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Ajuste créditos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							@php
								$tercero = $obligacion->tercero;
							@endphp
							<label>Ajuste para:</label> {{ $tercero->tipoIdentificacion->codigo }} {{$tercero->nombre_completo}}
						</div>
						<div class="col-md-6">
							<label>Fecha ajuste:</label> {{ $fecha }}
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<label>Obligación:</label> {{ $obligacion->numero_obligacion}}
						</div>
						<div class="col-md-3">
							<label>Valor inicial:</label> ${{ number_format($obligacion->valor_credito, 0) }}
						</div>
						<div class="col-md-3">
							<label>Fecha de crédito:</label> {{ $obligacion->fecha_desembolso }}
						</div>
						<div class="col-md-3">
							<label>Tasa M.V.:</label> {{ $obligacion->tasa }}%
						</div>
					</div>
					<br>
					<h4>Conceptos de ajuste</h4>

					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<label>Saldo capital</label>
								<div>${{ number_format($obligacion->saldoObligacion($fecha), 0) }}</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('ajusteCapital') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Ajuste</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('ajusteCapital', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => '0', 'data-maskMoney']) !!}
									@if ($errors->has('ajusteCapital'))
										<div class="invalid-feedback">{{ $errors->first('ajusteCapital') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<div>
									@php
										$naturaleza = trim(old('naturalezaAjusteCapital')) == '' ? 'AUMENTO' : old('naturalezaAjusteCapital');
										$naturaleza = $naturaleza == 'AUMENTO' ? true : false;
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteCapital', 'AUMENTO', $naturaleza ? true : false, ['class' => [$valid]]) !!}<i class="fa fa-arrow-up"></i>
										</label>
										<label class="btn btn-primary {{ !$naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteCapital', 'DECREMENTO', !$naturaleza ? true : false, ['class' => [$valid]]) !!}<i class="fa fa-arrow-down"></i>
										</label>
									</div>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label class="text-right">
									Nuevo saldo
								</label>
								<div class="nuevoSaldoCapital">
									${{ number_format($obligacion->saldoObligacion($fecha), 0) }}
								</div>
							</div>
						</div>
					</div>

					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<label>Saldo intereses</label>
								<div>
									${{ number_format($obligacion->saldoInteresObligacion($fecha), 0) }}
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('ajusteIntereses') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Ajuste</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('ajusteIntereses', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => '0', 'data-maskMoney']) !!}
									@if ($errors->has('ajusteIntereses'))
										<div class="invalid-feedback">{{ $errors->first('ajusteIntereses') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<div>
									@php
										$naturaleza = trim(old('naturalezaAjusteIntereses')) == '' ? 'AUMENTO' : old('naturalezaAjusteIntereses');
										$naturaleza = $naturaleza == 'AUMENTO' ? true : false;
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteIntereses', 'AUMENTO', $naturaleza ? true : false) !!}<i class="fa fa-arrow-up"></i>
										</label>
										<label class="btn btn-primary {{ !$naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteIntereses', 'DECREMENTO', !$naturaleza ? true : false) !!}<i class="fa fa-arrow-down"></i>
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
								<label>Nuevo saldo</label>
								<div class="nuevoSaldoIntereses">
									${{ number_format($obligacion->saldoInteresObligacion($fecha), 0) }}
								</div>
							</div>
						</div>
					</div>

					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								<label>Saldo seguro cartera</label>
								<div>
									<?php
										$saldoSeguroCartera = $obligacion->saldoSeguroObligacion($fecha);
										echo "$" . number_format($saldoSeguroCartera);
									?>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('ajusteSeguroCartera') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Ajuste</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									@php
										$seguro = is_null($obligacion->seguroCartera) ? false : true;
									@endphp
									{!! Form::text('ajusteSeguroCartera', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => '0', 'data-maskMoney', $seguro ? '' : 'readonly']) !!}
									@if ($errors->has('ajusteSeguroCartera'))
										<div class="invalid-feedback">{{ $errors->first('ajusteSeguroCartera') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label>
								<div>
									@php
										$naturaleza = trim(old('naturalezaAjusteSeguro')) == '' ? 'AUMENTO' : old('naturalezaAjusteSeguro');
										$naturaleza = $naturaleza == 'AUMENTO' ? true : false;
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteSeguro', 'AUMENTO', $naturaleza ? true : false) !!}<i class="fa fa-arrow-up"></i>
										</label>
										<label class="btn btn-primary {{ !$naturaleza ? 'active' : '' }}">
											{!! Form::radio('naturalezaAjusteSeguro', 'DECREMENTO', !$naturaleza ? true : false) !!}<i class="fa fa-arrow-down"></i>
										</label>
									</div>
									@if ($errors->has('naturalezaAjusteSeguro'))
										<div class="invalid-feedback">{{ $errors->first('naturalezaAjusteSeguro') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label>Nuevo saldo</label>
								<div class="nuevoSaldoSeguro">
									${{ number_format($saldoSeguroCartera) }}
								</div>
							</div>
						</div>
					</div>

					<br>

					<div class="row">
						<div class="col-md-12">
							<dl class="row">
								<dt class="col-md-2">Total ajuste</dt>
								<dd class="col-md-9 totalAjuste">$0</dd>
							</dl>
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
								{!! Form::select('terceroContrapartidaId', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
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
									$valid = $errors->has('comentarios') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Observaciones</label>
								{!! Form::textarea('comentarios', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Observaciones']) !!}
								@if ($errors->has('comentarios'))
									<div class="invalid-feedback">{{ $errors->first('comentarios') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="tituloConfirmacion">Ajuste crédito</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div class="alert alert-warning">
												<h4>
													<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
												</h4>
												Confirme los datos antes de grabar el ajuste de crédito
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<dl class="dl-horizontal">
												<dt>Deudor:</dt>
												<dd>{{ $tercero->tipoIdentificacion->codigo }} {{ $tercero->nombre_completo }}</dd>
												<dt>Obligación:</dt>
												<dd>{{ $obligacion->numero_obligacion }}</dd>
												<dt>Tasa:</dt>
												<dd>{{ $obligacion->tasa }}%</dd>
												<dt>Fecha ajuste:</dt>
												<dd>{{ $fecha }}</dd>
											</dl>
											<div class="row">
												<div class="col-md-4">
													<dl class="dl-horizontal">
														<dt>Nuevo saldo capital:</dt>
														<dd id="capitalConfirmacion"></dd>
														<dt>Nuevo saldo intereses:</dt>
														<dd id="interesesConfirmacion"></dd>
														<dt>Nuevo saldo seguro cartera:</dt>
														<dd id="seguroConfirmacion"></dd>
														<br>
														<dt>Total ajuste:</dt>
														<dd id="totalConfirmacion"></dd>
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

				</div>
				<div class="card-footer text-right">
					<div class="row">
						@php
							$url = sprintf("%s?tercero=%s&fechaAjuste=%s", url('ajusteCreditos'), $tercero->id, $fecha);
						@endphp
						<div class="col-md-12">
							<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#confirmacion">Ajustar</button>
							<a href="{{ $url }}" class="btn btn-outline-danger pull-right">Cancelar</a>
						</div>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		var saldoCapital = {{ $obligacion->saldoObligacion($fecha) }};
		var saldoIntereses = {{ $obligacion->saldoInteresObligacion($fecha) }};
		var saldoSeguroCartera = {{ $saldoSeguroCartera }};

		$("input[name='ajusteCapital']").on('change', function(event){
			totalAjuste();
		});
		$("input[name='naturalezaAjusteCapital']").on('change', function(event){
			totalAjuste();
		});

		function ajusteCapital(){
			var $ajuste = 0;
			var $valorAjuste = $("input[name='ajusteCapital']").maskMoney('cleanvalue');
			if($("input[name='naturalezaAjusteCapital']:checked").val() == 'AUMENTO'){
				$ajuste = saldoCapital + $valorAjuste;
			}
			else{
				$ajuste = saldoCapital - $valorAjuste;
				$valorAjuste = -$valorAjuste;
			}
			$(".nuevoSaldoCapital").text("$" + $("input[name='ajusteCapital']").maskMoney('maskvalue', $ajuste));
			return $valorAjuste;
		}

		$("input[name='ajusteIntereses']").on('change', function(event){
			totalAjuste();
		});
		$("input[name='naturalezaAjusteIntereses']").on('change', function(event){
			totalAjuste();
		});

		function ajusteIntereses(){
			var $ajuste = 0;
			var $valorAjuste = $("input[name='ajusteIntereses']").maskMoney('cleanvalue');
			if($("input[name='naturalezaAjusteIntereses']:checked").val() == 'AUMENTO'){
				$ajuste = saldoIntereses + $valorAjuste;
			}
			else{
				$ajuste = saldoIntereses - $valorAjuste;
				$valorAjuste = -$valorAjuste;
			}
			$(".nuevoSaldoIntereses").text("$" + $("input[name='ajusteIntereses']").maskMoney('maskvalue', $ajuste));
			return $valorAjuste;
		}

		$("input[name='ajusteSeguroCartera']").on('change', function(event){
			totalAjuste();
		});
		$("input[name='naturalezaAjusteSeguro']").on('change', function(event){
			totalAjuste();
		});

		function ajusteSeguroCartera(){
			var $ajuste = 0;
			var $valorAjuste = $("input[name='ajusteSeguroCartera']").maskMoney('cleanvalue');
			if($("input[name='naturalezaAjusteSeguro']:checked").val() == 'AUMENTO'){
				$ajuste = saldoSeguroCartera + $valorAjuste;
			}
			else{
				$ajuste = saldoSeguroCartera - $valorAjuste;
				$valorAjuste = -$valorAjuste;
			}
			$(".nuevoSaldoSeguro").text("$" + $("input[name='ajusteSeguroCartera']").maskMoney('maskvalue', $ajuste));
			return $valorAjuste;
		}

		function totalAjuste(){
			var $totalAjuste = ajusteCapital() + ajusteIntereses() +  ajusteSeguroCartera();
			$(".totalAjuste").text("$" + $("input[name='ajusteSeguroCartera']").maskMoney('maskvalue', $totalAjuste));
		}

		totalAjuste();

		$("select[name='cuifId']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: "{{ url('cuentaContable/getCuentaConParametros') }}",
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
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuifId') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuifId']"));
					$("select[name='cuifId']").val(element.id).trigger("change");
				}
			});
		@endif

		$('#confirmacion').on('show.bs.modal', function (event){
			var confirmacion = $(this);

			confirmacion.find('#capitalConfirmacion').text($(".nuevoSaldoCapital").text());
			confirmacion.find('#interesesConfirmacion').text($(".nuevoSaldoIntereses").text());
			confirmacion.find('#seguroConfirmacion').text($(".nuevoSaldoSeguro").text());
			confirmacion.find('#totalConfirmacion').text($(".totalAjuste").text());
		});

		$("#continuar").click(function(e){
			e.preventDefault();
			$("#continuar").addClass("disabled");
			$("#formProcesar").submit();
		});

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
			$terceroContraPartida = empty($terceroContraPartida) ? $tercero->id : $terceroContraPartida;
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
	});
</script>
@endpush
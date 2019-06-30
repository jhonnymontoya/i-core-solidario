@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Comprobantes
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Comprobantes</li>
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
		{!! Form::open(['url' => ['comprobante', $movimiento, 'impuesto'], 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Agregar impuesto</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('tipoImpuesto')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tipoImpuesto'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo impuesto
									</label>
									<br>
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary active">
											{!! Form::radio('tipoImpuesto', 'NACIONAL', true) !!}Nacional
										</label>
										<label class="btn btn-primary">
											{!! Form::radio('tipoImpuesto', 'REGIONAL', false) !!}Regional
										</label>
										<label class="btn btn-primary">
											{!! Form::radio('tipoImpuesto', 'DISTRITAL', false) !!}Distrital
										</label>
									</div>
									@if ($errors->has('tipoImpuesto'))
										<span class="help-block">{{ $errors->first('tipoImpuesto') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('impuesto')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('impuesto'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Impuesto
									</label>
									<br>
									{!! Form::select('impuesto', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un impuesto']) !!}
									@if ($errors->has('impuesto'))
										<span class="help-block">{{ $errors->first('impuesto') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('concepto')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('concepto'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Concepto
									</label>
									<br>
									{!! Form::select('concepto', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un concepto']) !!}
									@if ($errors->has('concepto'))
										<span class="help-block">{{ $errors->first('concepto') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('tercero')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('tercero'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tercero
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-male"></i></span>
										{!! Form::select('tercero', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('tercero'))
										<span class="help-block">{{ $errors->first('tercero') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('base')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('base'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Base
									</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('base', null, ['class' => 'form-control text-right', 'placeholder' => 'Base', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'false']) !!}
									</div>
									@if ($errors->has('base'))
										<span class="help-block">{{ $errors->first('base') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4 col-sm-12">
								<div class="form-group {{ ($errors->has('iva')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('iva'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										I.V.A.
									</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('iva', null, ['class' => 'form-control text-right', 'placeholder' => 'IVA', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'true']) !!}
									</div>
									@if ($errors->has('iva'))
										<span class="help-block">{{ $errors->first('iva') }}</span>
									@endif
								</div>
							</div>
						</div>
						<hr>
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Tercero</th>
											<th>Impuesto</th>
											<th>Concepto</th>
											<th class="text-center">Base</th>
											<th class="text-center">Tasa</th>
											<th class="text-center">Valor impuesto</th>
											<th class="text-center">IVA</th>
											<th></th>
										</tr>
									</thead>
									<tbody>
										@foreach ($movimiento->movimientosImpuestosTemporales as $imp)
											<tr data-id="{{ $imp->id }}">
												<td>{{ $imp->terceroRelacion->nombre_completo }}</td>
												<td>{{ $imp->impuesto->nombre }}</td>
												<td>{{ $imp->conceptoImpueso->nombre }}</td>
												<td class="text-right">${{ number_format($imp->base, 0) }}</td>
												<td class="text-right">{{ number_format($imp->tasa, 2) }}%</td>
												<td class="text-right">${{ number_format($imp->valor_impuesto, 0) }}</td>
												<td class="text-right">${{ number_format($imp->iva, 0) }}</td>
												<td>
													<a class="btn btn-danger btn-xs eliminarImpuesto">
														<i class="fa fa-trash"></i>
													</a>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
					<div class="box-footer">
						{!! Form::submit('Agregar', ['class' => 'btn btn-success']) !!}
						<a href="{{ route('comprobanteEdit', $movimiento->id) }}" class="btn btn-danger pull-right">Volver al comprobante</a>
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
		var impuestos = [
			<?php
			foreach ($impuestos as $impuesto) {
				?>
				{
					"tipo": "{{ $impuesto->tipo }}",
					"id": {{ $impuesto->id }},
					"nombre": "{{ $impuesto->nombre }}",
					"conceptos": [
						<?php
							foreach ($impuesto->conceptosImpuestos as $concepto) {
								if ($concepto->esta_activo) {
									?>
									{
										"id": "{{ $concepto->id }}",
										"nombre": "{{ $concepto->nombre . ' - ' . number_format($concepto->tasa, 2) . '%' }}"
									},
									<?php
								}
							}
						?>
					]
				},
				<?php
			}
			?>
		];
		$("input[name='tipoImpuesto']").on("change", function(event){
			var tp = $("input[name='tipoImpuesto']:checked").val();
			llenarImpuesto(tp);
		});

		function llenarImpuesto(tipo) {
			$("select[name='impuesto']").empty().val(null).trigger("change");
			$("select[name='concepto']").empty().val(null).trigger("change");
			$.each(impuestos, function(){
				if(this.tipo == tipo) {
					$("select[name='impuesto']").append(
						$("<option/>").val(this.id).text(this.nombre)
					).val(null).trigger("change");
				}
			});
		}

		$(".eliminarImpuesto").click(function(event){
			event.preventDefault();
			var row = $(this).parents("tr");
			var url = "{{ url('comprobante', $movimiento->id) }}/" + row.data("id");
			$.ajax({
				url: url,
				type: 'DELETE',
				dataType: 'json',
				data: "_token={{ csrf_token() }}"
			}).done(function(data){
				row.fadeOut(500);
			}).fail(function(data) {
			});
		});

		$("select[name='impuesto']").on("change", function(){
			impuesto =  $(this).val();
			if (impuesto == null || impuesto.length == 0) {
				return;
			}
			$("select[name='concepto']").empty();
			$.each(impuestos, function(){
				if(this.id == impuesto) {
					$.each(this.conceptos, function(){
						$("select[name='concepto']").append(
							$("<option/>").val(this.id).text(this.nombre)
						).val(null).trigger("change");
					});
				}
			});
		});
		$(".select2").select2();
		$("select[name='tercero']").select2({
			allowClear: true,
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
		llenarImpuesto("NACIONAL");
	});
</script>
@endpush

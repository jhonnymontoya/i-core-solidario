@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
		{!! Form::open(['url' => ['comprobante', $movimiento, 'impuesto'], 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Agregar impuesto</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								<label class="control-label">Tipo Impuesto</label>
								<div>
									@php
										$valid = $errors->has('tipoImpuesto') ? 'is-invalid' : '';
										$tipoImpuesto = empty(old('tipoImpuesto')) ? 'NACIONAL' : old('tipoImpuesto');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $tipoImpuesto ? 'active' : '' }}">
											{!! Form::radio('tipoImpuesto', 'NACIONAL', ($tipoImpuesto ? true : false), ['class' => [$valid]]) !!}Nacional
										</label>
										<label class="btn btn-primary {{ !$tipoImpuesto ? 'active' : '' }}">
											{!! Form::radio('tipoImpuesto', 'REGIONAL', (!$tipoImpuesto ? true : false ), ['class' => [$valid]]) !!}Regional
										</label>
										<label class="btn btn-primary {{ !$tipoImpuesto ? 'active' : '' }}">
											{!! Form::radio('tipoImpuesto', 'DISTRITAL', (!$tipoImpuesto ? true : false ), ['class' => [$valid]]) !!}Distrital
										</label>
									</div>
									@if ($errors->has('tipoImpuesto'))
										<div class="invalid-feedback">{{ $errors->first('tipoImpuesto') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								@php
									$valid = $errors->has('impuesto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Impuesto</label>
								{!! Form::select('impuesto', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('impuesto'))
									<div class="invalid-feedback">{{ $errors->first('impuesto') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								@php
									$valid = $errors->has('concepto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Concepto</label>
								{!! Form::select('concepto', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('concepto'))
									<div class="invalid-feedback">{{ $errors->first('concepto') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								@php
									$valid = $errors->has('tercero') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tercero</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-male"></i>
										</span>
									</div>
									{!! Form::select('tercero', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('tercero'))
										<div class="invalid-feedback">{{ $errors->first('tercero') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								@php
									$valid = $errors->has('base') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Base</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('base', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'Base', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'false']) !!}
									@if ($errors->has('base'))
										<div class="invalid-feedback">{{ $errors->first('base') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4 col-sm-12">
							<div class="form-group">
								@php
									$valid = $errors->has('iva') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">I.V.A.</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('iva', null, ['class' => [$valid, 'form-control', 'text-right'], 'autocomplete' => 'off', 'placeholder' => 'I.V.A.', 'data-maskMoney', 'data-allownegative' => 'true', 'data-allowzero' => 'true']) !!}
									@if ($errors->has('iva'))
										<div class="invalid-feedback">{{ $errors->first('iva') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-hover">
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
											<td class="text-right">{{ number_format($imp->tasa, 3) }}%</td>
											<td class="text-right">${{ number_format($imp->valor_impuesto, 2) }}</td>
											<td class="text-right">${{ number_format($imp->iva, 0) }}</td>
											<td>
												<a href="#" class="btn btn-outline-danger btn-sm eliminarImpuesto">
													<i class="far fa-trash-alt"></i>
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ route('comprobanteEdit', $movimiento->id) }}" class="btn btn-outline-danger pull-right">Volver al comprobante</a>
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
										"nombre": "{{ $concepto->nombre . ' - ' . number_format($concepto->tasa, 3) . '%' }}"
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

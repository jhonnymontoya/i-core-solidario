@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Duplicar comprobante
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		{!! Form::model($comprobante, ['url' => ['comprobante', $comprobante, 'duplicar'], 'method' => 'post', 'role' => 'form', 'id' => 'formDuplicar']) !!}
		<div class="container-fluid">
			<div class="card card-warning card-outline">
				<div class="card-header with-border">
					@php
						$tipoComprobante = $comprobante->tipoComprobante;
						$numero = $tipoComprobante->codigo;
						if (!empty($comprobante->numero_comprobante)) {
							$numero .= ' ' . $comprobante->numero_comprobante;
						}
					@endphp
					<h3 class="card-title">Duplicar comprobante {{ $numero }}</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<div class="row">
						<div class="col-md-4 col-md-offset-1">
							<dl class="dl-horizontal">
								<dt>Tipo comprobante:</dt>
								<dd>{{ $tipoComprobante->nombre_completo }}</dd>

								<dt>Fecha comprobante:</dt>
								<dd>{{ $comprobante->fecha_movimiento }} ({{ $comprobante->fecha_movimiento->diffForHumans() }})</dd>

								<dt>Número registros:</dt>
								<dd>{{ number_format($comprobante->detalleMovimientos->count()) }}</dd>
							</dl>
						</div>

						<div class="col-md-3">
							<dl class="dl-horizontal">
								<dt>Numero comprobante:</dt>
								<dd>{{ $comprobante->numero_comprobante }}</dd>

								<dt>Valor:</dt>
								<dd>${{ number_format($comprobante->debitos) }}</dd>
							</dl>
						</div>
					</div>
					<div class="row">
						<div class="col-md-11 col-md-offset-1">
							<h4>Descripción:</h4>
							<p>{{ $comprobante->descripcion }}</p>
						</div>
					</div>
					<br>
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tipo_comprobante_id')?'has-error':'') }}">
								<label class="col-sm-4 control-label">
									@if ($errors->has('tipo_comprobante_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo de Comprobante
								</label>
								<div class="col-sm-8">
									{!! Form::select('tipo_comprobante_id', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione un tipo de comprobante']) !!}
									@if ($errors->has('tipo_comprobante_id'))
										<span class="help-block">{{ $errors->first('tipo_comprobante_id') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('fecha_movimiento')?'has-error':'') }}">
								<label class="col-sm-3 control-label">
									@if ($errors->has('fecha_movimiento'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha
								</label>
								<div class="col-sm-9">
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('fecha_movimiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_movimiento'))
										<span class="help-block">{{ $errors->first('fecha_movimiento') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row form-horizontal">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="col-sm-2 control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								<div class="col-sm-10">
									{!! Form::text('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
									@if ($errors->has('descripcion'))
										<span class="help-block">{{ $errors->first('descripcion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr>
					<br>
					<h3>Registros</h3>
					<div class="row">
						<div class="col-md-12">
							<table class="table table-responsive" id="tablaRegistros">
								<thead>
									<tr>
										<th>Tercero</th>
										<th>Cuenta</th>
										<th>Referencia</th>
										<th class="text-center">debito</th>
										<th class="text-center">debito</th>
									</tr>
								</thead>
								<tbody>
									<?php
										$detalles = $comprobante->detalleMovimientos()->orderBy("serie")->get();
										foreach ($detalles as $registro) {
											$t = number_format($registro->tercero_identificacion);
											$t .= ' ' . $registro->tercero;
											$codigo = $registro->cuif_codigo . ' ' . $registro->cuif_nombre;
											?>
											<tr>
												<td>{{ $t }}</td>
												<td>{{ $codigo }}</td>
												<td>{{ $registro->referencia }}</td>
												<td class="text-right">${{ number_format($registro->debito) }}</td>
												<td class="text-right">${{ number_format($registro->credito) }}</td>
											</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					<a class="btn btn-success" id="duplicar">Duplicar</a>
					<a href="{{ url('comprobante') }}" class="btn btn-danger pull-right" tabindex="2">Cancelar</a>
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
		@php
			$tipoComprobante = $comprobante->tipo_comprobante_id;
			if (!empty(old('tipo_comprobante_id'))) {
				$tipoComprobante = old('tipo_comprobante_id');
			}
		@endphp
		$("select[name='tipo_comprobante_id']").selectAjax("{{ url('api/tipoComprobante') }}", {entidad: {{ Auth::getSession()->get('entidad')->id }}, id:"{{ $tipoComprobante }}"});
		$("#duplicar").click(function(){
			$("#duplicar").addClass("disabled");
			$("#duplicar").text("Duplicando...");
			$("#formDuplicar").submit();
		});
		$(document).ready(function() {
			$('#tablaRegistros').DataTable({
				"scrollCollapse": true,
				"paging": true,
				"ordering": false,
				"info": false,
				"searching": false
			});
		});
	});
</script>
@endpush

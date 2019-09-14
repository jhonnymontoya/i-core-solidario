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
						<div class="col-md-6">
							<dl class="row">
								<dt class="col-md-4">Tipo comprobante:</dt>
								<dd class="col-md-8">{{ $tipoComprobante->nombre_completo }}</dd>

								<dt class="col-md-4">Fecha comprobante:</dt>
								<dd class="col-md-8">{{ $comprobante->fecha_movimiento }} ({{ $comprobante->fecha_movimiento->diffForHumans() }})</dd>

								<dt class="col-md-4">Número registros:</dt>
								<dd class="col-md-8">{{ number_format($comprobante->detalleMovimientos->count()) }}</dd>
							</dl>
						</div>

						<div class="col-md-6">
							<dl class="row">
								<dt class="col-md-5">Numero comprobante:</dt>
								<dd class="col-md-7">{{ $comprobante->numero_comprobante }}</dd>

								<dt class="col-md-5">Valor:</dt>
								<dd class="col-md-7">${{ number_format($comprobante->debitos) }}</dd>
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
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_comprobante_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo de comprobante</label>
								{!! Form::select('tipo_comprobante_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('tipo_comprobante_id'))
									<div class="invalid-feedback">{{ $errors->first('tipo_comprobante_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_movimiento') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_movimiento', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_movimiento'))
										<div class="invalid-feedback">{{ $errors->first('fecha_movimiento') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row form-horizontal">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::text('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>
					<hr>
					<br>
					<h3>Registros</h3>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-striped table-hover" id="tablaRegistros">
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
				<div class="card-footer text-right">
					<a href="#" class="btn btn-outline-success" id="duplicar">Duplicar</a>
					<a href="{{ url('comprobante') }}" class="btn btn-outline-danger pull-right" tabindex="2">Cancelar</a>
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
		$("#duplicar").click(function(e){
			e.preventDefault();
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Conceptos de recaudo
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Conceptos de recaudo</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($conceptoRecaudo, ['url' => ['conceptosRecaudos', $conceptoRecaudo], 'method' => 'put', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Editar concepto de recaudo</h3>
			</div>
			<div class="card-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('pagaduria_id')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('pagaduria_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Pagaduria
							</label>
							{!! Form::text('pagaduria_id', $conceptoRecaudo->pagaduria->nombre, ['class' => 'form-control', 'placeholder' => 'Seleccione pagaduría', 'readonly']) !!}
							@if ($errors->has('pagaduria_id'))
								<span class="help-block">{{ $errors->first('pagaduria_id') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('codigo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Código concepto
							</label>
							{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Código del concepto', 'autofocus']) !!}
							@if ($errors->has('codigo'))
								<span class="help-block">{{ $errors->first('codigo') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-6">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del concepto']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>
				</div>
				<br><br>
				<div class="row">
					<div class="col-md-12">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('conceptosRecaudos') }}" class="btn btn-danger pull-right">Volver</a>
					</div>
				</div>

				<br>
				<ul class="nav nav-tabs" role="tablist">
					<li role="presentation" class="active">
						<a href="#ahorros" aria-controls="ahorros" role="tab" data-toggle="tab">Ahorros</a>
					</li>
					<li role="presentation">
						<a href="#creditos" aria-controls="creditos" role="tab" data-toggle="tab">Créditos</a>
					</li>
				</ul>

				<div class="tab-content">
					<div role="tabpanel" class="tab-pane fade in active" id="ahorros">
						<br>
						<div class="row">
							<div class="col-md-10 col-md-offset-1 table-responsive">
								@if($modalidadesAhorros->count())
									<table class="table">
										<thead>
											<tr>
												<th>Código</th>
												<th>Nombre</th>
												<th>Concepto actual</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach($modalidadesAhorros as $modalidad)
												<?php
													$esConceptoActual = false;
													$concepto = null;
													foreach($modalidad->conceptosRecaudos as $item)
													{
														if($item->pagaduria->id == $conceptoRecaudo->pagaduria->id)
														{
															$concepto = $item;
															if($item->id == $conceptoRecaudo->id)
															{
																$esConceptoActual = true;
															}
															break;
														}
													}
												?>
												<tr data-id="{{ $modalidad->id }}">
													<td>{{ $modalidad->codigo }}</td>
													<td>{{ $modalidad->nombre }}</td>
													<td class="conceptoActual">{{ !empty($concepto) ? $concepto->codigo . ' - ' . $concepto->nombre : '' }}</td>
													<td>
														<a class="btn btn-xs btn-success {{ $esConceptoActual ? 'disabled' : ''}}" onclick="javascript:asociarModalidadAhorros(this);">Asociar</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								@else
									<p>No se encontraró modalidades de ahorros</p>
								@endif
							</div>
						</div>
					</div>
					<div role="tabpanel" class="tab-pane fade" id="creditos">
						<br>
						<div class="row">
							<div class="col-md-10 col-md-offset-1 table-responsive">
								@if($modalidadesCreditos->count())
									<table class="table">
										<thead>
											<tr>
												<th>Código</th>
												<th>Nombre</th>
												<th>Concepto actual</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach($modalidadesCreditos as $modalidad)
												<?php
													$esConceptoActual = false;
													$concepto = null;
													foreach($modalidad->conceptosRecaudos as $item)
													{
														if($item->pagaduria->id == $conceptoRecaudo->pagaduria->id)
														{
															$concepto = $item;
															if($item->id == $conceptoRecaudo->id)
															{
																$esConceptoActual = true;
															}
															break;
														}
													}
												?>
												<tr data-id="{{ $modalidad->id }}">
													<td>{{ $modalidad->codigo }}</td>
													<td>{{ $modalidad->nombre }}</td>
													<td class="conceptoActual">{{ !empty($concepto) ? $concepto->codigo . ' - ' . $concepto->nombre : '' }}</td>
													<td>
														<a class="btn btn-xs btn-success {{ $esConceptoActual ? 'disabled' : ''}}" onclick="javascript:asociarModalidadCredito(this);">Asociar</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								@else
									<p>No se encontraró modalidades de ahorros</p>
								@endif
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card-footer">
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
	});
	function asociarModalidadAhorros(modalidad)
	{
		var $modalidad = $(modalidad).parent().parent();
		var $idModalidad = $modalidad.data('id');

		$.ajax({
			url: '{{ route('conceptosRecaudosAdociarAhorro', $conceptoRecaudo->id) }}',
			type: 'POST',
			data: '_token={{ csrf_token() }}&modalidadAhorroId=' + $idModalidad
		}).done(function(data){
			$modalidad.find('.conceptoActual').text(data.concepto);
			$modalidad.find('.btn').addClass('disabled');
		}).fail(function(data){});
	}

	function asociarModalidadCredito(modalidad)
	{
		var $modalidad = $(modalidad).parent().parent();
		var $idModalidad = $modalidad.data('id');

		$.ajax({
			url: '{{ route('conceptosRecaudosAdociarCredito', $conceptoRecaudo->id) }}',
			type: 'POST',
			data: '_token={{ csrf_token() }}&modalidadCreditoId=' + $idModalidad
		}).done(function(data){
			$modalidad.find('.conceptoActual').text(data.concepto);
			$modalidad.find('.btn').addClass('disabled');
		}).fail(function(data){});
	}
</script>
@endpush

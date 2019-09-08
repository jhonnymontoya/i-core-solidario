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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($conceptoRecaudo, ['url' => ['conceptosRecaudos', $conceptoRecaudo], 'method' => 'put', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar concepto de recaudo</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('pagaduria_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Pagaduría</label>
								{!! Form::text('pagaduria_id', $conceptoRecaudo->pagaduria->nombre, ['class' => 'form-control', 'placeholder' => 'Seleccione pagaduría', 'readonly']) !!}
								@if ($errors->has('pagaduria_id'))
									<div class="invalid-feedback">{{ $errors->first('pagaduria_id') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código concepto</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código concepto', 'autofocus']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
					</div>
					<br><br>
					<div class="row">
						<div class="col-md-12 text-right">
							{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
							<a href="{{ url('conceptosRecaudos') }}" class="btn btn-outline-danger">Volver</a>
						</div>
					</div>

					<br>
					<ul class="nav nav-pills mb-3" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" data-toggle="pill" href="#ahorros" role="tab">Ahorros</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" data-toggle="pill" href="#creditos" role="tab">Crédito</a>
						</li>
					</ul>
					<div class="tab-content" id="pills-tabContent">
						<div class="tab-pane fade show active" id="ahorros" role="tabpanel">
							<br>
							<div class="row">
								<div class="col-md-12 table-responsive">
									@if($modalidadesAhorros->count())
										<table class="table table-striped table-hover">
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
															<a class="btn btn-sm btn-outline-success {{ $esConceptoActual ? 'disabled' : ''}}" onclick="javascript:asociarModalidadAhorros(this);">Asociar</a>
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
						<div class="tab-pane fade" id="creditos" role="tabpanel">
							<br>
							<div class="row">
								<div class="col-md-12 table-responsive">
									@if($modalidadesCreditos->count())
										<table class="table table-striped table-hover">
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
															<a class="btn btn-sm btn-outline-success {{ $esConceptoActual ? 'disabled' : ''}}" onclick="javascript:asociarModalidadCredito(this);">Asociar</a>
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
				{!! Form::close() !!}
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Editar socio</li>
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

		<div class="container-fluid">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'obligacionesFinancieras'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEdit', $socio->id) }}">General</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditLaboral', $socio->id) }}">Laboral</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditImagenes', $socio->id) }}">Imagen</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditFinanciera', $socio->id) }}">Financiera</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								<div class="col-md-12">
									<h4>Información de obligaciones financieras</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('banco') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Banco</label>
										{!! Form::select('banco', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('banco'))
											<div class="invalid-feedback">{{ $errors->first('banco') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('monto') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Monto</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('monto', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Monto', 'data-maskMoney', 'autofocus']) !!}
											@if ($errors->has('monto'))
												<div class="invalid-feedback">{{ $errors->first('monto') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-4">
									<div class="form-group">
										@php
											$valid = $errors->has('tasa_mes_vencido') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tasa mes vencido</label>
										<div class="input-group">
											{!! Form::text('tasa_mes_vencido', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tasa mes vencido']) !!}
											<div class="input-group-append">
												<span class="input-group-text">%</span>
											</div>
											@if ($errors->has('tasa_mes_vencido'))
												<div class="invalid-feedback">{{ $errors->first('tasa_mes_vencido') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-4">
									<div class="form-group">
										@php
											$valid = $errors->has('plazo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Plazo meses</label>
										{!! Form::text('plazo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Plazo meses', 'data-mask' => '000']) !!}
										@if ($errors->has('plazo'))
											<div class="invalid-feedback">{{ $errors->first('plazo') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-4">
									<div class="form-group">
										@php
											$valid = $errors->has('fecha_inicial') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Fecha inicial</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-calendar"></i>
												</span>
											</div>
											{!! Form::text('fecha_inicial', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
											@if ($errors->has('fecha_inicial'))
												<div class="invalid-feedback">{{ $errors->first('fecha_inicial') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							<br><br>
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-12">
									@if($obligaciones->total() > 0)
										<div class="table-responsive">
											<table class="table table-hover" id="id_beneficiario">
												<thead>
													<tr>
														<th>Banco</th>
														<th>Tasa</th>
														<th>Plazo</th>
														<th>Fecha inicial</th>
														<th>Monto</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$monto = 0;
													?>
													@foreach ($obligaciones as $obligacion)
														<tr data-id='{{ $obligacion->id }}'>
															<td>{{ $obligacion->banco->nombre }}</td>
															<td>{{ number_format($obligacion->tasa_mes_vencido, 2) }}%</td>
															<td>{{ number_format($obligacion->plazo, 0) }}</td>
															<td>{{ $obligacion->fecha_inicial }} ({{ !empty($obligacion->fecha_inicial) ? $obligacion->fecha_inicial->diffForHumans() : 'No especificado'}})</td>
															<td>${{ number_format($obligacion->monto) }}</td>
															<td>
																<a href="{{ route('socioEditObligacionesFinancierasEliminar', [$socio->id, $obligacion->id]) }}" class="btn btn-outline-danger btn-sm">
																	<i class="fa fa-trash"></i>
																</a>
															</td>
														</tr>
														<?php
															$monto += $obligacion->monto;
														?>
													@endforeach
												</tbody>
												<tfoot>
													<tr>
														<th>Banco</th>
														<th>Tasa</th>
														<th>Plazo</th>
														<th>Fecha inicial</th>
														<th>Monto</th>
														<th></th>
													</tr>
													<tr>
														<th colspan="3"></th>
														<th class="pull-right">Totales:</th>
														<th>${{ number_format($monto) }}</th>
													</tr>
												</tfoot>
											</table>
										</div>
									@else
										<div class="col-md-12">
											<h3>No tiene obligaciones de financieras</h3>
										</div>
									@endif
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							<div class="row">
								<div class="col-md-12 text-center">
									{!! $obligaciones->render() !!}
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('socio') }}" class="btn btn-outline-danger">Volver</a>
					<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-outline-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
				</div>
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
		$("select[name='banco']").selectAjax("{{ url('banco/listar') }}", {id:"{{ old('banco') }}"});

		$(window).load(function(){
			@if(old('cupo'))
			$("input[name='cupo']").maskMoney('mask');
			@endif
			@if(old('saldo'))
			$("input[name='saldo']").maskMoney('mask');
			@endif
		});
	});
</script>
@endpush

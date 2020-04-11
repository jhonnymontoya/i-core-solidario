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
			{!! Form::model($socio, ['url' => ['socio', $socio, 'tarjetasCredito'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
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
							<a class="nav-link active" href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a>
						</li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								<div class="col-md-12">
									<h4>Información de tarjetas de crédito</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('franquicia') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Franquicia</label>
										{!! Form::select('franquicia', $franquicias, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('franquicia'))
											<div class="invalid-feedback">{{ $errors->first('franquicia') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
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
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('anio') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Año vencimiento</label>
										{!! Form::text('anio', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Año vencimiento', 'data-mask' => '0000', 'autofocus']) !!}
										@if ($errors->has('anio'))
											<div class="invalid-feedback">{{ $errors->first('anio') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('mes') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Mes vencimiento</label>
										{!! Form::text('mes', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mes vencimiento', 'data-mask' => '00']) !!}
										@if ($errors->has('mes'))
											<div class="invalid-feedback">{{ $errors->first('mes') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('cupo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Cupo</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('cupo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Cupo', 'data-maskMoney', 'autofocus']) !!}
											@if ($errors->has('cupo'))
												<div class="invalid-feedback">{{ $errors->first('cupo') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group">
										@php
											$valid = $errors->has('saldo') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Saldo</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">$</span>
											</div>
											{!! Form::text('saldo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Saldo', 'data-maskMoney']) !!}
											@if ($errors->has('saldo'))
												<div class="invalid-feedback">{{ $errors->first('saldo') }}</div>
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
									@if($tarjetas->total() > 0)
										<div class="table-responsive">
											<table class="table table-hover" id="id_beneficiario">
												<thead>
													<tr>
														<th>Franquicia</th>
														<th>Banco</th>
														<th>Año vencimiento</th>
														<th>Mes vencimiento</th>
														<th>Cupo</th>
														<th>Saldo</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													<?php
														$cupo = 0;
														$saldo = 0;
													?>
													@foreach ($tarjetas as $tarjeta)
														<tr data-id='{{ $tarjeta->id }}'>
															<td>{{ $tarjeta->franquicia->nombre }}</td>
															<td>{{ $tarjeta->banco->nombre }}</td>
															<td>{{ $tarjeta->anio_vencimiento }}</td>
															<td>{{ $tarjeta->mes_vencimiento }}</td>
															<td>${{ number_format($tarjeta->cupo) }}</td>
															<td>${{ number_format($tarjeta->saldo) }}</td>
															<td>
																<a href="{{ route('socioEditTarjetasCreditoEliminar', [$socio->id, $tarjeta->id]) }}" class="btn btn-outline-danger btn-sm">
																	<i class="fa fa-trash"></i>
																</a>
															</td>
														</tr>
														<?php
															$cupo += $tarjeta->cupo;
															$saldo += $tarjeta->saldo;
														?>
													@endforeach
												</tbody>
												<tfoot>
													<tr>
														<th>Franquicia</th>
														<th>Banco</th>
														<th>Año vencimiento</th>
														<th>Mes vencimiento</th>
														<th>Cupo</th>
														<th>Saldo</th>
														<th></th>
													</tr>
													<tr>
														<th colspan="3"></th>
														<th class="pull-right">Totales:</th>
														<th>${{ number_format($cupo) }}</th>
														<th>${{ number_format($saldo) }}</th>
													</tr>
												</tfoot>
											</table>
										</div>
									@else
										<div class="col-md-12">
											<h3>No tiene tarjetas de crédito</h3>
										</div>
									@endif
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							<div class="row">
								<div class="col-md-12 text-center">
									{!! $tarjetas->render() !!}
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

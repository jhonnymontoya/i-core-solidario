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
									<div class="form-group {{ ($errors->has('franquicia')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('franquicia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Franquicia
										</label>
										<div class="col-sm-8">
											{!! Form::select('franquicia', $franquicias, null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('franquicia'))
												<span class="help-block">{{ $errors->first('franquicia') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('banco')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('banco'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Banco
										</label>
										<div class="col-sm-9">
											{!! Form::select('banco', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('banco'))
												<span class="help-block">{{ $errors->first('banco') }}</span>
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
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('anio')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('anio'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Año vencimiento
										</label>
										<div class="col-sm-8">
											{!! Form::text('anio', null, ['class' => 'form-control', 'placeholder' => 'Año vencimiento', 'autocomplete' => 'off', 'data-mask' => '0000', 'autofocus']) !!}
											@if ($errors->has('anio'))
												<span class="help-block">{{ $errors->first('anio') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('mes')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('mes'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Mes vencimiento
										</label>
										<div class="col-sm-8">
											{!! Form::text('mes', null, ['class' => 'form-control', 'placeholder' => 'Mes vencimiento', 'autocomplete' => 'off', 'data-mask' => '00']) !!}
											@if ($errors->has('mes'))
												<span class="help-block">{{ $errors->first('mes') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('cupo')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('cupo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Cupo
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('cupo', null, ['class' => 'form-control', 'placeholder' => 'Cupo', 'autocomplete' => 'off', 'data-maskMoney', 'autofocus']) !!}
											</div>
											@if ($errors->has('cupo'))
												<span class="help-block">{{ $errors->first('cupo') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('saldo')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('saldo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Saldo
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('saldo', null, ['class' => 'form-control', 'placeholder' => 'Saldo', 'autocomplete' => 'off', 'data-maskMoney']) !!}
											</div>
											@if ($errors->has('saldo'))
												<span class="help-block">{{ $errors->first('saldo') }}</span>
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

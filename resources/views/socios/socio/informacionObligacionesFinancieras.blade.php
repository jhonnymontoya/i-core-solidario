@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Editar socio
			<small>Socios</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Socios</a></li>
			<li class="active">Socio</li>
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

		<div class="row">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'obligacionesFinancieras'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'data-maskMoney-removeMask']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
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
									<div class="form-group {{ ($errors->has('banco')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('banco'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Banco
										</label>
										<div class="col-sm-8">
											{!! Form::select('banco', [], null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('banco'))
												<span class="help-block">{{ $errors->first('banco') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('monto')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('monto'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Monto
										</label>
										<div class="col-sm-9">
											<div class="input-group">
												<span class="input-group-addon">$</span>
												{!! Form::text('monto', null, ['class' => 'form-control', 'placeholder' => 'Monto', 'autocomplete' => 'off', 'data-maskMoney', 'autofocus']) !!}
											</div>
											@if ($errors->has('monto'))
												<span class="help-block">{{ $errors->first('monto') }}</span>
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
									<div class="form-group {{ ($errors->has('tasa_mes_vencido')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tasa_mes_vencido'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tasa mes vencido
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon">%</span>
												{!! Form::text('tasa_mes_vencido', null, ['class' => 'form-control', 'placeholder' => 'Tasa mes vencido', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('tasa_mes_vencido'))
												<span class="help-block">{{ $errors->first('tasa_mes_vencido') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('plazo')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('plazo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Plazo meses
										</label>
										<div class="col-sm-8">
											{!! Form::text('plazo', null, ['class' => 'form-control', 'placeholder' => 'Plazo en meses', 'autocomplete' => 'off', 'data-mask' => '000']) !!}
											@if ($errors->has('plazo'))
												<span class="help-block">{{ $errors->first('plazo') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('fecha_inicial')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('fecha_inicial'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha inicial
										</label>
										<div class="col-sm-8">
											<div class="input-group">
												<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
												{!! Form::text('fecha_inicial', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
											</div>
											@if ($errors->has('fecha_inicial'))
												<span class="help-block">{{ $errors->first('fecha_inicial') }}</span>
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
																<a href="{{ route('socioEditObligacionesFinancierasEliminar', [$socio->id, $obligacion->id]) }}" class="btn btn-danger btn-xs">
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
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('socio') }}" class="btn btn-danger">Volver</a>
									<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} pull-right {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
								</div>
							</div>
						</div>
					</div>
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

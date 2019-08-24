@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Modalidades de créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Modalidades de créditos</li>
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
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'amortizacion'], 'method' => 'put', 'role' => 'form']) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar modalidad</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código
								</label>
								{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
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
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('es_exclusivo_de_socios')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('es_exclusivo_de_socios'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Exclusiva para socios?
								</label>
								<br>
								<?php
									$es_exclusivo_de_socios = $modalidad->es_exclusivo_de_socios;
									if(old('es_exclusivo_de_socios') == '0')
									{
										$es_exclusivo_de_socios = false;
									}
									elseif(old('es_exclusivo_de_socios') == '1')
									{
										$es_exclusivo_de_socios = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $es_exclusivo_de_socios ? 'active' : ''}}">
										{!! Form::radio('es_exclusivo_de_socios', '1', $es_exclusivo_de_socios ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ $es_exclusivo_de_socios ? '' : 'active'}}">
										{!! Form::radio('es_exclusivo_de_socios', '0', $es_exclusivo_de_socios? false : true) !!}No
									</label>
								</div>
								@if ($errors->has('es_exclusivo_de_socios'))
									<span class="help-block">{{ $errors->first('es_exclusivo_de_socios') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<br>
								<?php
									$esta_activa = $modalidad->esta_activa;
									if(old('esta_activa') == '0')
									{
										$esta_activa = false;
									}
									elseif(old('esta_activa') == '1')
									{
										$esta_activa = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $esta_activa ? 'active' : ''}}">
										{!! Form::radio('esta_activa', '1', $esta_activa ? true : false) !!}Activa
									</label>
									<label class="btn btn-outline-danger {{ $esta_activa ? '' : 'active'}}">
										{!! Form::radio('esta_activa', '0', $esta_activa? false : true) !!}Inactiva
									</label>
								</div>
								@if ($errors->has('esta_activa'))
									<span class="help-block">{{ $errors->first('esta_activa') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								{!! Form::textarea('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<span class="help-block">{{ $errors->first('descripcion') }}</span>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li role="presentation" class="active">
							<a href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active">
							<br>
							<div class="row form-horizontal">
								<div class="col-md-12">
									<div class="form-group {{ ($errors->has('tipo_cuota')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_cuota'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo de cuota
										</label>
										<div class="col-sm-8">
											<?php
												$tipoCuota = empty($modalidad->tipo_cuota) ? 'FIJA' : $modalidad->tipo_cuota;
												if(old('tipo_cuota') == 'FIJA')
												{
													$tipoCuota = 'FIJA';
												}
												elseif(old('tipo_cuota') == '1')
												{
													$tipoCuota = 'CAPITAL';
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $tipoCuota == 'FIJA' ? 'active' : ''}}">
													{!! Form::radio('tipo_cuota', 'FIJA', $tipoCuota == 'FIJA' ? true : false, ['class' => 'radio']) !!}Fija compuesta
												</label>
												<label class="btn btn-outline-primary {{ $tipoCuota == 'FIJA' ? '' : 'active'}}">
													{!! Form::radio('tipo_cuota', 'CAPITAL', $tipoCuota == 'FIJA' ? false : true) !!}Fija capital
												</label>
											</div>
											@if ($errors->has('tipo_cuota'))
												<span class="help-block">{{ $errors->first('tipo_cuota') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-2"></div>
								<div class="col-md-10">
									<div class="form-group {{ ($errors->has('periodicidades_admitidas')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('periodicidades_admitidas'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Periodicidades de pago admitidas
										</label>
										<br>
										<div class="btn-group" data-toggle="buttons">
											<?php
												$amortizacion = $modalidad->acepta_pago_semanal;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('semanal', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'semanal', $amortizacion ? true : false) !!}Semanal
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_decadal;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('decadal', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'decadal', $amortizacion ? true : false) !!}Decadal
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_catorcenal;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('catorcenal', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'catorcenal', $amortizacion ? true : false) !!}Catorcenal
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_quincenal;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('quincenal', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'quincenal', $amortizacion ? true : false) !!}Quincenal
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_mensual;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('mensual', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'mensual', $amortizacion ? true : false) !!}Mensual
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_bimestral;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('bimestral', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'bimestral', $amortizacion ? true : false) !!}Bimestral
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_trimestral;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('trimestral', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'trimestral', $amortizacion ? true : false) !!}Trimestral
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_cuatrimestral;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('cuatrimestral', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'cuatrimestral', $amortizacion ? true : false) !!}Cuatrimestral
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_semestral;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('semestral', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'semestral', $amortizacion ? true : false) !!}Semestral
											</label>
											<?php
												$amortizacion = $modalidad->acepta_pago_anual;
												if(!empty(old('periodicidades_admitidas'))){
													$amortizacion = array_search('anual', old('periodicidades_admitidas')) !== false ? true : false;
												}
											?>
											<label class="btn btn-outline-primary {{ $amortizacion ? 'active' : '' }}">
												{!! Form::checkbox('periodicidades_admitidas[]', 'anual', $amortizacion ? true : false) !!}Anual
											</label>
										</div>
										@if ($errors->has('periodicidades_admitidas'))
											<span class="help-block">{{ $errors->first('periodicidades_admitidas') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row form-horizontal">
								<div class="col-md-12">
									<div class="form-group {{ ($errors->has('acepta_cuotas_extraordinarias')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('acepta_cuotas_extraordinarias'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Acepta pagos extraordinarios programados?
										</label>
										<div class="col-sm-8">
											<?php
												$aceptaCuotaExtraordinaria = $modalidad->acepta_cuotas_extraordinarias;
												if(old('acepta_cuotas_extraordinarias') == '0')
												{
													$aceptaCuotaExtraordinaria = false;
												}
												elseif(old('acepta_cuotas_extraordinarias') == '1')
												{
													$aceptaCuotaExtraordinaria = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-outline-primary {{ $aceptaCuotaExtraordinaria ? 'active' : ''}}">
													{!! Form::radio('acepta_cuotas_extraordinarias', '1', $aceptaCuotaExtraordinaria ? true : false) !!}Sí
												</label>
												<label class="btn btn-outline-primary {{ $aceptaCuotaExtraordinaria ? '' : 'active'}}">
													{!! Form::radio('acepta_cuotas_extraordinarias', '0', $aceptaCuotaExtraordinaria? false : true) !!}No
												</label>
											</div>
											@if ($errors->has('acepta_cuotas_extraordinarias'))
												<span class="help-block">{{ $errors->first('acepta_cuotas_extraordinarias') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<br>
							<div class="row form-horizontal" id="aceptaCuotaExtraordinaria">
								<div class="col-sm-12">
									<div class="form-group {{ ($errors->has('maximo_porcentaje_pago_extraordinario')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('maximo_porcentaje_pago_extraordinario'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Porcentaje máximo de pago con cuotas extraordinarias
										</label>
										<div class="col-sm-5">
											{!! Form::number('maximo_porcentaje_pago_extraordinario', null, ['class' => 'form-control', 'placeholder' => 'Valor porcentaje', 'autocomplete' => 'off']) !!}
											@if ($errors->has('maximo_porcentaje_pago_extraordinario'))
												<span class="help-block">{{ $errors->first('maximo_porcentaje_pago_extraordinario') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('modalidadCredito') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	textarea{
		height: 150px !important;
	}
	@if(!$aceptaCuotaExtraordinaria)
		#aceptaCuotaExtraordinaria{
			display: none;
		}
	@endif
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$('input[name="acepta_cuotas_extraordinarias"]').change(function(){
			var aceptaCuotaExtraordinaria = ($(this).val() == '1' ? true : false);
			
			if(aceptaCuotaExtraordinaria)
			{
				$("#aceptaCuotaExtraordinaria").show(200);
			}
			else
			{
				$("#aceptaCuotaExtraordinaria").hide(200);
			}
		});
	});
</script>
@endpush

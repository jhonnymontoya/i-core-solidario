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
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'readonly']) !!}
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
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Exclusiva para socios?</label>
								<div>
									@php
										$valid = $errors->has('es_exclusivo_de_socios') ? 'is-invalid' : '';
										$exclusivoSocios = empty(old('es_exclusivo_de_socios')) ? $modalidad->es_exclusivo_de_socios : old('es_exclusivo_de_socios');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 1, ($exclusivoSocios ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 0, (!$exclusivoSocios ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('es_exclusivo_de_socios'))
										<div class="invalid-feedback">{{ $errors->first('es_exclusivo_de_socios') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Activa?</label>
								<div>
									@php
										$valid = $errors->has('esta_activa') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activa')) ? $modalidad->esta_activa : old('esta_activa');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::textarea('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditConsultaAsociado', $modalidad) }}">Consulta Asociado</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade show active">
							<br>
							<div class="row">
								<div class="col-md-12 text-center">
									<div class="form-group">
										<label class="control-label">¿Tipo de cuota?</label>
										<div>
											@php
												$valid = $errors->has('tipo_cuota') ? 'is-invalid' : '';
												$tipoCuota = empty(old('tipo_cuota')) ? $modalidad->tipo_cuota : old('tipo_cuota');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $tipoCuota == 'FIJA' ? 'active' : '' }}">
													{!! Form::radio('tipo_cuota', 'FIJA', ($tipoCuota == 'FIJA' ? true : false), ['class' => [$valid]]) !!}Fija compuesta
												</label>
												<label class="btn btn-primary {{ $tipoCuota == 'CAPITAL' ? 'active' : '' }}">
													{!! Form::radio('tipo_cuota', 'CAPITAL', ($tipoCuota == 'CAPITAL' ? true : false ), ['class' => [$valid]]) !!}Fija capital
												</label>
											</div>
											@if ($errors->has('tipo_cuota'))
												<div class="invalid-feedback">{{ $errors->first('tipo_cuota') }}</div>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 text-center">
									<div class="form-group">
										<label class="control-label">Periodicidades de pago admitidas</label>
										<div>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<?php
													$amortizacion = $modalidad->acepta_pago_semanal;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('semanal', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'semanal', $amortizacion ? true : false) !!}Semanal
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_decadal;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('decadal', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'decadal', $amortizacion ? true : false) !!}Decadal
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_catorcenal;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('catorcenal', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'catorcenal', $amortizacion ? true : false) !!}Catorcenal
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_quincenal;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('quincenal', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'quincenal', $amortizacion ? true : false) !!}Quincenal
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_mensual;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('mensual', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'mensual', $amortizacion ? true : false) !!}Mensual
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_bimestral;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('bimestral', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'bimestral', $amortizacion ? true : false) !!}Bimestral
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_trimestral;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('trimestral', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'trimestral', $amortizacion ? true : false) !!}Trimestral
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_cuatrimestral;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('cuatrimestral', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'cuatrimestral', $amortizacion ? true : false) !!}Cuatrimestral
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_semestral;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('semestral', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'semestral', $amortizacion ? true : false) !!}Semestral
												</label>
												<?php
													$amortizacion = $modalidad->acepta_pago_anual;
													if(!empty(old('periodicidades_admitidas'))){
														$amortizacion = array_search('anual', old('periodicidades_admitidas')) !== false ? true : false;
													}
												?>
												<label class="btn btn-primary {{ $amortizacion ? 'active' : '' }}">
													{!! Form::checkbox('periodicidades_admitidas[]', 'anual', $amortizacion ? true : false) !!}Anual
												</label>
											</div>
										</div>
										@if ($errors->has('periodicidades_admitidas'))
											<div class="invalid-feedback">{{ $errors->first('periodicidades_admitidas') }}</div>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 text-center">
									<div class="form-group">
										<label class="control-label">¿Acepta pagos extraordinarios programados?</label>
										<div>
											@php
												$valid = $errors->has('acepta_cuotas_extraordinarias') ? 'is-invalid' : '';
												$aceptaCuotaExtraordinaria = empty(old('acepta_cuotas_extraordinarias')) ? $modalidad->acepta_cuotas_extraordinarias : old('acepta_cuotas_extraordinarias');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $aceptaCuotaExtraordinaria ? 'active' : '' }}">
													{!! Form::radio('acepta_cuotas_extraordinarias', 1, ($aceptaCuotaExtraordinaria ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$aceptaCuotaExtraordinaria ? 'active' : '' }}">
													{!! Form::radio('acepta_cuotas_extraordinarias', 0, (!$aceptaCuotaExtraordinaria ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('acepta_cuotas_extraordinarias'))
												<div class="invalid-feedback">{{ $errors->first('acepta_cuotas_extraordinarias') }}</div>
											@endif
										</div>
									</div>
								</div>
							</div>

							<br>
							<div class="row" id="aceptaCuotaExtraordinaria">
								<div class="col-sm-12">
									<div class="form-group">
										@php
											$valid = $errors->has('maximo_porcentaje_pago_extraordinario') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Porcentaje máximo de pago con cuotas extraordinarias</label>
										{!! Form::number('maximo_porcentaje_pago_extraordinario', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor porcentaje']) !!}
										@if ($errors->has('maximo_porcentaje_pago_extraordinario'))
											<div class="invalid-feedback">{{ $errors->first('maximo_porcentaje_pago_extraordinario') }}</div>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
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

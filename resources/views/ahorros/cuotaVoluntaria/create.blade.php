@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas voluntarias
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Cuotas voluntarias</li>
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
		{!! Form::open(['url' => ['cuotaVoluntaria', $socio], 'method' => 'post', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Agregar nueva cuota voluntaria</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<p>{{ $socio->tercero->nombre_completo }}</p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('modalidad_ahorro_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Modalidad</label>
								{!! Form::select('modalidad_ahorro_id', $tiposCuotasVoluntarias, null, ['class' => [$valid, 'form-control', 'placeholder' => 'Seleccione modalidad']]) !!}
								@if ($errors->has('modalidad_ahorro_id'))
									<div class="invalid-feedback">{{ $errors->first('modalidad_ahorro_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('factor_calculo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Variable</label>
								{!! Form::select('factor_calculo', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor Fijo'], null, ['class' => [$valid, 'form-control']]) !!}
								@if ($errors->has('factor_calculo'))
									<div class="invalid-feedback">{{ $errors->first('factor_calculo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('valor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor</label>
								<div class="input-group">
									<div id="moneda" class="input-group-prepend" style="display: none;"><span class="input-group-text">$</span></div>
									{!! Form::number('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor', 'step' => '0.001']) !!}
									<div id="porcentaje" class="input-group-append"><span class="input-group-text">%</span></div>
									@if ($errors->has('valor'))
										<div class="invalid-feedback">{{ $errors->first('valor') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('periodicidad') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Periodicidad</label>
								{!! Form::select('periodicidad', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione periodicidad']) !!}
								@if ($errors->has('periodicidad'))
									<div class="invalid-feedback">{{ $errors->first('periodicidad') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('periodo_inicial') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha periodo inicial</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::select('periodo_inicial', $programaciones, null, ['class' => [$valid, 'form-control']]) !!}
									@if ($errors->has('periodo_inicial'))
										<div class="invalid-feedback">{{ $errors->first('periodo_inicial') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('periodo_final') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha periodo final</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('periodo_final', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('periodo_final'))
										<div class="invalid-feedback">{{ $errors->first('periodo_final') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('cuotaVoluntaria?socio=' . $socio->id) }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$(".select2").select2();
		$("select[name='factor_calculo']").on('change', function(){
			if($(this).find('option:selected').val() == 'VALORFIJO') {
				$("#moneda").show();
				$("#porcentaje").hide();
			}
			else {
				$("#moneda").hide();
				$("#porcentaje").show();
			}
		});

		if($("select[name='factor_calculo']").find('option:selected').val() == 'VALORFIJO') {
			$("#moneda").show();
			$("#porcentaje").hide();
		}
		else {
			$("#moneda").hide();
			$("#porcentaje").show();
		}
	});
</script>
@endpush

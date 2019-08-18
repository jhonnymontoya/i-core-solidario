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
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
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
								<div class="form-group {{ ($errors->has('modalidad_ahorro_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('modalidad_ahorro_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Modalidad
									</label>
									{!! Form::select('modalidad_ahorro_id', $tiposCuotasVoluntarias, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione modalidad', 'autofocus']) !!}
									@if ($errors->has('modalidad_ahorro_id'))
										<span class="help-block">{{ $errors->first('modalidad_ahorro_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('factor_calculo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('factor_calculo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Variable
									</label>
									{!! Form::select('factor_calculo', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor Fijo',], null, ['class' => 'form-control select2']) !!}
									@if ($errors->has('factor_calculo'))
										<span class="help-block">{{ $errors->first('factor_calculo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('valor'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Valor
									</label>
									<div class="input-group">
										<span id="moneda" class="input-group-addon" style="display: none;">$</span>
										{!! Form::number('valor', null, ['class' => 'form-control', 'step' => '0.001']) !!}
										<span id="porcentaje" class="input-group-addon">%</span>
									</div>
									@if ($errors->has('valor'))
										<span class="help-block">{{ $errors->first('valor') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('periodicidad')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('periodicidad'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Periodicidad
									</label>
									{!! Form::select('periodicidad', $periodicidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodicidad']) !!}
									@if ($errors->has('periodicidad'))
										<span class="help-block">{{ $errors->first('periodicidad') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('periodo_inicial')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('periodo_inicial'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha periodo inicial
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::select('periodo_inicial', $programaciones, null, ['class' => 'form-control pull-right select2']) !!}
									</div>
									@if ($errors->has('periodo_inicial'))
										<span class="help-block">{{ $errors->first('periodo_inicial') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('periodo_final')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('periodo_final'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha periodo final
									</label>
									<div class="input-group">
										<div class="input-group-addon">
											<i class="fa fa-calendar"></i>
										</div>
										{!! Form::text('periodo_final', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('periodo_final'))
										<span class="help-block">{{ $errors->first('periodo_final') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Agregar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('cuotaVoluntaria?socio=' . $socio->id) }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
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
			if($(this).find('option:selected').val() == 'VALORFIJO')
			{
				$("#moneda").show();
				$("#porcentaje").hide();
			}
			else
			{
				$("#moneda").hide();
				$("#porcentaje").show();
			}
		});

		if($("select[name='factor_calculo']").find('option:selected').val() == 'VALORFIJO')
		{
			$("#moneda").show();
			$("#porcentaje").hide();
		}
		else
		{
			$("#moneda").hide();
			$("#porcentaje").show();
		}
	});
</script>
@endpush

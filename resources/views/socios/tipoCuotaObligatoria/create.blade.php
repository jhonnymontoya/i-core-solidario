@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas obligatorias
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Cuotas obligatorias</li>
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
		{!! Form::open(['url' => 'tipoCuotaObligatoria', 'method' => 'post', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva cuota obligatoria</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código
								</label>
								{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'autofocus']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group {{ ($errors->has('cuif_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('cuif_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Cuenta auxiliar
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-table"></i></span>
									{!! Form::select('cuif_id', [], null, ['class' => 'form-control select2']) !!}
								</div>
								@if ($errors->has('cuif_id'))
									<span class="help-block">{{ $errors->first('cuif_id') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('es_reintegrable')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('es_reintegrable'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Es reintegrable?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<?php
										$reintegro = trim(old('es_reintegrable')) == '' ? '1' : old('es_reintegrable');
										$reintegro = $reintegro == '1' ? true : false;
									?>
									<label class="btn btn-primary {{ $reintegro ? 'active' : '' }}">
										{!! Form::radio('es_reintegrable', '1', $reintegro ? true : false) !!}Sí
									</label>
									<label class="btn btn-danger {{ !$reintegro ? 'active' : '' }}">
										{!! Form::radio('es_reintegrable', '0', !$reintegro ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('es_reintegrable'))
									<span class="help-block">{{ $errors->first('es_reintegrable') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('tipo_calculo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tipo_calculo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo cálculo
								</label>
								{!! Form::select('tipo_calculo', ['PORCENTAJESUELDO' => '% Sueldo', 'PORCENTAJESMMLV' => '% SMMLV', 'VALORFIJO' => 'Valor fijo'], null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Seleccione una opción']) !!}
								@if ($errors->has('tipo_calculo'))
									<span class="help-block">{{ $errors->first('tipo_calculo') }}</span>
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
								{!! Form::number('valor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Valor', 'step' => '0.01']) !!}
								@if ($errors->has('valor'))
									<span class="help-block">{{ $errors->first('valor') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('tope')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tope'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tope
								</label>
								{!! Form::number('tope', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tope', 'step' => '0.01']) !!}
								@if ($errors->has('tope'))
									<span class="help-block">{{ $errors->first('tope') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
					<a href="{{ url('tipoCuotaObligatoria') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("select[name='cuif_id']").selectAjax("{{ url('cuentaContable/cuentaContableAuxiliarAhorros') }}", {id:"{{ old('cuif_id') }}"});
	});
</script>
@endpush

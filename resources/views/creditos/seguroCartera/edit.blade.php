@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Seguros de cartera
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Seguros de cartera</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($seguroCartera, ['url' => ['seguroCartera', $seguroCartera], 'method' => 'PUT', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar seguro de cartera</h3>
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
								{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autofocus', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-7">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('base_prima')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('base_prima'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Base para prima
								</label>
								{!! Form::select('base_prima', ['SALDO' => 'Saldo', 'VALORINICIAL' => 'Valor inicial'], null, ['class' => 'form-control']) !!}
								@if ($errors->has('base_prima'))
									<span class="help-block">{{ $errors->first('base_prima') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-7">
							<div class="form-group {{ ($errors->has('aseguradora_tercero_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('aseguradora_tercero_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Aseguradora
								</label>
								{!! Form::select('aseguradora_tercero_id', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione aseguradora']) !!}
								@if ($errors->has('aseguradora_tercero_id'))
									<span class="help-block">{{ $errors->first('aseguradora_tercero_id') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('tasa_mes')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tasa_mes'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tasa mensual
								</label>
								{!! Form::number('tasa_mes', number_format($seguroCartera->tasa_mes, 4), ['class' => 'form-control', 'placeholder' => 'Tasa mensual', 'autocomplete' => 'off', 'step' => '0.0001']) !!}
								@if ($errors->has('tasa_mes'))
									<span class="help-block">{{ $errors->first('tasa_mes') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Esta activo?
								</label>
								<br>
								<div class="btn-group" data-toggle="buttons">
									<?php
										$activo = trim(old('esta_activo')) == '' ? $seguroCartera->esta_activo : old('esta_activo');
										$activo = $activo ? true : false;
									?>
									<label class="btn btn-outline-primary {{ $activo ? 'active' : '' }}">
										{!! Form::radio('esta_activo', '1', $activo ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ !$activo ? 'active' : '' }}">
										{!! Form::radio('esta_activo', '0', !$activo ? true : false) !!}No
									</label>
								</div>
								@if ($errors->has('esta_activo'))
									<span class="help-block">{{ $errors->first('esta_activo') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							@php
								$texto = "";
								$label = "warning";
								if($seguroCartera->modalidades->count() != 1) {
									$texto = "modalidades de credito asociadas";
								}
								else {
									$texto = "modalidad de credito asociada";
								}
								$label = $seguroCartera->modalidades->count() == 0 ? "warning" : "success";
							@endphp
							<span class="label label-{{ $label }}">{{ $seguroCartera->modalidades->count() }}</span> {{ $texto }}.
						</div>
						<div class="col-md-8">
							<a class="btn btn-outline-primary" href="{{ route('seguroCarteraModalidades', $seguroCartera) }}">Asociar modalidades de créditos</a>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('seguroCartera') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$(".select2").select2();
		$("select[name='aseguradora_tercero_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una aseguradora",
			ajax: {
				url: "{{ url('tercero/getTerceroConParametros') }}",
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						estado: 'ACTIVO'
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});
		@php
			$aseguradoraTerceroId = optional($seguroCartera)->aseguradora_tercero_id;
			$aseguradoraTerceroId = !empty(old('aseguradora_tercero_id')) ? old('aseguradora_tercero_id') : $aseguradoraTerceroId;
		@endphp

		@if(!empty($aseguradoraTerceroId))
			$.ajax({url: "{{ url('tercero/getTerceroConParametros') }}", dataType: 'json', data: {id: {{ $aseguradoraTerceroId }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='aseguradora_tercero_id']"));
					$("select[name='aseguradora_tercero_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

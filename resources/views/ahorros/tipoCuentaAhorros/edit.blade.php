@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipo cuenta de ahorros
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Tipo cuenta de ahorros</li>
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
		{!! Form::model($tipoCuentaAhorro, ['url' => ['tipoCuentaAhorros', $tipoCuentaAhorro], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar tipo de cuenta de ahorros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre_producto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre_producto', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre_producto'))
									<div class="invalid-feedback">{{ $errors->first('nombre_producto') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('capital_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta capital</label>
								@if ($cantidadCuentasDeAhorros == 0)
									{!! Form::select('capital_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@else
									<br>
									{!! Form::hidden('capital_cuif_id', $tipoCuentaAhorro->capital_cuif_id )  !!}
									{!! Form::text(null, str_limit($tipoCuentaAhorro->capitalCuif->full, 50), ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Cuenta capital', 'readonly' => true]) !!}
								@endif
								@if ($errors->has('capital_cuif_id'))
									<div class="invalid-feedback">{{ $errors->first('capital_cuif_id') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('saldo_minimo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Saldo mínimo</label>
								<div class="input-group">
									<div class="input-group-prepend"><span class="input-group-text">$</span></div>
									{!! Form::text('saldo_minimo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Saldo mínimo']) !!}
									@if ($errors->has('saldo_minimo'))
										<div class="invalid-feedback">{{ $errors->first('saldo_minimo') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('dias_para_inactivacion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Días para inactivación</label>
								{!! Form::number('dias_para_inactivacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Días para inactivación', 'min' => 0, 'step' => 1]) !!}
								@if ($errors->has('dias_para_inactivacion'))
									<div class="invalid-feedback">{{ $errors->first('dias_para_inactivacion') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Activa?</label>
								<div>
									@php
										$valid = $errors->has('esta_activa') ? 'is-invalid' : '';
										$estaActiva = empty(old('esta_activa')) ? $tipoCuentaAhorro->esta_activa : old('esta_activa');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActiva ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($estaActiva ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActiva ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$estaActiva ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoCuentaAhorros') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
@if ($cantidadCuentasDeAhorros == 0)
	<script type="text/javascript">
		$(function(){
			$("input[name='saldo_minimo']").maskMoney('mask');
			$("select[name='capital_cuif_id']").select2({
				allowClear: true,
				placeholder: "Seleccione una opción",
				ajax: {
					url: '{{ url('cuentaContable/getCuentaConParametros') }}',
					dataType: 'json',
					delay: 250,
					data: function (params) {
						return {
							q: params.term,
							tipoCuenta: 'AUXILIAR',
							estado: 1,
							modulo: 6,
							page: params.page
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
				$cuenta = trim(old('capital_cuif_id')) == '' ? $tipoCuentaAhorro->capital_cuif_id : old('capital_cuif_id');
			@endphp
			@if(!empty($cuenta))
				$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $cuenta }} }}).done(function(data){
					if(data.total_count == 1) {
						element = data.items[0];
						$('<option>').val(element.id).text(element.text).appendTo($("select[name='capital_cuif_id']"));
						$("select[name='capital_cuif_id']").val(element.id).trigger("change");
					}
				});
			@endif
		});
	</script>
@endif
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipo cuenta de ahorros
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">Tipo cuenta de ahorros</li>
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
		{!! Form::open(['url' => 'tipoCuentaAhorros', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Crear nuevo tipo de cuenta de ahorros</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('nombre_producto')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre_producto'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									{!! Form::text('nombre_producto', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del producto', 'autofocus']) !!}
									@if ($errors->has('nombre_producto'))
										<span class="help-block">{{ $errors->first('nombre_producto') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('capital_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('capital_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta capital
									</label>
									{!! Form::select('capital_cuif_id', [], null, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Cuenta capital']) !!}
									@if ($errors->has('capital_cuif_id'))
										<span class="help-block">{{ $errors->first('capital_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('saldo_minimo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('saldo_minimo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Saldo mínimo
									</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('saldo_minimo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Saldo mínimo', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
									</div>
									@if ($errors->has('saldo_minimo'))
										<span class="help-block">{{ $errors->first('saldo_minimo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('dias_para_inactivacion')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('dias_para_inactivacion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Días para inactivación
									</label>
									{!! Form::number('dias_para_inactivacion', null, ['class' => 'form-control select2', 'placeholder' => 'Días para inactivación', 'min' => 0, 'step' => 1]) !!}
									@if ($errors->has('dias_para_inactivacion'))
										<span class="help-block">{{ $errors->first('dias_para_inactivacion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tipoCuentaAhorros') }}" class="btn btn-danger pull-right">Cancelar</a>
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

		@if(!empty(old('capital_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('capital_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='capital_cuif_id']"));
					$("select[name='capital_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

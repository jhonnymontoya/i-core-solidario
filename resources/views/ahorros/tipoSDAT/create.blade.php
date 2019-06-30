@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos SDAT
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">Tipos SDAT</li>
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
		{!! Form::open(['url' => 'tipoSDAT', 'method' => 'post', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Crear nuevo tipo SDAT</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-4">
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
							<div class="col-md-8">
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
						</div>

						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('capital_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('capital_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta capital
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('capital_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('capital_cuif_id'))
										<span class="help-block">{{ $errors->first('capital_cuif_id') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('intereses_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('intereses_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta intereses
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('intereses_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('intereses_cuif_id'))
										<span class="help-block">{{ $errors->first('intereses_cuif_id') }}</span>
									@endif
								</div>
							</div>

							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('intereses_por_pagar_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('intereses_por_pagar_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta intereses por pagar
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('intereses_por_pagar_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('intereses_por_pagar_cuif_id'))
										<span class="help-block">{{ $errors->first('intereses_por_pagar_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>

					</div>
					<div class="box-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('tipoSDAT') }}" class="btn btn-danger pull-right">Cancelar</a>
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
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='capital_cuif_id']"));
					$("select[name='capital_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='intereses_cuif_id']").select2({
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
						modulo: 2,
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

		@if(!empty(old('intereses_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('intereses_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_cuif_id']"));
					$("select[name='intereses_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='intereses_por_pagar_cuif_id']").select2({
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

		@if(!empty(old('intereses_por_pagar_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('intereses_por_pagar_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='intereses_por_pagar_cuif_id']"));
					$("select[name='intereses_por_pagar_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

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
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::open(['url' => 'seguroCartera', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Crear nuevo seguro para cartera</h3>
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
							{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autofocus']) !!}
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
					<div class="col-md-9">
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
							{!! Form::number('tasa_mes', null, ['class' => 'form-control', 'placeholder' => 'Tasa mensual', 'autocomplete' => 'off', 'step' => '0.0001']) !!}
							@if ($errors->has('tasa_mes'))
								<span class="help-block">{{ $errors->first('tasa_mes') }}</span>
							@endif
						</div>
					</div>
				</div>

			</div>
			<div class="card-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('seguroCartera') }}" class="btn btn-danger pull-right">Cancelar</a>
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

		@if(!empty(old('aseguradora_tercero_id')))
			$.ajax({url: "{{ url('tercero/getTerceroConParametros') }}", dataType: 'json', data: {id: {{ old('aseguradora_tercero_id') }} }}).done(function(data){
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

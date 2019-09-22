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
				{!! Form::open(['url' => 'seguroCartera', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo seguro para cartera</h3>
				</div>
				<div class="card-body">

					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Codigo</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Codigo', 'autofocus']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-7">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('base_prima') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Base para prima</label>
								{!! Form::select('base_prima', ['SALDO' => 'Saldo', 'VALORINICIAL' => 'Valor inicial'], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('base_prima'))
									<div class="invalid-feedback">{{ $errors->first('base_prima') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-9">
							<div class="form-group">
								@php
									$valid = $errors->has('aseguradora_tercero_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Aseguradora</label>
								{!! Form::select('aseguradora_tercero_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('aseguradora_tercero_id'))
									<div class="invalid-feedback">{{ $errors->first('aseguradora_tercero_id') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('tasa_mes') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tasa mensual</label>
								{!! Form::number('tasa_mes', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Tasa mensual', 'step' => '0.0001']) !!}
								@if ($errors->has('tasa_mes'))
									<div class="invalid-feedback">{{ $errors->first('tasa_mes') }}</div>
								@endif
							</div>
						</div>
					</div>

				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('seguroCartera') }}" class="btn btn-outline-danger">Cancelar</a>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuenta de ahorros
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Cuenta de ahorros</li>
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
		{!! Form::open(['url' => 'cuentaAhorros', 'method' => 'post', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva cuenta de ahorros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_cuenta_ahorro_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo cuenta ahorro</label>
								{!! Form::select('tipo_cuenta_ahorro_id', $tiposCuentaAhorros, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('tipo_cuenta_ahorro_id'))
									<div class="invalid-feedback">{{ $errors->first('tipo_cuenta_ahorro_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('titular_socio_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Titular</label>
								{!! Form::select('titular_socio_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('titular_socio_id'))
									<div class="invalid-feedback">{{ $errors->first('titular_socio_id') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('cuentaAhorros') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$("select[name='titular_socio_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('socio/getSocioConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						estadoIgualA: 'ACTIVO',
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

		@if(!empty(old('titular_socio_id')))
			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ old('titular_socio_id') }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='titular_socio_id']"));
					$("select[name='titular_socio_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

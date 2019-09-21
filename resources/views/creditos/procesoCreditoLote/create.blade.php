@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito en lote
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito en lote</li>
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
				{!! Form::open(['url' => 'procesoCreditoLote', 'method' => 'post', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo proceso</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_proceso') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha del proceso</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_proceso', date('d/m/Y'), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_proceso'))
										<div class="invalid-feedback">{{ $errors->first('fecha_proceso') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('modalidad_credito_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Modalidad de crédito</label>
								{!! Form::select('modalidad_credito_id', $modalidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Modalidad de crédito']) !!}
								@if ($errors->has('modalidad_credito_id'))
									<div class="invalid-feedback">{{ $errors->first('modalidad_credito_id') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::text('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('contrapartida_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta contra partida</label>
								{!! Form::select('contrapartida_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('contrapartida_cuif_id'))
									<div class="invalid-feedback">{{ $errors->first('contrapartida_cuif_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('contrapartida_tercero_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tercero contra partida</label>
								{!! Form::select('contrapartida_tercero_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('contrapartida_tercero_id'))
									<div class="invalid-feedback">{{ $errors->first('contrapartida_tercero_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('referencia') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Referencia</label>
								{!! Form::text('referencia', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Referencia']) !!}
								@if ($errors->has('referencia'))
									<div class="invalid-feedback">{{ $errors->first('referencia') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('procesoCreditoLote') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$('.select2').select2();

		$("select[name='contrapartida_cuif_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						modulo: '1,2',
						estado: '1',
						tipoCuenta: 'AUXILIAR'
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

		@if(!empty(old('cuenta')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta']"));
					$("select[name='cuenta']").val(element.id).trigger("change");
				}
			});
		@endif

		$("select[name='contrapartida_tercero_id']").select2({
			allowClear: true,
			placeholder: "Seleccione un tercero",
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

		@if(!empty(old('contrapartida_tercero_id')))
			$.ajax({url: '{{ url('tercero/getTerceroConParametros') }}', dataType: 'json', data: {id: {{ old('contrapartida_tercero_id') }} }}).done(function(data){
				if(data.total_count == 1)  {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='contrapartida_tercero_id']"));
					$("select[name='contrapartida_tercero_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

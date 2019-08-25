@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Proceso de retiros
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Proceso de retiros</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::open(['url' => 'retiroSocio', 'method' => 'post', 'role' => 'form']) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva solicitud de crédito</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('socio_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Socio</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fas fa-male"></i>
										</span>
									</div>
									{!! Form::select('socio_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('socio_id'))
										<div class="invalid-feedback">{{ $errors->first('socio_id') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('causa_retiro_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Causa de retiro</label>
								{!! Form::select('causa_retiro_id', $causasRetiros, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Causa de retiro']) !!}
								@if ($errors->has('causa_retiro_id'))
									<div class="invalid-feedback">{{ $errors->first('causa_retiro_id') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha_solicitud_retiro') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha Solicitud retiro</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::text('fecha_solicitud_retiro',  date('d/m/Y'), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha_solicitud_retiro'))
										<div class="invalid-feedback">{{ $errors->first('fecha_solicitud_retiro') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('observacion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Observaciones</label>
								{!! Form::textarea('observacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Observaciones']) !!}
								@if ($errors->has('observacion'))
									<div class="invalid-feedback">{{ $errors->first('observacion') }}</div>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Procesar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('retiroSocio') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
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
		$("select[name='socio_id']").select2({
			allowClear: true,
			placeholder: "Seleccione un socio",
			ajax: {
				url: '{{ url('socio/getSocioConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
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

		@if(!empty(old('socio_id')))
			$.ajax({url: '{{ url('socio/getSocioConParametros') }}', dataType: 'json', data: {id: {{ old('socio_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio_id']"));
					$("select[name='socio_id']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Proceso de retiros
			<small>Socios</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Socios</a></li>
			<li class="active">Proceso de retiros</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="row">
			{!! Form::open(['url' => 'retiroSocio', 'method' => 'post', 'role' => 'form']) !!}
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Crear nueva solicitud de crédito</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('socio_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('socio_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Socio
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-male"></i></span>
										{!! Form::select('socio_id', [], null, ['class' => 'form-control select2', 'tabIndex' => '6']) !!}
									</div>
									@if ($errors->has('socio_id'))
										<span class="help-block">{{ $errors->first('socio_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('causa_retiro_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('causa_retiro_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Causa de retiro
									</label>
									{!! Form::select('causa_retiro_id', $causasRetiros, null, ['class' => 'form-control select2', 'placeholder' => 'Causa de retiro', 'autocomplete' => 'off', 'autofocus']) !!}
									@if ($errors->has('causa_retiro_id'))
										<span class="help-block">{{ $errors->first('causa_retiro_id') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('fecha_solicitud_retiro')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('fecha_solicitud_retiro'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha solicitud retiro
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
										{!! Form::text('fecha_solicitud_retiro', date('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
									</div>
									@if ($errors->has('fecha_solicitud_retiro'))
										<span class="help-block">{{ $errors->first('fecha_solicitud_retiro') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('observacion')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('observacion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Observaciones
									</label>
									{!! Form::textarea('observacion', null, ['class' => 'form-control', 'placeholder' => 'Observaciones', 'autocomplete' => 'off']) !!}
									@if ($errors->has('observacion'))
										<span class="help-block">{{ $errors->first('observacion') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Procesar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('retiroSocio') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito</li>
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
			{!! Form::open(['url' => 'solicitudCredito', 'method' => 'post', 'role' => 'form']) !!}<div class="col-md-12">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva solicitud de crédito</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('modalidad')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('modalidad'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Modalidad de crédito
								</label>
								{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off', 'autofocus']) !!}
								@if ($errors->has('modalidad'))
									<span class="help-block">{{ $errors->first('modalidad') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('solicitante')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('solicitante'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Solicitante
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-male"></i></span>
									{!! Form::select('solicitante', [], null, ['class' => 'form-control select2', 'tabIndex' => '6']) !!}
								</div>
								@if ($errors->has('solicitante'))
									<span class="help-block">{{ $errors->first('solicitante') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('fecha_solicitud')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('fecha_solicitud'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha solicitud
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									{!! Form::text('fecha_solicitud', date('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('fecha_solicitud'))
									<span class="help-block">{{ $errors->first('fecha_solicitud') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('solicitudCredito') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
		$(".select2").select2();

		$("select[name='solicitante']").select2({
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
						estado: 'ACTIVO',
						tipo: 'NATURAL'
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
		
		<?php
			$solicitante = old('solicitante');
			if(!is_null($solicitante)) {
				?>
				$.ajax({url: "{{ url('tercero/getTerceroConParametros') }}", dataType: 'json', data: {id: {{ $solicitante }} }}).done(function(data){
					if(data.total_count == 1)
					{
						element = data.items[0];
						$('<option>').val(element.id).text(element.text).appendTo($("select[name='solicitante']"));
						$("select[name='solicitante']").val(element.id).trigger("change");
					}
				});
				<?php
			}
		?>
	});
</script>
@endpush

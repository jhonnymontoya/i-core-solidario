<?php
	$dataTitulo = Session::has("dataTitulo") ? Session::get("dataTitulo") : null;
?>
@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						SDAT
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">SDAT</li>
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
		@if (Session::has("error"))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get("error") }}</p>
			</div>
		@endif
		{!! Form::open(['url' => 'SDAT', 'method' => 'post', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo SDAT</h3>
				</div>
				<div class="card-body">
					<div class="row">

						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tipo_sdat')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tipo_sdat'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo SDAT
								</label>
								{!! Form::select('tipo_sdat', $tiposSDAT, null, ['class' => 'form-control select2']) !!}
								@if ($errors->has('tipo_sdat'))
									<span class="help-block">{{ $errors->first('tipo_sdat') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('socio')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('socio'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Socio
								</label>
								{!! Form::select('socio', [], null, ['class' => 'form-control select2']) !!}
								@if ($errors->has('socio'))
									<span class="help-block">{{ $errors->first('socio') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="row">

						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('valor'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Valor
								</label>
								<div class="input-group">
									<span class="input-group-addon">$</span>
									{!! Form::text('valor', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'valor', 'data-maskMoney']) !!}
								</div>
								@if ($errors->has('valor'))
									<span class="help-block">{{ $errors->first('valor') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('fecha')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('fecha'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Fecha constitución
								</label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fa fa-calendar"></i></span>
									@php
										$fecha = date('d/m/Y');
										$fecha = !empty(old($fecha)) ? old($fecha) : $fecha;
									@endphp
									{!! Form::text('fecha', $fecha, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
								</div>
								@if ($errors->has('fecha'))
									<span class="help-block">{{ $errors->first('fecha') }}</span>
								@endif
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('plazo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('plazo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Plazo (días)
								</label>
								{!! Form::text('plazo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'plazo', 'data-maskMoney']) !!}
								@if ($errors->has('plazo'))
									<span class="help-block">{{ $errors->first('plazo') }}</span>
								@endif
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-12">
							{!! Form::submit('Continuar', ['class' => 'btn btn-outline-primary']) !!}
							<a href="{{ url('SDAT') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
						</div>
					</div>

				@if (isset($dataTitulo))
					<br>
					<hr>
					<div class="row">
						<div class="col-md-12">
							<dl class="dl-horizontal">
								<dt>Fecha vencimiento</dt>
								<dd>{{ $dataTitulo["fechaVencimiento"] }}</dd>

								<dt>Tasa E.A.</dt>
								<dd>{{ number_format($dataTitulo["tasaEA"], 2) }}%</dd>

								<dt>Tasa M.V.</dt>
								<dd>{{ number_format($dataTitulo["tasa"], 2) }}%</dd>

								<dt>Interes estimado</dt>
								<dd>${{ number_format($dataTitulo["interesEstimado"]) }}</dd>

								<dt>Retefuente estimado</dt>
								<dd>${{ number_format($dataTitulo["retefuenteEstimado"]) }}</dd>
							</dl>
							<br>
							<strong>TOTAL AL VENCIMIENTO: ${{ number_format($dataTitulo["total"]) }}</strong>
						</div>
					</div>
				@endif

				</div>
				<div class="card-footer">
					@if (isset($dataTitulo))
						{!! Form::submit('Radicar', ['class' => 'btn btn-outline-success', 'name' => 'radicar']) !!}
					@endif
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
	<?php
		$urlSocios = url('socio/getSocioConParametros');
	?>
	$(function(){

		$("select[name='socio']").select2({
			allowClear: true,
			placeholder: "Seleccione una opción",
			ajax: {
				url: '{{ $urlSocios }}',
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

		<?php
			$socio = Request::has('socio') ? Request::get('socio') : null;
			$socio = (!empty(old('socio'))) ? old('socio') : $socio;
			if($socio) {
				?>
				$.ajax({url: '{{ $urlSocios }}', dataType: 'json', data: {id: {{ $socio }} }}).done(function(data){
					if(data.total_count == 1) {
						element = data.items[0];
						$('<option>').val(element.id).text(element.text).appendTo($("select[name='socio']"));
						$("select[name='socio']").val(element.id).trigger("change");
					}
				});
				<?php
			}
		?>
	});
</script>
@endpush

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
							<div class="form-group">
								@php
									$valid = $errors->has('tipo_sdat') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Tipo SDAT</label>
								{!! Form::select('tipo_sdat', $tiposSDAT, null, ['class' => [$valid, 'form-control']]) !!}
								@if ($errors->has('tipo_sdat'))
									<div class="invalid-feedback">{{ $errors->first('tipo_sdat') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('socio') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Socio</label>
								{!! Form::select('socio', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('socio'))
									<div class="invalid-feedback">{{ $errors->first('socio') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('valor') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Valor</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor', 'data-maskMoney']) !!}
									@if ($errors->has('valor'))
										<div class="invalid-feedback">{{ $errors->first('valor') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('fecha') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Fecha constitución</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha', date('d/m/Y'), ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									@if ($errors->has('fecha'))
										<div class="invalid-feedback">{{ $errors->first('fecha') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('plazo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Plazo (días)</label>
								{!! Form::text('plazo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Plazo (días)', 'data-maskMoney']) !!}
								@if ($errors->has('plazo'))
									<div class="invalid-feedback">{{ $errors->first('plazo') }}</div>
								@endif
							</div>
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-12 text-right">
							{!! Form::submit('Continuar', ['class' => 'btn btn-outline-primary']) !!}
							<a href="{{ url('SDAT') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
						</div>
					</div>

				@if (isset($dataTitulo))
					<br>
					<hr>
					<div class="row">
						<div class="col-md-6">
							<dl>
								<dt>Fecha vencimiento</dt>
								<dd>{{ $dataTitulo["fechaVencimiento"] }}</dd>

								<dt>Tasa M.V.</dt>
								<dd>{{ number_format($dataTitulo["tasa"], 2) }}%</dd>

								<dt>Retefuente estimado</dt>
								<dd>${{ number_format($dataTitulo["retefuenteEstimado"]) }}</dd>
							</dl>
						</div>
						<div class="col-md-6">
							<dl>
								<dt>Tasa E.A.</dt>
								<dd>{{ number_format($dataTitulo["tasaEA"], 2) }}%</dd>

								<dt>Interes estimado</dt>
								<dd>${{ number_format($dataTitulo["interesEstimado"]) }}</dd>
							</dl>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<strong>TOTAL AL VENCIMIENTO: ${{ number_format($dataTitulo["total"]) }}</strong>
						</div>
					</div>
				@endif

				</div>
				<div class="card-footer text-right">
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

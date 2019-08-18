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
		{!! Form::open(['route' => ['SDAT.put.constituir', $sdat->id], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Constituir SDAT</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<h4>Confirmación de constitución de deposito</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12">
								<dl class="dl-horizontal">
									<dt>No. Radicado:</dt>
									<dd>{{ $sdat->id }}</dd>

									@php
										$tercero = $sdat->socio->tercero;
										$nombre = sprintf(
											"%s %s - %s",
											$tercero->tipoIdentificacion->codigo,
											$tercero->numero_identificacion,
											$tercero->nombre_corto
										);
									@endphp

									<dt>Nombre:</dt>
									<dd>{{ $nombre }}</dd>

									<dt>Valor:</dt>
									<dd>${{ number_format($sdat->valor) }}</dd>
								</dl>
							</div>
						</div>

						<div class="row form-horizontal">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('cuenta')?'has-error':'') }}">
									<label class="control-label col-md-2">
										@if ($errors->has('cuenta'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta
									</label>
									<div class="col-md-8">
										<div class="input-group">
											<span class="input-group-addon"><i class="fa fa-table"></i></span>
											{!! Form::select('cuenta', [], null, ['class' => 'form-control select2']) !!}
										</div>
										@if ($errors->has('cuenta'))
											<span class="help-block">{{ $errors->first('cuenta') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>

					</div>
					<div class="card-footer">
						{!! Form::submit('Constituir', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('SDAT') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("select[name='cuenta']").select2({
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
						modulo: '2,1',
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

		@if(!empty(old('cuenta')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta') }} }}).done(function(data){
				if(data.total_count == 1) {
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta']"));
					$("select[name='cuenta']").val(element.id).trigger("change");
				}
			});
		@endif
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cobros administrativos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Cobros administrativos</li>
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

		<div class="row">
			{!! Form::open(['url' => 'cobrosAdministrativos', 'method' => 'post', 'role' => 'form']) !!}
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Crear nuevo cobro administrativo</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'autofocus']) !!}
									@if ($errors->has('codigo'))
										<span class="help-block">{{ $errors->first('codigo') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off']) !!}
									@if ($errors->has('nombre'))
										<span class="help-block">{{ $errors->first('nombre') }}</span>
									@endif
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('efecto')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('efecto'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Efecto
									</label>
									<br>
									<?php
										$efecto = 'DEDUCCIONCREDITO';
										if(!empty(old('efecto'))) {
											$efecto = old('efecto');
										}
									?>
									<div class="btn-group" data-toggle="buttons">
										<label class="btn btn-primary {{ $efecto == 'DEDUCCIONCREDITO' ? 'active' : ''}}">
											{!! Form::radio('efecto', 'DEDUCCIONCREDITO', $efecto == 'DEDUCCIONCREDITO' ? true : false) !!}Deducción de crédito
										</label>
										<label class="btn btn-primary {{ $efecto == 'DEDUCCIONCREDITO' ? '' : 'active'}}">
											{!! Form::radio('efecto', 'ADICIONCREDITO', $efecto == 'DEDUCCIONCREDITO' ? false : true) !!}Adición de crédito
										</label>
									</div>
									@if ($errors->has('efecto'))
										<span class="help-block">{{ $errors->first('efecto') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-8">
								<div class="form-group {{ ($errors->has('destino_cuif_id')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('destino_cuif_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Cuenta destino
									</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fa fa-table"></i></span>
										{!! Form::select('destino_cuif_id', [], null, ['class' => 'form-control select2']) !!}
									</div>
									@if ($errors->has('destino_cuif_id'))
										<span class="help-block">{{ $errors->first('destino_cuif_id') }}</span>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('cobrosAdministrativos') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("select[name='destino_cuif_id']").select2({
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
						modulo: 2,
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

		@if(!empty(old('destino_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('destino_cuif_id') }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='destino_cuif_id']"));
					$("select[name='destino_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif
	</script>
@endpush

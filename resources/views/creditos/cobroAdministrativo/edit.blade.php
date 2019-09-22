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

		{!! Form::model($cobro, ['url' => ['cobrosAdministrativos', $cobro], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar cobro administrativo</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'autofocus']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
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
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Estado?</label>
								<div>
									@php
										$valid = $errors->has('esta_activo') ? 'is-invalid' : '';
										$estado = empty(old('esta_activo')) ? $cobro->esta_activo : old('esta_activo');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estado ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 1, ($estado ? true : false), ['class' => [$valid]]) !!}Activo
										</label>
										<label class="btn btn-danger {{ !$estado ? 'active' : '' }}">
											{!! Form::radio('esta_activo', 0, (!$estado ? true : false ), ['class' => [$valid]]) !!}Inactivo
										</label>
									</div>
									@if ($errors->has('esta_activo'))
										<div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">Efecto</label>
								<div>
									@php
										$valid = $errors->has('efecto') ? 'is-invalid' : '';
										$efecto = empty(old('efecto')) ? $cobro->efecto : old('efecto');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $efecto == 'DEDUCCIONCREDITO' ? 'active' : '' }}">
											{!! Form::radio('efecto', 'DEDUCCIONCREDITO', ($efecto == 'DEDUCCIONCREDITO' ? true : false), ['class' => [$valid]]) !!}Deducción de crédito
										</label>
										<label class="btn btn-primary {{ $efecto == 'ADICIONCREDITO' ? 'active' : '' }}">
											{!! Form::radio('efecto', 'ADICIONCREDITO', ($efecto == 'ADICIONCREDITO' ? true : false ), ['class' => [$valid]]) !!}Adición de crédito
										</label>
									</div>
									@if ($errors->has('efecto'))
										<div class="invalid-feedback">{{ $errors->first('efecto') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-8">
							<div class="form-group">
								@php
									$valid = $errors->has('destino_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Cuenta destino</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::select('destino_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
									@if ($errors->has('destino_cuif_id'))
										<div class="invalid-feedback">{{ $errors->first('destino_cuif_id') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<hr>
					<br>

					{{-- Componente de parametros del cobro --}}
					@component('creditos.cobroAdministrativo.componentes.parametros', ['cobro' => $cobro])
					@endcomponent

					@if ($cobro->es_condicionado)
						{{-- Componente de condiciones del cobro --}}
						@component('creditos.cobroAdministrativo.componentes.condiciones', ['cobro' => $cobro])
						@endcomponent
					@endif
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('cobrosAdministrativos') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</section>
</div>
{!! Form::open(['url' => ['cobrosAdministrativos', $cobro], 'id' => 'adicionarCondicion']) !!}
{!! Form::close() !!}
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

		@php
			$destinoCuifId = $cobro->destino_cuif_id;
			$destinoCuifId = empty(old('destino_cuif_id')) ? $cobro->destino_cuif_id : old('destino_cuif_id');
		@endphp

		@if($destinoCuifId)
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ $destinoCuifId }} }}).done(function(data){
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

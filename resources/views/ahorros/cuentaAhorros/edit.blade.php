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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		{!! Form::model($cuentaAhorro, ['url' => ['cuentaAhorros', $cuentaAhorro], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar cuenta de ahorros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tipo_cuenta_ahorro_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tipo_cuenta_ahorro_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo cuenta ahorro
								</label>
								{!! Form::text(null, $cuentaAhorro->tipoCuentaAhorro->nombre_producto, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tipo cuenta ahorro', 'readonly' => true]) !!}
								@if ($errors->has('tipo_cuenta_ahorro_id'))
									<span class="help-block">{{ $errors->first('tipo_cuenta_ahorro_id') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('titular_socio_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('titular_socio_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Titular
								</label>
								{!! Form::text('', $cuentaAhorro->socioTitular->tercero->nombre_completo, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Titular', 'readonly' => true]) !!}
								@if ($errors->has('titular_socio_id'))
									<span class="help-block">{{ $errors->first('titular_socio_id') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('numero_cuenta')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('numero_cuenta'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Número de cuenta
								</label>
								{!! Form::text('numero_cuenta', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de cuenta', 'readonly' => true]) !!}
								@if ($errors->has('numero_cuenta'))
									<span class="help-block">{{ $errors->first('numero_cuenta') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Fecha apertura</label>
								@php
									$fechaApertura = $cuentaAhorro->fecha_apertura . ' (' . $cuentaAhorro->fecha_apertura->diffForHumans() . ')';
								@endphp
								{!! Form::text('fecha_apertura', $fechaApertura, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Fecha apertura', 'readonly' => true]) !!}
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('cupo_flexible') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Saldo flexible</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-table"></i>
										</span>
									</div>
									{!! Form::text('cupo_flexible', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Saldo flexible', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
									@if ($errors->has('cupo_flexible'))
										<div class="invalid-feedback">{{ $errors->first('cupo_flexible') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre_deposito') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre deposito</label>
								{!! Form::text('nombre_deposito', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre deposito']) !!}
								@if ($errors->has('nombre_deposito'))
									<div class="invalid-feedback">{{ $errors->first('nombre_deposito') }}</div>
								@endif
							</div>
						</div>
					</div>

					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Estado</label>
								<br>
								<span class="badge badge-pill badge-{{ $cuentaAhorro->estado == 'ACTIVA' ? 'success' : 'danger' }}">
									{{ $cuentaAhorro->estado }}
								</span>
							</div>
						</div>
						@if ($cuentaAhorro->estado == 'CERRADA')
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Cuenta capital</label>
									{!! Form::text('fecha_cierre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Fecha cierre', 'readonly' => true]) !!}
								</div>
							</div>
						@endif
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
		$(window).load(function(){
			$("input[name='cupo_flexible']").maskMoney('mask');
		});
	});
</script>
@endpush

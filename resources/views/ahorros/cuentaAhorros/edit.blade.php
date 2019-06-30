@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cuenta de ahorros
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">Cuenta de ahorros</li>
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
		{!! Form::model($cuentaAhorro, ['url' => ['cuentaAhorros', $cuentaAhorro], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Editar cuenta de ahorros</h3>
					</div>
					<div class="box-body">
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
								<div class="form-group {{ ($errors->has('cupo_flexible')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('cupo_flexible'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Saldo flexible
									</label>
									<div class="input-group">
										<span class="input-group-addon">$</span>
										{!! Form::text('cupo_flexible', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Saldo flexible', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
									</div>
									@if ($errors->has('cupo_flexible'))
										<span class="help-block">{{ $errors->first('cupo_flexible') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('nombre_deposito')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nombre_deposito'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre deposito
									</label>
									{!! Form::text('nombre_deposito', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del deposito', 'autofocus']) !!}
									@if ($errors->has('nombre_deposito'))
										<span class="help-block">{{ $errors->first('nombre_deposito') }}</span>
									@endif
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<div class="form-group">
									<label class="control-label">Estado</label>
									<br>
									<span class="label label-{{ $cuentaAhorro->estado == 'ACTIVA' ? 'success' : 'danger' }}">
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
					<div class="box-footer">
						{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('cuentaAhorros') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$(window).load(function(){
			$("input[name='cupo_flexible']").maskMoney('mask');
		});
	});
</script>
@endpush

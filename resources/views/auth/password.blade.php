@extends('layouts.login')

@section('content')
@php
	$realm = null;
	if(Session::has('realm')) {
		$realm = Session::get('realm');
		if(isset($realm->categoriaImagenes[1])) {
			$realm = $realm->categoriaImagenes[1]->pivot->nombre;
			$realm = "storage/entidad/" . $realm;
		}
		else {
			$realm = "img/logos/icore.png";
		}
	}
	else {
		$realm = "img/logos/icore.png";
	}
@endphp
<div class="login-box-body">
	{!! Form::open(['url' => 'login', 'method' => 'post', 'role' => 'form']) !!}
	<p class="login-box-msg"></p>
	<div class="row">
		<div class="hidden-xs col-sm-4 col-md-4">
			<img src="{{ asset($realm) }}">
		</div>
		<div class="col-xs-12 col-sm-8 col-md-8">
			<div class="row">
				<div class="col-md-12 text-center"><h4>Iniciar sesión</h4></div>
			</div>
			<div class="row">
				<div class="col-md-12"><h5>Contraseña para: {{ Session::get("usuario") }}</h5></div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="form-group has-feedback {{ ($errors->has('password')?'has-error':'') }}">
						{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'autofocus']) !!}
						<span class="glyphicon glyphicon-lock form-control-feedback"></span>
						@if ($errors->has('password'))
							<span class="help-block">{{ $errors->first('password') }}</span>
						@endif
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					{!! Form::submit('Ingresar', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
				</div>
			</div>

			<div class="row">
				<div class="col-md-3 col-sm-12 col-xs-12">
					<a href="{{ route('login') }}?volver" class="pull-right">Volver</a>
				</div>
				<div class="col-md-9 col-sm-12 col-xs-12">
					<a href="{{ route('password.request') }}" class="pull-right">Olvide mi contraseña</a>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection

@push('style')
	<style type="text/css">
		@media (min-width: 768px) { 
			.login-box{
				width: 480px;
			}
		}
	</style>
@endpush

@push('scripts')
@endpush
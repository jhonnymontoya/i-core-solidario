@extends('layouts.login')

@section('content')
<div class="login-pic" data-tilt>
	<img class="login-pic-border img-circle" src="{{ asset(Session::get('avatar')) }}">
</div>
<div class="login-form">
	{!! Form::open(['url' => 'login', 'method' => 'post', 'role' => 'form']) !!}
	<div class="text-center">
		<h4>Iniciar sesión</h4>
	</div>
	<div>
		<h5>Contraseña para: {{ Session::get("usuario") }}</h5>
	</div>
	<div>
		<div class="form-group has-feedback {{ ($errors->has('password')?'has-error':'') }}">
			{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'autofocus']) !!}
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			@if ($errors->has('password'))
				<span class="help-block">{{ $errors->first('password') }}</span>
			@endif
		</div>
	</div>
	<div>
		{!! Form::submit('Ingresar', ['class' => 'btn btn-success btn-block btn-flat']) !!}
	</div>

	<div>
		<div class="col-md-3 col-sm-12 col-xs-12">
			<a href="{{ route('login') }}?volver" class="pull-right">Volver</a>
		</div>
		<div class="col-md-9 col-sm-12 col-xs-12">
			<a href="{{ route('password.request') }}" class="pull-right">Olvide mi contraseña</a>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection

@push('style')
@endpush

@push('scripts')
	<script type="text/javascript">
		$('.login-pic').tilt({
			scale: 1.2
		})
	</script>
@endpush
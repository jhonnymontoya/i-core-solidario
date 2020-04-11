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
		<div class="form-group">
			@php
				$valid = $errors->has('password') ? 'is-invalid' : '';
			@endphp
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fas fa-lock"></i>
					</span>
				</div>
				{!! Form::password('password', ['class' => [$valid, 'form-control'], 'placeholder' => 'Contraseña', 'autofocus']) !!}
				@if ($errors->has('password'))
					<div class="invalid-feedback">{{ $errors->first('password') }}</div>
				@endif
			</div>
		</div>
	</div>
	<div>
		{!! Form::submit('Ingresar', ['class' => 'btn btn-outline-success btn-block']) !!}
	</div>

	<div class="row">
		<div class="col-md-3 col-sm-12 col-xs-12">
			<a href="{{ route('login') }}?volver">Volver</a>
		</div>
		<div class="col-md-9 col-sm-12 col-xs-12">
			<a href="{{ route('password.request') }}" class="float-right">Olvide mi contraseña</a>
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
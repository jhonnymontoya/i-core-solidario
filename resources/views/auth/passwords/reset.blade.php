@extends('layouts.login')

@section('content')
<div class="login-pic" data-tilt>
	<img src="{{ asset(Session::get('realmAvatar')) }}">
</div>
<div class="login-form">
	{!! Form::open(['url' => '/password/reset', 'method' => 'post', 'role' => 'form']) !!}
	{!! Form::hidden('token', $token) !!}
	<div class="text-center">
		<h4>Restaurar contraseña</h4>
	</div>
	@if (session('status'))
		<div class="alert alert-success">
			{{ session('status') }}
		</div>
	@endif

	<div>
		<div class="form-group has-feedback {{ ($errors->has('usuario')?'has-error':'') }}">
			{!! Form::text('usuario', null, ['class' => 'form-control', 'placeholder' => 'Usuario', 'autocomplete' => 'off', 'autofocus']) !!}
			<span class="glyphicon glyphicon-user form-control-feedback"></span>
			@if ($errors->has('usuario'))
				<span class="help-block">{{ $errors->first('usuario') }}</span>
			@endif
		</div>
	</div>

	<div>
		<div class="form-group has-feedback {{ ($errors->has('password')?'has-error':'') }}">
			{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'autocomplete' => 'on']) !!}
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			@if ($errors->has('password'))
				<span class="help-block">{{ $errors->first('password') }}</span>
			@endif
		</div>
	</div>

	<div>
		<div class="form-group has-feedback {{ ($errors->has('password_confirmation')?'has-error':'') }}">
			{!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirmar contraseña', 'autocomplete' => 'on']) !!}
			<span class="glyphicon glyphicon-lock form-control-feedback"></span>
			@if ($errors->has('password_confirmation'))
				<span class="help-block">{{ $errors->first('password_confirmation') }}</span>
			@endif
		</div>
	</div>

	<div>
		{!! Form::submit('Restaurar', ['class' => 'btn btn-success btn-block btn-flat']) !!}
	</div>

	<div class="volver">
		<a class="btn btn-info btn-block btn-flat" href="{{ url('login') }}" role="button">Volver</a>
	</div>
	{!! Form::close() !!}
</div>
@endsection


@push('style')
	<style type="text/css">
		.volver {
			margin-top: 5px;
		}
	</style>
@endpush

@push('scripts')
	<script type="text/javascript">
		$('.login-pic').tilt({
			scale: 1.2
		})
	</script>
@endpush

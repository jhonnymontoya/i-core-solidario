@extends('layouts.login')

@section('content')
<div class="login-pic" data-tilt>
	<img src="{{ asset('img/logos/I-Core.png') }}">
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
		<div class="form-group">
			@php
				$valid = $errors->has('usuario') ? 'is-invalid' : '';
			@endphp
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fas fa-user"></i>
					</span>
				</div>
				{!! Form::text('usuario', null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Usuario', 'autocomplete' => 'off', 'autofocus']) !!}
				@if ($errors->has('usuario'))
					<div class="invalid-feedback">{{ $errors->first('usuario') }}</div>
				@endif
			</div>
		</div>
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
		<div class="form-group">
			@php
				$valid = $errors->has('password_confirmation') ? 'is-invalid' : '';
			@endphp
			<div class="input-group">
				<div class="input-group-prepend">
					<span class="input-group-text">
						<i class="fas fa-lock"></i>
					</span>
				</div>
				{!! Form::password('password_confirmation', ['class' => [$valid, 'form-control'], 'placeholder' => 'Confirmar contraseña']) !!}
				@if ($errors->has('password_confirmation'))
					<div class="invalid-feedback">{{ $errors->first('password_confirmation') }}</div>
				@endif
			</div>
		</div>
	</div>

	<div>
		{!! Form::submit('Restaurar', ['class' => 'btn btn-outline-success btn-block btn-flat']) !!}
	</div>

	<div class="volver">
		<a class="btn btn-outline-info btn-block btn-flat" href="{{ url('login') }}" role="button">Volver</a>
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

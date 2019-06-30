@extends('layouts.login')

@section('content')
<div class="login-box-body">
	<p class="login-box-msg">Restaurar contraseña</p>

	@if (session('status'))
		<div class="alert alert-success">
			{{ session('status') }}
		</div>
	@endif
	
	{!! Form::open(['url' => '/password/reset', 'method' => 'post', 'role' => 'form']) !!}
	{!! Form::hidden('token', $token) !!}

		<div class="row">
			<div class="col-md-12">
				<div class="form-group has-feedback {{ ($errors->has('usuario')?'has-error':'') }}">
					{!! Form::text('usuario', null, ['class' => 'form-control', 'placeholder' => 'Usuario', 'autocomplete' => 'off', 'autofocus']) !!}
					<span class="glyphicon glyphicon-user form-control-feedback"></span>
					@if ($errors->has('usuario'))
						<span class="help-block">{{ $errors->first('usuario') }}</span>
					@endif
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group has-feedback {{ ($errors->has('password')?'has-error':'') }}">
					{!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'autocomplete' => 'on']) !!}
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					@if ($errors->has('password'))
						<span class="help-block">{{ $errors->first('password') }}</span>
					@endif
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="form-group has-feedback {{ ($errors->has('password_confirmation')?'has-error':'') }}">
					{!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => 'Confirmar contraseña', 'autocomplete' => 'on']) !!}
					<span class="glyphicon glyphicon-lock form-control-feedback"></span>
					@if ($errors->has('password_confirmation'))
						<span class="help-block">{{ $errors->first('password_confirmation') }}</span>
					@endif
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				{!! Form::submit('Restaurar', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<a class="btn btn-info btn-block btn-flat" href="{{ url('login') }}" role="button">Volver</a>
			</div>
		</div>
	{!! Form::close() !!}
</div>
@endsection


@push('style')
@endpush

@push('scripts')
@endpush

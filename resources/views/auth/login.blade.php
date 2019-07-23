@extends('layouts.login')

@section('content')
<div class="login-pic" data-tilt>
	<img src="{{ asset(Session::get('realmAvatar')) }}">
</div>
<div class="login-form">
	{!! Form::open(['url' => 'login', 'method' => 'post', 'role' => 'form']) !!}
	<div class="text-center">
		<h4>Iniciar sesión</h4>
	</div>
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
		{!! Form::submit('Continuar', ['class' => 'btn btn-success btn-block btn-flat']) !!}
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
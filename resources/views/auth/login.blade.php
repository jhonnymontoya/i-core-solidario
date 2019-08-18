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
		{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success btn-block']) !!}
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
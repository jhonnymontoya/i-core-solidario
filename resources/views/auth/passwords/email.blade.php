@extends('layouts.login')

@section('content')
	@if (session('status'))
		<?php
			$correos = Session::get("correos");
			$enviado = empty($correos) ? false : true;
		?>
		<div class="alert alert-{{ $enviado ? "success" : "warning"}}">
			<?php
				if(!$enviado) {
					echo "Ya que no cuenta con un correo electrónico asociado para enviar el enlace de restauración de contraseña, por favor comuniquese con un asesor.";
				}
				else {
					echo session('status') . "<br>";
					foreach($correos as $correo) {
						echo $correo . "<br>";
					}
				}
			?>
		</div>
	@endif
<div class="login-pic" data-tilt>
	<img src="{{ asset(Session::get('realmAvatar')) }}">
</div>
<div class="login-form">
	{!! Form::open(['url' => '/password/email', 'method' => 'post', 'role' => 'form']) !!}
	<div class="text-center">
		<h4>Restaurar contraseña</h4>
	</div>
	@php
		$usuario = Session::has("usuario") ? Session::get("usuario") : null;
		$usuario = !empty(old('usuario')) ? old('usuario') : $usuario;
	@endphp
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
	$(function(){
		$("input[name='usuario']").focus();

		$('.login-pic').tilt({
			scale: 1.2
		})
	});
</script>
@endpush

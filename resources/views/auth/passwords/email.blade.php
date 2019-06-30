@extends('layouts.login')

@section('content')
<div class="login-box-body">
	<p class="login-box-msg">Restaurar contraseña</p>

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
	
	{!! Form::open(['url' => '/password/email', 'method' => 'post', 'role' => 'form']) !!}
	@php
		$usuario = Session::has("usuario") ? Session::get("usuario") : null;
		$usuario = !empty(old('usuario')) ? old('usuario') : $usuario;
	@endphp
	<div class="row">
		<div class="col-md-12">
			<div class="form-group has-feedback {{ ($errors->has('usuario')?'has-error':'') }}">
				{!! Form::text('usuario', $usuario, ['class' => 'form-control', 'placeholder' => 'Usuario', 'autocomplete' => 'off', 'autofocus']) !!}
				<span class="glyphicon glyphicon-user form-control-feedback"></span>
				@if ($errors->has('usuario'))
					<span class="help-block">{{ $errors->first('usuario') }}</span>
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
<script type="text/javascript">
	$(function(){
		$("input[name='usuario']").focus();
		$("input[name='usuario']").val($("input[name='usuario']").val());
	});
</script>
@endpush

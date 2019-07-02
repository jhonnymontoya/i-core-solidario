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
<div>
	{!! Form::open(['url' => 'login', 'method' => 'post', 'role' => 'form']) !!}
	<div style="290px">
		<div>
			<div class="form-group has-feedback {{ ($errors->has('usuario')?'has-error':'') }}">
				{!! Form::text('usuario', null, ['class' => 'form-control', 'placeholder' => 'Usuario', 'autocomplete' => 'off', 'autofocus']) !!}
				<span class="glyphicon glyphicon-user form-control-feedback"></span>
				@if ($errors->has('usuario'))
					<span class="help-block">{{ $errors->first('usuario') }}</span>
				@endif
			</div>
		</div>

			<div class="row">
				<div class="col-md-12">
					{!! Form::submit('Continuar', ['class' => 'btn btn-primary btn-block btn-flat']) !!}
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
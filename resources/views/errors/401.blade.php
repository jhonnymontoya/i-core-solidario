<?php
	$layout = 'layouts.invitado';
	if(Auth::getUser() instanceof \App\Models\Sistema\Usuario){
		$layout = 'layouts.admin';
	}
	elseif(Auth::getUser() instanceof \App\Models\Sistema\UsuarioWeb){
		$layout = 'layouts.consulta';
	}
	else{
		$layout = 'layouts.invitado';
	}
?>
@extends($layout)

@section('content')
<div class="content-wrapper">
	<br><br>
	<div class="row">
		<div class="col-md-8 col-md-offset-2">
			<div class="callout callout-warning">
				<h4>No autorizado!</h4>
				<p>{{ $exception->getMessage() }}</p>
			</div>
		</div>
	</div>
</div>
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
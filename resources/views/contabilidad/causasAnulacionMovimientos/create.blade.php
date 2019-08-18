@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Causa de anulación para movimientos
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Causa de anulación para movimientos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		{!! Form::open(['url' => 'causaAnulacionMovimiento', 'method' => 'post', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Crear nueva causa de anulación para movimientos</h3>
					</div>
					<div class="card-body">
						<div class="row form-horizontal">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
									<label class="col-sm-1 control-label">
										@if ($errors->has('nombre'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Nombre
									</label>
									<div class="col-sm-11">
										{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
										@if ($errors->has('nombre'))
											<span class="help-block">{{ $errors->first('nombre') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="card-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('causaAnulacionMovimiento') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
	});
</script>
@endpush

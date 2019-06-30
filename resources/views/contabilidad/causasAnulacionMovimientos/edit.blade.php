@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Causa de anulación para movimientos
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Causa de anulación para movimientos</li>
		</ol>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		{!! Form::model($causa, ['url' => ['causaAnulacionMovimiento', $causa], 'method' => 'put', 'role' => 'form', 'id' => 'comprobante']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Crear nueva causa de anulación para movimientos</h3>
					</div>
					<div class="box-body">
						<div class="row form-horizontal">
							<div class="col-md-6">
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
							{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('esta_activa'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Estado
										</label>
										<div class="col-sm-8">
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-primary {{ $causa->esta_activa ? 'active' : ''}}">
													{!! Form::radio('esta_activa', '1', $causa->esta_activa ? true : false) !!}ACTIVA
												</label>
												<label class="btn btn-danger {{ $causa->esta_activa ? '' : 'active'}}">
													{!! Form::radio('esta_activa', '0', $causa->esta_activa ? false : true) !!}INACTIVA
												</label>
											</div>
											@if ($errors->has('esta_activa'))
												<span class="help-block">{{ $errors->first('esta_activa') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
						</div>
					</div>
					<div class="box-footer">
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

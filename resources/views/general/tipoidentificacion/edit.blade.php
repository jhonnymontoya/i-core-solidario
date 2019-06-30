@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tipos de identificación
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Tipos de identificación</li>
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
		<div class="box box-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($tipoIdentificacion, ['url' => ['tipoIdentificacion', $tipoIdentificacion], 'method' => 'PUT', 'role' => 'form']) !!}
			<div class="box-header with-border">
				<h3 class="box-title">Editar tipo de identificación</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('aplicacion')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('aplicacion'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Tipo de persona
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary{{ $tipoIdentificacion->aplicacion=='NATURAL'?' active':'' }}">
									{!! Form::radio('aplicacion', 'NATURAL', true) !!}Natural
								</label>
								<label class="btn btn-primary{{ $tipoIdentificacion->aplicacion=='JURÍDICA'?' active':'' }}">
									{!! Form::radio('aplicacion', 'JURÍDICA', false) !!}Jurídica
								</label>
							</div>
							@if ($errors->has('aplicacion'))
								<span class="help-block">{{ $errors->first('aplicacion') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('codigo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Código
							</label>
							{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'CC, TI, CE, NIT', 'required']) !!}
							@if ($errors->has('codigo'))
								<span class="help-block">{{ $errors->first('codigo') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre del tipo de identificación', 'required']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('esta_activo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Activo
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<label class="btn btn-primary{{ $tipoIdentificacion->esta_activo?' active':'' }}">
									{!! Form::radio('esta_activo', '1', true) !!}Sí
								</label>
								<label class="btn btn-danger{{ !$tipoIdentificacion->esta_activo?' active':'' }}">
									{!! Form::radio('esta_activo', '0', false) !!}No
								</label>
							</div>
							@if ($errors->has('activo'))
								<span class="help-block">{{ $errors->first('esta_activo') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="box-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('tipoIdentificacion') }}" class="btn btn-danger pull-right">Cancelar</a>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='codigo']").enfocar();
	});
</script>
@endpush

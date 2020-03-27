@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de identificación
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Tipos de identificación</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($tipoIdentificacion, ['url' => ['tipoIdentificacion', $tipoIdentificacion], 'method' => 'PUT', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar tipo de identificación</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    <label class="control-label">Tipo de persona</label>
							    <div>
							        @php
							            $valid = $errors->has('aplicacion') ? 'is-invalid' : '';
							            $aplicacion = empty(old('aplicacion')) ? $tipoIdentificacion->aplicacion : old('aplicacion');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $aplicacion ? 'active' : '' }}">
							                {!! Form::radio('aplicacion', 'NATURAL', ($aplicacion ? true : false), ['class' => [$valid]]) !!}Natural
							            </label>
							            <label class="btn btn-primary {{ !$aplicacion ? 'active' : '' }}">
							                {!! Form::radio('aplicacion', 'JURIDICA', (!$aplicacion ? true : false ), ['class' => []]) !!}Jurídica
							            </label>
							        </div>
							        @if ($errors->has('aplicacion'))
							            <div class="invalid-feedback">{{ $errors->first('aplicacion') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('codigo') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Código</label>
							    {!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'CC, TI, CE, NIT', 'autofocus']) !!}
							    @if ($errors->has('codigo'))
							        <div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
							    @endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('nombre') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Nombre</label>
							    {!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre del tipo de identificación', 'autofocus']) !!}
							    @if ($errors->has('nombre'))
							        <div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
							    @endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    <label class="control-label">¿Esta activo?</label>
							    <div>
							        @php
							            $valid = $errors->has('esta_activo') ? 'is-invalid' : '';
							            $activo = empty(old('esta_activo')) ? $tipoIdentificacion->esta_activo : old('esta_activo');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $activo ? 'active' : '' }}">
							                {!! Form::radio('esta_activo', 1, ($activo ? true : false), ['class' => [$valid]]) !!}Sí
							            </label>
							            <label class="btn btn-danger {{ !$activo ? 'active' : '' }}">
							                {!! Form::radio('esta_activo', 0, (!$activo ? true : false ), ['class' => []]) !!}No
							            </label>
							        </div>
							        @if ($errors->has('esta_activo'))
							            <div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoIdentificacion') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
			</div>
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

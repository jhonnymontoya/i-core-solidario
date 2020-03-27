@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipos de indicadores
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Tipos de indicadores</li>
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
				{!! Form::open(['url' => 'tipoIndicador', 'method' => 'post', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nuevo tipo de indicador</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-5">
							<div class="form-group">
							    @php
							        $valid = $errors->has('codigo') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Código</label>
							    {!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'autofocus']) !!}
							    @if ($errors->has('codigo'))
							        <div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
							    @php
							        $valid = $errors->has('periodicidad') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Periodicidad de actualización</label>
							    {!! Form::select('periodicidad', $periodicidades, null, ['class' => [$valid, 'form-control'], 'placeholder' => 'Seleccione una opción']) !!}
							    @if ($errors->has('periodicidad'))
							        <div class="invalid-feedback">{{ $errors->first('periodicidad') }}</div>
							    @endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
							    <label class="control-label">Variable</label>
							    <div>
							        @php
							            $valid = $errors->has('variable') ? 'is-invalid' : '';
							            $variable = empty(old('variable')) ? 'PORCENTAJE' : old('variable');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $variable ? 'active' : '' }}">
							                {!! Form::radio('variable', 'PORCENTAJE', ($variable ? true : false), ['class' => [$valid]]) !!}%
							            </label>
							            <label class="btn btn-primary {{ !$variable ? 'active' : '' }}">
							                {!! Form::radio('variable', 'VALOR', (!$variable ? true : false ), ['class' => []]) !!}$
							            </label>
							        </div>
							        @if ($errors->has('variable'))
							            <div class="invalid-feedback">{{ $errors->first('variable') }}</div>
							        @endif
							    </div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('descripcion') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Descripción</label>
							    {!! Form::text('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción', 'autofocus']) !!}
							    @if ($errors->has('descripcion'))
							        <div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
							    @endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('tipoIndicador') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Parametros institucionales
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Parametros institucionales</li>
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
				{!! Form::model($parametro, ['url' => ['parametrosInstitucionales', $parametro], 'method' => 'PUT', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar parametro institucional</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
							    @php
							        $valid = $errors->has('modulo') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Código</label>
							    {!! Form::text('modulo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'readonly']) !!}
							    @if ($errors->has('modulo'))
							        <div class="invalid-feedback">{{ $errors->first('modulo') }}</div>
							    @endif
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
							    {!! Form::text('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'readonly']) !!}
							    @if ($errors->has('descripcion'))
							        <div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
							    @endif
							</div>
						</div>
					</div>
					<div class="row">
						@if($parametro->tipo_parametro == 'VALOR' || $parametro->tipo_parametro == 'VALOR_INDICADOR')
						<div class="col-md-6">
							<div class="form-group">
							    @php
							        $valid = $errors->has('valor') ? 'is-invalid' : '';
							    @endphp
							    <label class="control-label">Valor</label>
							    {!! Form::text('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor']) !!}
							    @if ($errors->has('valor'))
							        <div class="invalid-feedback">{{ $errors->first('valor') }}</div>
							    @endif
							</div>
						</div>
						@endif
						@if($parametro->tipo_parametro == 'INDICADOR' || $parametro->tipo_parametro == 'VALOR_INDICADOR')
						<div class="col-md-6">
							<div class="form-group">
							    <label class="control-label">Indicador</label>
							    <div>
							        @php
							            $valid = $errors->has('indicador') ? 'is-invalid' : '';
							            $indicador = empty(old('indicador')) ? $parametro->indicador : old('indicador');
							        @endphp
							        <div class="btn-group btn-group-toggle" data-toggle="buttons">
							            <label class="btn btn-primary {{ $indicador ? 'active' : '' }}">
							                {!! Form::radio('indicador', 1, ($indicador ? true : false), ['class' => [$valid]]) !!}Sí
							            </label>
							            <label class="btn btn-danger {{ !$indicador ? 'active' : '' }}">
							                {!! Form::radio('indicador', 0, (!$indicador ? true : false ), ['class' => []]) !!}No
							            </label>
							        </div>
							        @if ($errors->has('indicador'))
							            <div class="invalid-feedback">{{ $errors->first('indicador') }}</div>
							        @endif
							    </div>
							</div>
						</div>
						@endif
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('parametrosInstitucionales') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Alertas
						<small>SARLAFT</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> SARLAFT</a></li>
						<li class="breadcrumb-item active">Aleras</li>
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
				{!! Form::model($alerta, ['url' => ['alertasSarlaft', $alerta], 'method' => 'put', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Actualizar alerta SARLAFT</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-auto">
							<h4>Actualizando la alerta: {{ $alerta->nombre }}</h4>
						</div>
					</div>

					<br><br>
					<div class="row">
						<div class="col-3">
							<div class="form-group">
								<label class="control-label">¿Diario?</label>
								<div>
									@php
										$valid = $errors->has('diario') ? 'is-invalid' : '';
										$diario = empty(old('diario')) ? $alerta->diario : old('diario');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $diario ? 'active' : '' }}">
											{!! Form::radio('diario', 1, ($diario ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$diario ? 'active' : '' }}">
											{!! Form::radio('diario', 0, (!$diario ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('diario'))
										<div class="invalid-feedback">{{ $errors->first('diario') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-3">
							<div class="form-group">
								<label class="control-label">¿Semanal?</label>
								<div>
									@php
										$valid = $errors->has('semanal') ? 'is-invalid' : '';
										$semanal = empty(old('semanal')) ? $alerta->semanal : old('semanal');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $semanal ? 'active' : '' }}">
											{!! Form::radio('semanal', 1, ($semanal ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$semanal ? 'active' : '' }}">
											{!! Form::radio('semanal', 0, (!$semanal ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('semanal'))
										<div class="invalid-feedback">{{ $errors->first('semanal') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-3">
							<div class="form-group">
								<label class="control-label">¿Mensual?</label>
								<div>
									@php
										$valid = $errors->has('mensual') ? 'is-invalid' : '';
										$mensual = empty(old('mensual')) ? $alerta->mensual : old('mensual');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $mensual ? 'active' : '' }}">
											{!! Form::radio('mensual', 1, ($mensual ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$mensual ? 'active' : '' }}">
											{!! Form::radio('mensual', 0, (!$mensual ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('mensual'))
										<div class="invalid-feedback">{{ $errors->first('mensual') }}</div>
									@endif
								</div>
							</div>
						</div>

						<div class="col-3">
							<div class="form-group">
								<label class="control-label">¿Anual?</label>
								<div>
									@php
										$valid = $errors->has('anual') ? 'is-invalid' : '';
										$anual = empty(old('anual')) ? $alerta->anual : old('anual');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $anual ? 'active' : '' }}">
											{!! Form::radio('anual', 1, ($anual ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$anual ? 'active' : '' }}">
											{!! Form::radio('anual', 0, (!$anual ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('anual'))
										<div class="invalid-feedback">{{ $errors->first('anual') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Actualizar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('alertasSarlaft') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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

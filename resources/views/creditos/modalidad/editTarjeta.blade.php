@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Modalidades de créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Modalidades de créditos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($modalidad, ['route' => ['modalidadCreditoUpdateTarjeta', $modalidad], 'method' => 'put', 'role' => 'form']) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar modalidad</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código
								</label>
								{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('es_exclusivo_de_socios')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('es_exclusivo_de_socios'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Exclusiva para socios?
								</label>
								<br>
								<?php
									$es_exclusivo_de_socios = $modalidad->es_exclusivo_de_socios;
									if(old('es_exclusivo_de_socios') == '0')
									{
										$es_exclusivo_de_socios = false;
									}
									elseif(old('es_exclusivo_de_socios') == '1')
									{
										$es_exclusivo_de_socios = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $es_exclusivo_de_socios ? 'active' : ''}}">
										{!! Form::radio('es_exclusivo_de_socios', '1', $es_exclusivo_de_socios ? true : false) !!}Sí
									</label>
									<label class="btn btn-outline-danger {{ $es_exclusivo_de_socios ? '' : 'active'}}">
										{!! Form::radio('es_exclusivo_de_socios', '0', $es_exclusivo_de_socios? false : true) !!}No
									</label>
								</div>
								@if ($errors->has('es_exclusivo_de_socios'))
									<span class="help-block">{{ $errors->first('es_exclusivo_de_socios') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<br>
								<?php
									$esta_activa = $modalidad->esta_activa;
									if(old('esta_activa') == '0')
									{
										$esta_activa = false;
									}
									elseif(old('esta_activa') == '1')
									{
										$esta_activa = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-outline-primary {{ $esta_activa ? 'active' : ''}}">
										{!! Form::radio('esta_activa', '1', $esta_activa ? true : false) !!}Activa
									</label>
									<label class="btn btn-outline-danger {{ $esta_activa ? '' : 'active'}}">
										{!! Form::radio('esta_activa', '0', $esta_activa? false : true) !!}Inactiva
									</label>
								</div>
								@if ($errors->has('esta_activa'))
									<span class="help-block">{{ $errors->first('esta_activa') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								{!! Form::textarea('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<span class="help-block">{{ $errors->first('descripcion') }}</span>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li role="presentation" class="active">
							<a href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active">
							<br>
							<div class="row form-horizontal">
								<div class="col-md-12">
									<p>Define si la modalidad '<strong>{{ $modalidad->codigo }} - {{ $modalidad->nombre }}</strong>', será usada en el módulo de tarjeta</p>
									<br>
									@if ($cantidadSolicitudes > 0)
										{!! Form::hidden("uso_para_tarjeta", $modalidad->uso_para_tarjeta ? '1' : '0') !!}
										<div class="row">
											<div class="col-md-4 text-right"><strong>Modalidad para uso del modulo de tarjeta:</strong></div>
											<div class="col-md-8"><span class="badge badge-pill badge-{{ $modalidad->uso_para_tarjeta ? 'success' : 'default' }}">{{ $modalidad->uso_para_tarjeta ? 'Sí' : 'No' }}</span>, Se encontrarón <a href="{{ url('solicitudCredito?modalidad=' . $modalidad->id) }}">{{ $cantidadSolicitudes }}</a> solicitudes de crédito.</div>
										</div>
									@else
										<div class="form-group {{ ($errors->has('uso_para_tarjeta')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('uso_para_tarjeta'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Modalidad para uso del modulo de tarjeta
											</label>
											<div class="col-sm-8">
												<?php
													$usoParaTarjeta = $modalidad->uso_para_tarjeta;
													$usoParaTarjeta = empty(old('uso_para_tarjeta')) ? $usoParaTarjeta : old('uso_para_tarjeta');
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-outline-primary {{ $usoParaTarjeta ? 'active' : '' }}">
														{!! Form::radio('uso_para_tarjeta', '1', ($usoParaTarjeta? true : false)) !!}Sí
													</label>
													<label class="btn btn-outline-primary {{ !$usoParaTarjeta ? 'active' : '' }}">
														{!! Form::radio('uso_para_tarjeta', '0', (!$usoParaTarjeta ? true : false)) !!}No
													</label>
												</div>
												@if ($errors->has('uso_para_tarjeta'))
													<span class="help-block">{{ $errors->first('uso_para_tarjeta') }}</span>
												@endif
											</div>
										</div>
									@endif
									<br>
									<small class="text-danger">Esta opción no podrá ser actualizada si a la modalidad cuenta con solicitudes de crédito</small>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('modalidadCredito') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
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
@endpush

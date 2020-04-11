@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Socios
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Socios</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="row">
			<div class="col-md-12 text-right">
				<a href="{{ url('socio/consulta') }}?socio={{ $socio->id }}&fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>

		<div class="container-fluid">
			<h5>{{ $socio->tercero->tipoIdentificacion->codigo }} {{ $socio->tercero->nombre_completo }}</h5>
		</div>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Documentación</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-10 col-md-offset-1 col-sm-12">
							<ul class="list-unstyled">
								<li>
									<strong>
										<a href="#" data-toggle="modal" data-target="#mct">
											<i class="fa fa-file-pdf-o"></i> Certificado tributario
										</a>:
									</strong> Descargue el certificado tributario
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 text-right">
				<a href="{{ url('socio/consulta') }}?socio={{ $socio->id }}&fecha={{ $fechaConsulta }}" class="btn btn-outline-danger">Volver</a>
			</div>
		</div>
		<br>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
<div class="modal fade" id="mct" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["route" => ["socioConsulta.documentacion", $socio->id], "method" => "get", "target" => "_blank"]) !!}
	{!! Form::hidden("certificado", "certificadoTributario") !!}
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="mLabel">Certificado tributario</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						Descargue el certificado tributario
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<label class="col-sm-3 control-label">Seleccione año</label>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<?php
							$anios = [];
							$entidad = Auth::getSession()->get('entidad');
							$anioInicio = 2018;
							if($entidad->fecha_inicio_contabilidad->year > $anioInicio) {
								$anioInicio = $entidad->fecha_inicio_contabilidad->year;
							}
							$anioActual = date("Y") - 1;
							while($anioActual >= $anioInicio) {
								$anios[$anioActual] = $anioActual;
								$anioActual--;
							}
							if(!count($anios)) $anios[date("Y")] = date("Y");
						?>
						{!! Form::select('anio', $anios, null, ['class' => 'form-control']) !!}
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
				{!! Form::submit("Descargar", ["class" => "btn btn-outline-success"]) !!}
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection

@push('style')
@endpush

@push('scripts')
@endpush
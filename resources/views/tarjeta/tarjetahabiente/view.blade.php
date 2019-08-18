@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tarjetahabiente
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Tarjetahabiente</li>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('error') }}
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('tarjetaHabiente/create') }}?tercero_id={{ $tercero->id }}" class="btn btn-primary">Crear nueva tarjeta</a>
			</div>
		</div>
		<br>
		<div class="row">
			<div class="col-md-12">
				<div class="card card-{{ $errors->count()?'danger':'success' }}">
					<div class="card-header with-border">
						<h3 class="card-title">Editar producto para {{ $tercero->nombre_corto }}</h3>
					</div>
					<div class="card-body">
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-6 col-sm-12">
										@php
											$link = $socio ? sprintf("%s?socio=%s&fecha=%s", url('socio/consulta'), $socio->id, date("d/m/Y")) : null;
											$antiguedad = " - ";
											if($socio) {
												$antiguedad = sprintf("%s (%s)", $socio->fecha_antiguedad, $socio->fecha_antiguedad->diffForHumans());
											}
											$label = "label-default";
											switch (optional($socio)->estado) {
												case 'ACTIVO':
													$label = 'bg-green';
													break;
												case 'NOVEDAD':
													$label = 'bg-orange';
													break;
												case 'RETIRO':
													$label = 'bg-maroon';
													break;
												case 'LIQUIDADO':
													$label = 'bg-red';
													break;
												case 'PROCESO':
													$label = 'bg-light-blue';
													break;
											}
										@endphp
										<dl class="dl-horizontal">
											<dt>Documento</dt>
											<dd>
												@if ($link)
													<a href="{{ $link }}" target="_blank">
														{{ $tercero->tipoIdentificacion->codigo }} {{ number_format($tercero->numero_identificacion) }}
														<small><i class="fa fa-external-link"></i></small>
													</a>
												@else
													{{ $tercero->tipoIdentificacion->codigo }} {{ number_format($tercero->numero_identificacion) }}
												@endif
											</dd>
											<dt>Empresa</dt>
											<dd>{{ $pagaduria->nombre ?? " - "}}</dd>
											<dt>Antigüedad</dt>
											<dd>{{ $antiguedad }}</dd>
										</dl>
									</div>
									<div class="col-md-6 col-sm-12">
										<dl class="dl-horizontal">
											<dt>Nombre</dt>
											<dd>
												@if ($link)
													<a href="{{ $link }}" target="_blank">
														{{ $tercero->nombre }}
														<small><i class="fa fa-external-link"></i></small>
													</a>
												@else
													{{ $tercero->nombre }}
												@endif
											</dd>
											<dt>Periodicidad</dt>
											<dd>{{ $pagaduria->periodicidad_pago ?? " - "}}</dd>
											<dt>Estado</dt>
											<dd>
												<span class="label {{ $label }}">
													{{ $socio->estado ?? "No asociado" }}
												</span>
											</dd>
										</dl>
									</div>
								</div>
							</div>
						</div>
						<hr>
						@if (!$tarjetaHabientes->count())
							No se encontraon tarjetas, <a href="{{ url('tarjetaHabiente/create') }}?tercero_id={{ $tercero->id }}" class="btn btn-primary btn-xs">Crear nueva tarjeta</a>
						@else
							<div class="row">
								<div class="col-md-12">
									<span class="label label-primary">{{ $tarjetaHabientes->count() }}</span> tarjetas.
								</div>
							</div>
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-striped">
										<thead>
											<tr>
												<th>Tarjeta</th>
												<th>Producto</th>
												<th>Fecha asignación</th>
												<th>Estado</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach ($tarjetaHabientes as $tarjetaHabiente)
												@php
													$label = "default";
													switch ($tarjetaHabiente->estado) {
														case 'ASIGNADA':
															$label = "info";
															break;
														case 'ACTIVA':
															$label = "success";
															break;
														case 'INACTIVA':
															$label = "warning";
															break;
														case 'BLOQUEADA':
														case 'CANCELADA':
															$label = "danger";
															break;
														default:
															$label = "default";
															break;
													}
												@endphp
												<tr>
													<td>
														<a href="{{ route('tarjetaHabiente.edit', [$tercero->id, $tarjetaHabiente->id]) }}">
															{{ $tarjetaHabiente->tarjeta->numeroFormateado }}
														</a>
													</td>
													<td>{{ $tarjetaHabiente->producto->nombre_completo }}</td>
													<td>{{ $tarjetaHabiente->fecha_asignacion }}</td>
													<td>
														<span class="label label-{{ $label }}">
															{{ $tarjetaHabiente->estado }}
														</span>
													</td>
													<td>
														<a href="{{ route('tarjetaHabiente.edit', [$tercero->id, $tarjetaHabiente->id]) }}" class="btn btn-info btn-xs">
															<i class="fa fa-edit"></i>
														</a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						@endif
					</div>
					<div class="card-footer">
						<span class="label label-primary">{{ $tarjetaHabientes->count() }}</span> tarjetas.
						<a href="{{ url('tarjetaHabiente') }}" class="btn btn-danger pull-right">Volver</a>
					</div>
				</div>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Extracto Social
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Extracto Social</li>
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
		<div class="row">
			<div class="col-md-12">
				<a href="{{ url('extractoSocial/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $configuraciones->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Configuraciones de extractos sociales</h3>
				</div>
				<div class="card-body">
					@if(!$configuraciones->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron configuraciones de extractos sociales <a href="{{ url('extractoSocial/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th></th>
										<th colspan="2" class="text-center">Tasa promedio externa</th>
										<th colspan="2" class="text-center">Gasto social</th>
										<th colspan="2" class="text-center">Fecha socio visible</th>
										<th colspan="5" class="text-center">Mensaje</th>
										<th></th>
									</tr>
									<tr>
										<th>Año</th>
										<th class="text-center">Ahorros</th>
										<th class="text-center">Créditos</th>
										<th class="text-center">Individual</th>
										<th class="text-center">Total</th>
										<th>Inicio</th>
										<th>Fin</th>
										<th>General</th>
										<th>Ahorros</th>
										<th>Créditos</th>
										<th>Convenios</th>
										<th>Inversión social</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($configuraciones as $configuracion)
										<tr>
											<td>
												<a href="{{ route('extractoSocial.edit', $configuracion->id) }}" title="Editar">
													{{ $configuracion->anio }}
												</a>
											</td>
											<td class="text-right">{{ number_format($configuracion->tasa_promedio_ahorros_externa, 2) }}%</td>
											<td class="text-right">{{ number_format($configuracion->tasa_promedio_creditos_externa, 2) }}%</td>
											<td class="text-right">${{ number_format($configuracion->gasto_social_individual, 0) }}</td>
											<td class="text-right">${{ number_format($configuracion->gasto_social_total, 0) }}</td>
											<td class="text-right">{{ $configuracion->fecha_inicio_socio_visible }}</td>
											<td class="text-right">{{ $configuracion->fecha_fin_socio_visible }}</td>
											<td>{{ Str::limit($configuracion->mensaje_general, 20) }}</td>
											<td>{{ Str::limit($configuracion->mensaje_ahorros, 20) }}</td>
											<td>{{ Str::limit($configuracion->mensaje_creditos, 20) }}</td>
											<td>{{ Str::limit($configuracion->mensaje_convenios, 20) }}</td>
											<td>{{ Str::limit($configuracion->mensaje_inversion_social, 20) }}</td>
											<td>
												<a href="{{ route('extractoSocial.edit', $configuracion->id) }}" class="btn btn-outline-info btn-sm" title="Editar">
													<i class="fa fa-edit"></i>
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $configuraciones->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $configuraciones->total()?'primary':'danger' }}">{{ $configuraciones->total() }}</span> elementos.
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
<script type="text/javascript">
	$(function(){
		$(window).formularioCrear("{{ url('extractoSocial/create') }}");
	});
</script>
@endpush

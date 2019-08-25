@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						SDAT
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">SDAT</li>
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
		<div class="row">
			<div class="col-md-1">
				<a href="{{ url('SDAT/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $sdats->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">SDAT</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => 'SDAT', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-8 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							{!! Form::select('estado', [true => 'Activa', false => 'Inactiva'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$sdats->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron SDAT <a href="{{ url('SDAT/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Tipo</th>
										<th>Socio</th>
										<th class="text-center">Valor</th>
										<th class="text-center">Tasa E.A.</th>
										<th>Fecha constitución</th>
										<th>Fecha vencimiento</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php
										foreach($sdats as $sdat) {
											$tercero = $sdat->socio->tercero;
											$nombre = sprintf(
												"%s %s - %s",
												$tercero->tipoIdentificacion->codigo,
												$tercero->numero_identificacion,
												$tercero->nombre_corto
											);
											$label = "default";
											switch ($sdat->estado) {
												case 'SOLICITUD':
													$label = "warning";
													break;
												case 'CONSTITUIDO':
													$label = "success";
													break;
												case 'RENOVADO':
													$label = "success";
													break;
												case 'PRORROGADO':
													$label = "success";
													break;
												case 'SALDADO':
													$label = "default";
													break;
												case 'ANULADO':
													$label = "danger";
													break;
												default:
													$label = "default";
													break;
											}
											?>
												<tr>
													<td>{{ $sdat->tipoSDAT->codigo }}</td>
													<td>{{ $nombre }}</td>
													<td class="text-right">${{ number_format($sdat->valor) }}</td>
													<td class="text-right">{{ number_format($sdat->tasa, 2) }}%</td>
													<td>{{ $sdat->fecha_constitucion }}</td>
													<td>{{ $sdat->fecha_vencimiento }}</td>
													<td><label class="badge badge-pill badge-{{ $label }}"></span>{{ $sdat->estado }}</label></td>
													<td>
														@if ($sdat->estado == "SOLICITUD")
															<a href="{{ route('SDAT.constituir', $sdat->id) }}" class="btn btn-outline-success btn-sm" title="Constituir">
																<i class="fa fa-money"></i>
															</a>
														@endif
														@if ($sdat->estado == "CONSTITUIDO")
															<a href="{{ route('SDAT.saldar', $sdat->id) }}" class="btn btn-outline-primary btn-sm" title="Saldar SDAT">
																<i class="fa fa-dollar"></i>
															</a>
														@endif
														@if ($sdat->estado == "RENOVADO")
															<a href="{{ route('SDAT.saldar', $sdat->id) }}" class="btn btn-outline-primary btn-sm" title="Saldar SDAT">
																<i class="fa fa-dollar"></i>
															</a>
														@endif
														@if ($sdat->estado == "PRORROGADO")
															<a href="{{ route('SDAT.saldar', $sdat->id) }}" class="btn btn-outline-primary btn-sm" title="Saldar SDAT">
																<i class="fa fa-dollar"></i>
															</a>
														@endif
													</td>
												</tr>
											<?php
										}
									?>
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $sdats->appends(Request::only('name'))->render() !!}
						</div>
					</div>			
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $sdats->total()?'primary':'danger' }}">{{ $sdats->total() }}</span> elementos.
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
	$(window).keydown(function(event) {
		if(event.altKey && event.keyCode == 78) { 
			window.location.href = "{{ url('SDAT/create') }}";
			event.preventDefault(); 
		}
	});
</script>
@endpush

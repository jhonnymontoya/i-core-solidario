@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Control cierres de periodos
			<small>General</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">General</a></li>
			<li class="active">Control cierres de periodos</li>
		</ol>
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
		<div class="card card-{{ $periodos->total()?'primary':'danger' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Resumen</h3>
			</div>
			<div class="card-body">
				@if(!$periodos->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron periodos
							</div>
						</div>
					</p>
				@else
					<br>
					@foreach($periodos as $periodo)
						<div class="row">
							<div class="col-md-12">
								<div class="info-card">
									<div class="info-card-content">
										<span class="info-card-number">
											Periodo {{ $periodo->mes }} - {{ $periodo->anio }}
										</span>
										<span class="info-card-text">
											<div class="row">
												@php
													$contador = 1;
												@endphp
												@foreach ($periodo->entidad->getModulos() as $modulo)
													@php
														$cerrado = $periodo->moduloCerrado($modulo->id);
														$link = '';
														switch ($modulo->id) {
															case 2: //Contabilidad
																$link = route('cierreModulosDetalleContabilidad', $periodo->id);
																break;
															case 3: //Convenios
																$link = '';
																break;
															case 4: //Nómina
																$link = '';
																break;
															case 6: //Ahorros y aportes
																$link = route('cierreModulosDetalleAhorros', $periodo->id);
																break;
															case 7: //Cartera
																$link = route('cierreModulosDetalleCartera', $periodo->id);
																break;
															case 10: //Socios
																$link = route('cierreModulosDetalleSocios', $periodo->id);
																break;
															default:
																$link = '';
																break;
														}
													@endphp
													@if (!$cerrado)
														<a href="{{ $link }}">
													@endif
														<div class="col-md-2">
															<div class="small-card bg-{{ $cerrado ? 'green' : 'red' }}">
																<div class="inner">
																	<h3>{{ $contador++ }}</h3>
																	<p>{{ $modulo->nombre }}</p>
																</div>
																<div class="icon">
																	<i class="fa {{ $modulo->icono }}"></i>
																</div>
															</div>
														</div>
													@if (!$cerrado)
														</a>
													@endif
												@endforeach
											</div>
										</span>

										<span class="info-card-text">
											@php
												$porcentajeProgreso = number_format($periodo->porcentajeProgreso(), 0);
											@endphp
											<div class="progress">
											<div class="progress-bar progress-bar-striped" role="progressbar" aria-valuenow="{{ $porcentajeProgreso }}" aria-valuemin="0" aria-valuemax="100" style="width: {{ $porcentajeProgreso }}%">
											<span class="">Progreso {{ $porcentajeProgreso }}%</span>
											</div>
											</div>
										</span>

										<span class="info-card-text">
											<a href="{{ route('cierreModulosDetalle', $periodo->id) }}" class="btn btn-success btn-block btn-xs btn-flat pull-right">Ir a resumen de módulos</a>
										</span>
									</div>
								</div>
							</div>
						</div>
					@endforeach
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $periodos->appends(Request::only('name', 'tipo', 'inicio', 'fin', 'estado'))->render() !!}
					</div>
				</div>
			</div>
			<div class="card-footer">
				<span class="label label-{{ $periodos->total()?'primary':'danger' }}">
					{{ $periodos->total() }}
				</span>&nbsp;elementos.
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	/*.info-card{
		border:1px solid #333333;
	}*/
	.info-card .info-card-content .info-card-text .progress{
		border-radius: 1px;
		background-color: #f5f5f5;
		height: 20px;
		margin: 5px -10px 5px -10px;
	}
	.info-card .info-card-content .info-card-text .progress .progress-bar{
		border-radius: 1px;
		background-color: #3c8dbc;
		background-image: linear-gradient(45deg,rgba(255,255,255,.15) 25%,transparent 25%,transparent 50%,rgba(255,255,255,.15) 50%,rgba(255,255,255,.15) 75%,transparent 75%,transparent)
	}
	.info-card-content{
		margin-left: 5px;
	}
</style>
@endpush

@push('scripts')
@endpush
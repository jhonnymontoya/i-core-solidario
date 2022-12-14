@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Control cierres de periodos
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Control cierres de periodos</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $periodos->total()?'primary':'danger' }} card-outline">
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
									<div class="info-box">
										<div class="info-box-content">
											<span class="info-box-number">
												Periodo {{ $periodo->mes }} - {{ $periodo->anio }}
											</span>
											<span class="info-box-text">
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
														<div class="col-md-2">
															<div class="small-box bg-{{ $cerrado ? 'green' : 'red' }}">
																<div class="inner">
																	<h3>{{ $contador++ }}</h3>
																	<p>{{ $modulo->nombre }}</p>
																</div>
																<div class="icon">
																	<i class="fas {{ $modulo->icono }}"></i>
																</div>
																@if (!$cerrado)
																	<a href="{{ $link }}" class="small-box-footer">Cerrar <i class="fas fa-arrow-circle-right"></i></a>
																@else
																	<span class="small-box-footer">&nbsp;</span>
																@endif
															</div>
														</div>
													@endforeach
												</div>
											</span>

											<span class="info-box-text">
												@php
													$porcentajeProgreso = number_format($periodo->porcentajeProgreso(), 0);
												@endphp
												<div class="progress-group">
													Progreso
													<span class="float-right"><b>{{ $porcentajeProgreso }}</b>/100</span>
													<div class="progress progress-sm">
														<div class="progress-bar bg-primary" style="width: {{ $porcentajeProgreso }}%"></div>
													</div>
												</div>
											</span>
											<span class="info-box-text">
												<a href="{{ route('cierreModulosDetalle', $periodo->id) }}" class="btn btn-outline-success btn-block btn-sm btn-flat pull-right">Ir a resumen de módulos</a>
											</span>
										</div>
									</div>
								</div>
							</div>
						@endforeach
					@endif
					<br>
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $periodos->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $periodos->total()?'primary':'danger' }}">
						{{ $periodos->total() }}
					</span>&nbsp;elementos.
				</div>
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

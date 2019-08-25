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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Proceso de cierres periodo {{ $periodo->mes }} - {{ $periodo->anio }} Contabilidad</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					@php
						$tieneAlertasTipoA = false;
						$tieneAlertasTipoB = false;
						$tieneAlertasTipoC = false;
					@endphp
					<div class="alert alert-danger"><h4><i class="fa fa-ban"></i> Alertas tipo A</h4></div>
					<ul>
					@if (count($comprobantesDescuadrados['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesDescuadrados['A']) }} comprobantes descuadrados</h4>
						</li>
					@endif
					@if (count($comprobantesBorradorConImpuesto['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorradorConImpuesto['A']) }} comprobantes en borrador con impuestos</h4>
						</li>
					@endif
					@if (count($comprobantesBorrador['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorrador['A']) }} comprobantes en borrador</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoA)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>

					<div class="alert alert-warning"><h4><i class="fa fa-exclamation-triangle"></i> Alertas tipo B</h4></div>
					<ul>
					@if (count($comprobantesDescuadrados['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesDescuadrados['B']) }} comprobantes descuadrados</h4>
						</li>
					@endif
					@if (count($comprobantesBorradorConImpuesto['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorradorConImpuesto['B']) }} comprobantes en borrador con impuestos</h4>
						</li>
					@endif
					@if (count($comprobantesBorrador['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorrador['B']) }} comprobantes en borrador</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoB)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>
					
					<div class="alert alert-info"><h4><i class="fa fa-info"></i> Alertas tipo C</h4></div>
					<ul>
					@if (count($comprobantesDescuadrados['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesDescuadrados['C']) }} comprobantes descuadrados</h4>
						</li>
					@endif
					@if (count($comprobantesBorradorConImpuesto['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorradorConImpuesto['C']) }} comprobantes en borrador con impuestos</h4>
						</li>
					@endif
					@if (count($comprobantesBorrador['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Existen {{ count($comprobantesBorrador['C']) }} comprobantes en borrador</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoC)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::model($periodo, ['route' => ['cierreModulosContabilidadProcesar', $periodo], 'method' => 'put', 'role' => 'form']) !!}
					{!! Form::submit('Procesar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ route('cierreModulosDetalle', $periodo->id) }}" class="btn btn-outline-danger pull-right">Volver</a>
					{!! Form::close() !!}
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

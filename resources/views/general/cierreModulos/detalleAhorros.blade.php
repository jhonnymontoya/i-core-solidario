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
					<h3 class="card-title">Proceso de cierres periodo {{ $periodo->mes }} - {{ $periodo->anio }} Ahorros</h3>
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
					@if (count($ahorrosNegativos['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios con ahorros negativos: {{ count($ahorrosNegativos['A']) }}</h4>
						</li>
					@endif
					@if (count($liquidadosConAhorros['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios en estado liquidado con ahorros por más de 60 días: {{ count($liquidadosConAhorros['A']) }}</h4>
						</li>
					@endif
					@if (count($diferenciasAhorrosContabilidad['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Diferencias halladas entre módulo contable y ahorro: {{ count($diferenciasAhorrosContabilidad['A']) }}</h4>
						</li>
					@endif
					@if (count($sinAhorros['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios activos sin ahorros por más de 60 días: {{ count($sinAhorros['A']) }}</h4>
						</li>
					@endif
					@if (count($sinMovimientosEnTiempo['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios activos sin movimientos de ahorros por más de 60 días: {{ count($sinMovimientosEnTiempo['A']) }}</h4>
						</li>
					@endif
					@if (count($sinAportes['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios activos con ahorros sin aportes: {{ count($sinAportes['A']) }}</h4>
						</li>
					@endif
					@if (count($aportesLimites['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios con aportes superior al 10% del total de la entidad: {{ count($aportesLimites['A']) }}</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoA)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>

					<div class="alert alert-warning"><h4><i class="fa fa-exclamation-triangle"></i> Alertas tipo B</h4></div>
					<ul>
					@if (count($ahorrosNegativos['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios con ahorros negativos: {{ count($ahorrosNegativos['B']) }}</h4>
						</li>
					@endif
					@if (count($liquidadosConAhorros['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios en estado liquidado con ahorros entre 31 y 60 días: {{ count($liquidadosConAhorros['B']) }}</h4>
						</li>
					@endif
					@if (count($diferenciasAhorrosContabilidad['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Diferencias halladas entre módulo contable y ahorro: {{ count($diferenciasAhorrosContabilidad['B']) }}</h4>
						</li>
					@endif
					@if (count($sinAhorros['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios activos sin ahorros entre 31 y 60 días: {{ count($sinAhorros['B']) }}</h4>
						</li>
					@endif
					@if (count($sinMovimientosEnTiempo['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios activos sin movimientos de ahorros entre 31 y 60 días: {{ count($sinMovimientosEnTiempo['B']) }}</h4>
						</li>
					@endif
					@if (count($sinAportes['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios activos con ahorros sin aportes: {{ count($sinAportes['B']) }}</h4>
						</li>
					@endif
					@if (count($aportesLimites['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Socios con aportes superior al 10% del total de la entidad: {{ count($aportesLimites['B']) }}</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoB)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>

					<div class="alert alert-info"><h4><i class="fa fa-info"></i> Alertas tipo C</h4></div>
					<ul>
					@if (count($ahorrosNegativos['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Socios con ahorros negativos: {{ count($ahorrosNegativos['C']) }}</h4>
						</li>
					@endif
					@if (count($liquidadosConAhorros['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Socios en estado liquidado con ahorros hasta 30 días: {{ count($liquidadosConAhorros['C']) }}</h4>
						</li>
					@endif
					@if (count($diferenciasAhorrosContabilidad['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Diferencias halladas entre módulo contable y ahorro: {{ count($diferenciasAhorrosContabilidad['C']) }}</h4>
						</li>
					@endif
					@if (count($sinAhorros['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Socios activos sin ahorros hasta 30 días: {{ count($sinAhorros['C']) }}</h4>
						</li>
					@endif
					@if (count($sinMovimientosEnTiempo['C']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios activos sin movimientos de ahorros hasta 30 días: {{ count($sinMovimientosEnTiempo['C']) }}</h4>
						</li>
					@endif
					@if (count($sinAportes['C']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios activos con ahorros sin aportes: {{ count($sinAportes['C']) }}</h4>
						</li>
					@endif
					@if (count($aportesLimites['C']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Socios con aportes superior al 10% del total de la entidad: {{ count($aportesLimites['C']) }}</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoC)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer text-right">
					{!! Form::model($periodo, ['route' => ['cierreModulosAhorrosProcesar', $periodo], 'method' => 'put', 'role' => 'form']) !!}
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

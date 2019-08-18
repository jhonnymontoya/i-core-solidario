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
					<h3 class="card-title">Proceso de cierres periodo {{ $periodo->mes }} - {{ $periodo->anio }} Cartera</h3>
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
					@if (count($creditosSaldoNegativo['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Créditos con saldo negativo: {{ count($creditosSaldoNegativo['A']) }}</h4>
						</li>
					@endif
					@if (count($creditosSinAmortizacion['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Créditos sin amortización: {{ count($creditosSinAmortizacion['A']) }}</h4>
						</li>
					@endif
					@if (count($creditosSaldoEstadoDiferenteDesembolso['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Créditos con saldo y estado no desembolsado: {{ count($creditosSaldoEstadoDiferenteDesembolso['A']) }}</h4>
						</li>
					@endif
					@if (count($diferenciaCarteraContabilidad['A']))
						<?php $tieneAlertasTipoA = true; ?>
						<li>
							<h4>Diferencia entre módulos contable y créditos</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoA)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>

					<div class="alert alert-warning"><h4><i class="fa fa-warning"></i> Alertas tipo B</h4></div>
					<ul>
					@if (count($carteraDiasVencidos['B']))
						<?php $tieneAlertasTipoB = true; ?>
						<li>
							<h4>Créditos con más de 90 días vencidos: {{ count($carteraDiasVencidos['B']) }}</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoB)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
					<br>
					
					<div class="alert alert-info"><h4><i class="fa fa-info"></i> Alertas tipo C</h4></div>
					<ul>
					@if (count($carteraDiasVencidos['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Créditos con 90 o menos días vencidos: {{ count($carteraDiasVencidos['C']) }}</h4>
						</li>
					@endif
					@if (count($creditosSinDefinir['C']))
						<?php $tieneAlertasTipoC = true; ?>
						<li>
							<h4>Solicitudes de crédito del periodo sin definir: {{ count($creditosSinDefinir['C']) }}</h4>
						</li>
					@endif
					@if (!$tieneAlertasTipoC)
						<h4>No presenta alertas</h4>
					@endif
					</ul>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::model($periodo, ['route' => ['cierreModulosCarteraProcesar', $periodo], 'method' => 'put', 'role' => 'form', 'id' => 'frmCierreCartera']) !!}
					{!! Form::submit('Procesar', ['class' => 'btn btn-success']) !!}
					<a href="{{ route('cierreModulos.cartera.precierre', $periodo->id) }}" target="_blank" class="btn btn-primary">Precierre</a>
					<a href="{{ route('cierreModulosDetalle', $periodo->id) }}" class="btn btn-danger pull-right">Volver</a>
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
<script type="text/javascript">
	$(function(){
		var procesando = false;
		$("#frmCierreCartera").submit(function(event){
			if(!procesando) {
				procesando = true;
			}
			else{
				event.preventDefault();
				return;
			}
			var $submit = $(this).find("input[type='submit']");
			$submit.val("Procesando....");
			$submit.addClass("disabled");
			$(this).submit();
		});
	});
</script>
@endpush

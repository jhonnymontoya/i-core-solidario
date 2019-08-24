@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Ajustes créditos en lote
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Ajustes créditos en lote</li>
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
		{!! Form::model($proceso, ['route' => ['ajusteCreditoLotePutLimpiar', $proceso], 'method' => 'put', 'role' => 'form']) !!}
		<div class="container-fluid">
			<div class="card card-warning card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Limpiar carga proceso ajustes créditos en lote</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<div class="alert alert-warning">
								<h4>
									<i class="fa fa-info-circle"></i>&nbsp;Confirmar limpiar archivo cargado
								</h4>
								Se limpiará el archivo cargado para el proceso en lote. esta acción no puede ser reversada
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<dl class="dl-horizontal">
								<dt>Número proceso:</dt>
								<dd>{{ $proceso->consecutivo_proceso }}</dd>
								<dt>Fecha proceso:</dt>
								<dd>{{ $proceso->fecha_proceso }}</dd>
								<dt>Estado:</dt>
								@php
									$label = "label-";
									switch($proceso->estado) {
										case 'PRECARGA':
											$label .= 'default';
											break;
										case 'CARGADO':
											$label .= 'info';
											break;
										case 'DESEMBOLSADO':
											$label .= 'success';
											break;
										case 'ANULADO':
											$label .= 'danger';
											break;
										default:
											$label .= 'default';
											break;
									}
								@endphp
								<dd><span class="label {{ $label }}">{{ $proceso->estado }}</span></dd>
								<dt>Número de ajustes:</dt>
								<dd>{{ $proceso->cantidad_ajustes_creditos }}</dd>
								<dt>Valor ajustes:</dt>
								<dd>${{ number_format($proceso->total_valor_ajuste, 0) }}</dd>
							</dl>
						</div>
					</div>
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer">
					{!! Form::submit('Limpiar', ['class' => 'btn btn-outline-danger']) !!}
					<a href="{{ route('ajusteCreditoLoteResumen', $proceso->id) }}" class="btn btn-outline-success pull-right">Volver</a>
				</div>
			</div>
		</div>
		{!! Form::close() !!}
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Solicitudes de crédito en lote
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Solicitudes de crédito en lote</li>
		</ol>
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
		{!! Form::model($proceso, ['route' => ['procesoCreditoLotePutAnular', $proceso], 'method' => 'put', 'role' => 'form']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h3 class="box-title">Anular proceso créditos en lote</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-warning">
									<h4>
										<i class="fa fa-info-circle"></i>&nbsp;Confirmar anulación
									</h4>
									Se anulará el proceso en lote. esta acción no puede ser reversada
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
									<dt>Modalidad:</dt>
									<dd>{{ $proceso->modalidad->nombre }}</dd>
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
									<dt>Número solicitudes:</dt>
									<dd>{{ $proceso->cantidad_solicitudes_creditos }}</dd>
									<dt>Valor solicitudes:</dt>
									<dd>${{ number_format($proceso->total_valor_creditos, 0) }}</dd>
								</dl>
							</div>
						</div>
					</div>
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						{!! Form::submit('Anular', ['class' => 'btn btn-danger']) !!}
						<a href="{{ route('procesoCreditoLoteDesembolso', $proceso->id) }}" class="btn btn-success pull-right">Volver</a>
					</div>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Ajustes créditos en lote
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Ajustes créditos en lote</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		{!! Form::model($proceso, ['route' => ['ajusteCreditoLotePutContabilizar', $proceso], 'method' => 'put', 'role' => 'form', 'id' => 'contabilizarCreditosLote']) !!}
		<div class="row">
			<div class="col-md-12">
				<div class="box box-warning">
					<div class="box-header with-border">
						<h3 class="box-title">Contabilizar proceso ajuste créditos en lote</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-warning">
									<h4>
										<i class="fa fa-info-circle"></i>&nbsp;Confirmar contabilizar
									</h4>
									Se contabilizará los ajustes de créditos en lote para el presente proceso
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
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						{{--{!! Form::submit('Desembolsar', ['class' => 'btn btn-success']) !!}--}}
						<a class="btn btn-success" id="contabilizar">Contabilizar</a>
						<a href="{{ route('ajusteCreditoLoteResumen', $proceso->id) }}" class="btn btn-danger pull-right">Volver</a>
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
<script type="text/javascript">
	$(function(){
		$("#contabilizar").click(function(){
			$("#contabilizar").addClass("disabled");
			$("#contabilizar").text("Procesando...");
			$("#contabilizarCreditosLote").submit();
		});
	});
</script>
@endpush

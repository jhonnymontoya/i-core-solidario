@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Recaudos nómina
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Recaudos nómina</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				@if (Session::has('codigoComprobante'))
					<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ Session::get('codigoComprobante') }}&numeroComprobante={{ Session::get('numeroComprobante') }}" title="Imprimir comprobante" target="_blank">
						{{ Session::get('message') }}
					</a>
					<i class="fas fa-external-link-alt"></i>
				@else
					{{ Session::get('message') }}
				@endif
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Recaudos</h3>
				</div>
				<div class="card-body">
					<a class="btn btn-outline-danger" href="{{ url('recaudosNomina?pagaduria=' . $controlProceso->pagaduria->id) }}">Volver</a>
					<br>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-1"><strong>Pagaduría</strong></div>
								<div class="col-md-2">{{ $controlProceso->pagaduria->nombre }}</div>

								<div class="col-md-1"><strong>Periodo</strong></div>
								<div class="col-md-2">{{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</div>

								<div class="col-md-1"><strong>Estado</strong></div>
								<div class="col-md-1">{{ Str::title($controlProceso->estado) }}</div>

								<div class="col-md-2"><strong>Número proceso</strong></div>
								<div class="col-md-1">{{ $controlProceso->id }}</div>
							</div>							
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-6">
							<h4>Resumen</h4>
						</div>
						<div class="col-md-6 text-right">
							<a class="btn btn-outline-success" href="{{ route('recaudosNominaAplicar', $controlProceso->id) }}"><i class="far fa-check-circle"></i> Aplicar recaudos</a>
							<a class="btn btn-outline-warning">Ajustar recaudos</a>
						</div>
					</div>
					<br>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>Concepto</th>
										<th class="text-center">Generado</th>
										<th class="text-center">Aplicado</th>
										<th class="text-center">Ajustado</th>
									</tr>
								</thead>
								<tbody>
									@php
										$generado = $aplicado = $ajustado = 0;
									@endphp
									@foreach($recaudosNomina as $recaudoNomina)
										@php
											$generado += $recaudoNomina->generado;
											$aplicado += $recaudoNomina->aplicado;
											$ajustado += $recaudoNomina->ajustado;
										@endphp
										<tr>
											<td>{{ $recaudoNomina->conceptoRecaudo->codigo }} - {{ $recaudoNomina->conceptoRecaudo->nombre }}</td>
											<td class="text-right">${{ number_format($recaudoNomina->generado) }}</td>
											<td class="text-right">${{ number_format($recaudoNomina->aplicado) }}</td>
											<td class="text-right">${{ number_format($recaudoNomina->ajustado) }}</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th>Totales</th>
										<th class="text-right">${{ number_format($generado, 0) }}</th>
										<th class="text-right">${{ number_format($aplicado, 0) }}</th>
										<th class="text-right">${{ number_format($ajustado, 0) }}</th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
					<br><br>
					<div class="row">
						<div class="col-md-10 col-md-offset-1">
							<a class="btn btn-outline-info btn-sm" href="{{ route('reportesReporte', 5) }}?numeroProceso={{ $controlProceso->id }}" target="_blank"><i class="fa fa-eye"></i> Detalle generado</a>
							<a href="#" data-toggle="modal" data-target="#mAnularGeneracion" class="btn btn-outline-danger btn-sm"><i class="fa fa-exclamation-triangle"></i> Anular generación</a>
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}

<div class="modal fade" id="mAnularGeneracion" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	{!! Form::open(["route" => ["recaudosNomina.eliminarProceso", $controlProceso->id], "method" => "delete", "id" => "frmManularGeneracion"]) !!}
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title" id="mLabel">Anular Generación</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="alert alert-warning alert-dismissible">
					<h4><i class="icon fa fa-exclamation-triangle"></i> Alerta</h4>
					¿Desea anular la generación del recaudo?
				</div>
				<div class="row">
					<div class="col-md-12">
						<dl>
							<dt>Pagaduría</dt>
							<dd id="mPagaduria">{{ $controlProceso->pagaduria->nombre }}</dd>

							<dt>Periodo</dt>
							<dd id="mPeriodo">{{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</dd>

							<dt>Proceso</dt>
							<dd id="mProceso">{{ $controlProceso->id }}</dd>
						</dl>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				{!! Form::submit("Anular", ["class" => "btn btn-outline-success"]) !!}
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</div>
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(".select2").select2();
	});
</script>
@endpush

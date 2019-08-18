@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Recaudos por caja
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Recaudos por caja</li>
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
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('recaudosCaja/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $recaudos->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Recaudos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/recaudosCaja', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$recaudos->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron recaudos <a href="{{ url('recaudosCaja/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Tercero</th>
										<th>Cuenta</th>
										<th>Fecha ajuste</th>
										<th>Valor abono</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($recaudos as $recaudo)
										@php
											$data = json_decode($recaudo->recaudo);
										@endphp
										<tr>
											<td>{{ $recaudo->tercero->nombre_completo }}</td>
											<td>{{ $recaudo->cuif->codigo }} - {{ $recaudo->cuif->nombre }}</td>
											<td>{{ $recaudo->fecha_recaudo }}</td>
											<td>{{ $recaudo->contacto }}</td>
											<td>${{ number_format($data->totalRecaudo) }}</td>
											<td>
												<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ $recaudo->movimiento->tipoComprobante->codigo }}&numeroComprobante={{ $recaudo->movimiento->numero_comprobante }}" class="btn btn-default btn-xs" title="Imprimir comprobante">
													<i class="fa fa-print"></i>
												</a>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $recaudos->appends(['name'])->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="label label-{{ $recaudos->total()?'primary':'danger' }}">
						{{ $recaudos->total() }}
					</span>&nbsp;elementos.
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
		$(window).formularioCrear("{{ url('recaudosCaja/create') }}");
	});
</script>
@endpush
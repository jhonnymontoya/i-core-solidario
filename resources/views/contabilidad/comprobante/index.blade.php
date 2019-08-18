@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('comprobante/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $comprobantes->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Comprobantes</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'tipo', 'inicio', 'fin', 'estado', 'origen'), ['url' => 'comprobante', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-2 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('tipo', $tiposComprobantes, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('origen', ["MANUAL" => "Manual", "PROCESO" => "Proceso"], null, ['class' => 'form-control', 'placeholder' => 'Origen']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							<div class="input-daterange input-group" id="fecha">
								{!! Form::text('inicio', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off']); !!}
								<span class="input-group-addon">a</span>
								{!! Form::text('fin', null, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off']); !!}
							</div>
						</div>
						<div class="col-md-2 col-sm-12">
							{!! Form::select('estado', ['CONTABILIZADO' => 'CONTABILIZADO', 'SIN CONTABILIZAR' => 'SIN CONTABILIZAR', 'ANULADO' => 'ANULADO'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$comprobantes->total())
						<br>
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron comprobantes <a href="{{ url('comprobante/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<br><br>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Comprobante</th>
										<th>Descripción</th>
										<th>Fecha</th>
										<th>Origen</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($comprobantes as $comprobante)
										<tr>
											@php
												$numero = $comprobante->tipoComprobante->codigo;
												if (!empty($comprobante->numero_comprobante)) {
													$numero .= ' ' . $comprobante->numero_comprobante;
												}
											@endphp
											<td>{{ $numero }}</td>
											<td>
												@if (strlen($comprobante->descripcion) <= 40)
													<p>
														{{ str_limit($comprobante->descripcion, 40) }}
													</p>
												@else
													<p data-toggle="tooltip" data-placement="right" title="{{ $comprobante->descripcion }}">
														{{ str_limit($comprobante->descripcion, 40) }}
													</p>
												@endif
											</td>
											<td>{{ $comprobante->fecha_movimiento }}</td>
											<td>{{ $comprobante->origen }}</td>
											<td>
												<?php
													$estadoLabel = 'success';
													switch($comprobante->estado)
													{
														case 'CONTABILIZADO':
															$estadoLabel = 'success';
															break;
														case 'ANULADO':
															$estadoLabel = 'danger';
															break;
														case 'SIN CONTABILIZAR':
															$estadoLabel = 'warning';
															break;
														
														default:
															$estadoLabel = 'success';
															break;
													}
												?>
												@if($estadoLabel == 'warning')
													<a href="{{ route('comprobanteContabilizar', $comprobante->id) }}" title="Contabilizar">
														<span class="label label-{{ $estadoLabel }}">
															{{ $comprobante->estado }}
														</span>
													</a>
												@else
													<span class="label label-{{ $estadoLabel }}">
														{{ $comprobante->estado }}
													</span>
												@endif
											</td>
											<td>
												<a href="{{ ($estadoLabel == 'warning' ? route('comprobanteEdit', $comprobante->id) : '') }}" class="btn btn-default btn-xs {{ ($estadoLabel == 'warning') ? '' : 'disabled' }}" title="Editar">
													<i class="fa fa-edit"></i>
												</a>
												<a href="{{ route('reportesReporte', 1) }}?codigoComprobante={{ $comprobante->tipoComprobante->codigo }}&numeroComprobante={{ $comprobante->numero_comprobante }}" class="btn btn-default btn-xs {{ ($estadoLabel == 'warning') ? 'disabled' : '' }}" title="Imprimir comprobante">
													<i class="fa fa-print"></i>
												</a>
												<a href="{{ route('comprobante.duplicar', $comprobante->id) }}" class="btn btn-default btn-xs {{ (($estadoLabel == 'success' || $estadoLabel == 'danger') && $comprobante->origen != 'PROCESO') ? '' : 'disabled' }}" title="Duplicar">
													<i class="fa fa-copy"></i>
												</a>
												@if ($estadoLabel != 'warning')
													@if ($comprobante->origen == 'MANUAL' && is_null($comprobante->causa_anulado_id))
														<a href="{{ route('comprobante.anular', $comprobante->id) }}" class="btn btn-default btn-xs" title="Anular">
															<i class="fa fa-close"></i>
														</a>
													@else
														<a href="" class="btn btn-default btn-xs disabled" title="Anular">
															<i class="fa fa-close"></i>
														</a>
													@endif
												@else
													<a href="{{ route('comprobanteDelete', $comprobante->id) }}" class="btn btn-default btn-xs" title="Eliminar">
													<i class="fa fa-trash"></i>
												</a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
								<tfoot>
									<tr>
										<th>Comprobante</th>
										<th>Descripción</th>
										<th>Fecha</th>
										<th>Origen</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</tfoot>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $comprobantes->appends(Request::only('name', 'tipo', 'inicio', 'fin', 'estado', 'origen'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="label label-{{ $comprobantes->total()?'primary':'danger' }}">
						{{ $comprobantes->total() }}
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
	$(window).keydown(function(event) {
		if(event.altKey && event.keyCode == 78) { 
			window.location.href = "{{ url('comprobante/create') }}";
			event.preventDefault(); 
		}
	});
	$(function(){
		$('.select2').select2();
		$('#fecha').datepicker({
			format: "dd/mm/yyyy",
			weekStart: 0,
			todayBtn: "linked",
			clearBtn: true,
			language: "es",
			multidate: false,
			forceParse: false,
			calendarWeeks: true,
			autoclose: true,
			todayHighlight: true
		});
	});
</script>
@endpush
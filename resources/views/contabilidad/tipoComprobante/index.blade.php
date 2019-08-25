@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Definir comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Definir comprobantes</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('tipoComprobante/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tiposComprobantes->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Comprobantes</h3>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/tipoComprobante', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$tiposComprobantes->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tipos de comprobantes <a href="{{ url('tipoComprobante/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Comprobante de diario</th>
										<th>Tipo consecutivo</th>
										<th>Módulo</th>
										<th>Uso</th>
										<th>Movimientos</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($tiposComprobantes as $tipoComprobante)
										<tr>
											<td>{{ $tipoComprobante->codigo }}</td>
											<td>{{ $tipoComprobante->nombre }}</td>
											<td>
												<?php
													switch($tipoComprobante->comprobante_diario)
													{
														case 'INGRESO':
															echo 'Ingreso';
															break;

														case 'EGRESO':
															echo 'Egreso';
															break;

														case 'NOTACONTABLE':
															echo 'Nota contable';
															break;
														
														default:
															echo '';
															break;
													}
												?>
											</td>
											<td>
												<?php
													switch($tipoComprobante->tipo_consecutivo)
													{
														case 'A':
															echo 'Año + Consecutivo';
															break;

														case 'B':
															echo 'Año + Mes + Consecutivo';
															break;

														case 'C':
														default:
															echo 'Secuencia continua';
															break;
													}
												?>
											</td>
											<td>{{ $tipoComprobante->modulo->nombre }}</td>
											<td>{{ $tipoComprobante->uso }}</td>
											<td>{{ $tipoComprobante->movimientos->count() }}</td>
											<td>
												@if($tipoComprobante->es_uso_manual)
													<a class="btn btn-outline-info btn-sm" href="{{ route('tipoComprobanteEdit', $tipoComprobante) }}"><i class="fa fa-edit"></i></a>
												@else
													<a class="btn btn-outline-info btn-sm disabled"><i class="fa fa-edit"></i></a>
												@endif
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $tiposComprobantes->appends(Request::only('name'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $tiposComprobantes->total()?'primary':'danger' }}">
						{{ $tiposComprobantes->total() }}
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
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('tipoComprobante/create') }}");
	});
</script>
@endpush
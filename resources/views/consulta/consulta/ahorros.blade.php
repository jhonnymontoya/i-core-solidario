@extends('layouts.consulta')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
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
					<h3 class="card-title">{{ $modalidad->getNombre($socio->id) }}</h3>
					<div class="card-tools">
						<a class="btn btn-sm btn-outline-danger float-right" href="{{ url('consulta/ahorros/lista') }}">Volver</a>
					</div>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($movimientos->count())
								<table class="table table-striped table-hover">
									<thead>
										<tr>
											<th>Fecha</th>
											<th>Concepto</th>
											<th>Detalle</th>
											<th class="text-center">Valor</th>
										</tr>
									</thead>
									<tbody>
										@foreach($movimientos as $movimiento)
											<tr>
												<td>{{ $movimiento->fecha_movimiento }}</td>
												<td>{{ $movimiento->movimiento->tipoComprobante->nombre }}</td>
												<td>{{ $movimiento->movimiento->descripcion }}</td>
												<?php
													$valor = $movimiento->valor_movimiento;
													$colorTexto = $valor < 0 ? 'text-danger' : '';
												?>
												<td class="text-right {{ $colorTexto }}">${{ number_format($valor, 0) }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							@else
								<strong>No existen registros para mostrar</strong>
							@endif
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<small>Se muestran los movimientos dentro de los últimos treinta y seis meses</small>
						</div>
					</div>
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

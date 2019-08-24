@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Producto
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Producto</li>
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
				<a href="{{ url('tarjetaProducto/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $productos->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Productos</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => 'tarjetaProducto', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-6 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br>
					@if(!$productos->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron productos <a href="{{ url('tarjetaProducto/create') }}" class="btn btn-outline-primary btn-sm">crear uno nuevo</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Tipo producto</th>
										<th class="text-center">Valor cuota manejo</th>
										<th class="text-center">Retiros red</th>
										<th class="text-center">Retiros otras redes</th>
										<th>Estado</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($productos as $producto)
										@php
											$modalidad = '';
											switch ($producto->modalidad) {
												case 'CUENTAAHORROS':
													$modalidad = 'Cuenta de ahorros';
													break;
												case 'CREDITO':
													$modalidad = 'Crédito';
													break;
												case 'MIXTO':
													$modalidad = 'Ahorro y Crédito';
													break;
												default:
													$modalidad = '';
													break;
											}
										@endphp
										<tr>
											<td><a href="{{ route('tarjetaProductoEdit', $producto) }}">{{ $producto->codigo }}</a></td>
											<td><a href="{{ route('tarjetaProductoEdit', $producto) }}">{{ $producto->nombre }}</a></td>
											<td>{{ $modalidad }}</td>
											<td class="text-right">${{ number_format($producto->valor_cuota_manejo_mes, 0) }}</td>
											<td class="text-right">{{ number_format($producto->numero_retiros_sin_cobro_red, 0) }}</td>
											<td class="text-right">{{ number_format($producto->numero_retiros_sin_cobro_otra_red, 0) }}</td>
											<td><span class="label label-{{ $producto->esta_activo ? 'primary' : 'danger' }}">{{ $producto->esta_activo ? 'Activo' : 'Inactivo' }}</span></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $productos->appends(Request::only('name'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="label label-{{ $productos->total()?'primary':'danger' }}">
						{{ $productos->total() }}
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
		$(window).formularioCrear("{{ url('tarjetaProducto/create') }}");
	});
</script>
@endpush
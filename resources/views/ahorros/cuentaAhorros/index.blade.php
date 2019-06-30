@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cuenta de ahorros
			<small>Ahorros</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Ahorros</a></li>
			<li class="active">Cuenta de ahorros</li>
		</ol>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				{{ Session::get('message') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-1">
				<a href="{{ url('cuentaAhorros/create') }}" class="btn btn-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $cuentasAhorros->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Cuentas de ahorros</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name', 'tipoCuenta', 'estado'), ['url' => 'cuentaAhorros', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-4 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('tipoCuenta', $tiposCuentaAhorros, null, ['class' => 'form-control select2', 'placeholder' => 'Tipo de cuenta']); !!}
					</div>
					<div class="col-md-3 col-sm-12">
						{!! Form::select('estado', ['ACTIVA' => 'Activa', 'APERTURA' => 'Apertura', 'INACTIVA' => 'Inactiva', 'CERRADA' => 'Cerrada'], null, ['class' => 'form-control select2', 'placeholder' => 'Estado']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$cuentasAhorros->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron cuentas de ahorros <a href="{{ url('cuentaAhorros/create') }}" class="btn btn-primary btn-xs">crear uno nueva</a>
							</div>
						</div>
					</p>
				@else
					<br>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Titular</th>
									<th>Tipo cuenta</th>
									<th>Cuenta</th>
									<th class="text-center">Fecha apertura</th>
									<th class="text-center">Saldo flexible</th>
									<th>Estado</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($cuentasAhorros as $cuentaAhorros)
									<tr>
										@php
											$tercero = $cuentaAhorros->socioTitular->tercero->numero_identificacion . ' - ' . $cuentaAhorros->socioTitular->tercero->nombre_corto;
										@endphp
										<td>{{ $tercero }}</td>
										<td>{{ $cuentaAhorros->tipoCuentaAhorro->nombre_producto }}</td>
										<td>
											<a href="{{ route('cuentaAhorros.edit', $cuentaAhorros->id) }}" title="Editar">
												{{ $cuentaAhorros->numero_cuenta }}
											</a>
										</td>
										<td class="text-right">{{ $cuentaAhorros->fecha_apertura }}</td>
										<td class="text-right">${{ number_format($cuentaAhorros->cupo_flexible) }}</td>
										<td>
											<span class="label label-default">
												{{ $cuentaAhorros->estado }}
											</span>
										</td>
										<td>
											<a href="{{ route('cuentaAhorros.edit', $cuentaAhorros->id) }}" class="btn btn-info btn-xs" title="Editar">
												<i class="fa fa-edit"></i>
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
						{!! $cuentasAhorros->appends(Request::only('name', 'tipoCuenta', 'estado'))->render() !!}
					</div>
				</div>			
			</div>
			<div class="box-footer">
				<span class="label label-{{ $cuentasAhorros->total()?'primary':'danger' }}">{{ $cuentasAhorros->total() }}</span> elementos.
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
		$(window).formularioCrear("{{ url('cuentaAhorros/create') }}");
		$('.select2').select2();
	});
</script>
@endpush

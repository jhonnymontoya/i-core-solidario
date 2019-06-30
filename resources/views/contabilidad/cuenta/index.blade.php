@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Cuentas
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Cuentas</li>
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
			<div class="col-md-2">
				<a href="{{ url('cuentaContable/create') }}" class="btn btn-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $cuentas->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Cuentas</h3>
			</div>
			<div class="box-body">
				{!! Form::model(Request::only('name', 'tipo', 'nivel', 'modulo','estado'), ['url' => 'cuentaContable', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
				<div class="row">
					<div class="col-md-3 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<?php
							$tnivel = array('' => 'Nivel',
								1 => '1', 2 => '2', 3 => '3', 4 => '4', 5 => '5', 6 => '6', 7 => '7', 8 => '8', 9 => '9', 10 => '10'
							);
						?>
						{!! Form::select('nivel', $tnivel, null, ['class' => 'form-control']) !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<?php
							$ttipo = array(
								'' => 'Tipo', 'CLASE' => 'CLASE', 'GRUPO' => 'GRUPO',
								'CUENTA' => 'CUENTA', 'SUBCUENTA' => 'SUBCUENTA',
								'AUXILIAR' => 'AUXILIAR'
							);
						?>
						{!! Form::select('tipo', $ttipo, null, ['class' => 'form-control']) !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('modulo', $modulos, null, ['class' => 'form-control', 'placeholder' => 'Módulo']) !!}
					</div>
					<div class="col-md-2 col-sm-12">
						{!! Form::select('estado', ['1' => 'Activo', '0' => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>
						
					</div>
				</div>
				{!! Form::close() !!}
				@if(!$cuentas->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron cuentas contables <a href="{{ url('cuentaContable/create') }}" class="btn btn-primary btn-xs">crear una nueva</a>
							</div>
						</div>
					</p>
				@else
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Código</th>
									<th>Cuenta</th>
									<th>Naturaleza</th>
									<th>Estado</th>
									<th>Nivel</th>
									<th>Tipo</th>
									<th>Módulo</td>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($cuentas as $cuenta)
									<tr>
										<td>{{ $cuenta->codigo }}</td>
										<td>{{ str_limit($cuenta->nombre, 40) }}</td>
										<td>{{ $cuenta->naturaleza }}</td>
										<td>
											<span class="label label-{{ $cuenta->esta_activo?'success':'danger' }}">
												{{ $cuenta->esta_activo?'activa':'inactiva' }}
											</span>
										</td>
										<td>{{ $cuenta->nivel }}</td>
										<td>{{ $cuenta->tipo_cuenta }}</td>
										<td>{{ !empty($cuenta->modulo) ? $cuenta->modulo->nombre : ''}}</td>
										<td><a class="btn btn-info btn-xs" href="{{ route('cuentaEdit', $cuenta->id) }}"><i class="fa fa-edit"></i></a></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $cuentas->appends(Request::only('name', 'tipo', 'nivel', 'modulo','estado'))->render() !!}
					</div>
				</div>
			</div>
			<div class="box-footer">
				<span class="label label-{{ $cuentas->total()?'primary':'danger' }}">
					{{ $cuentas->total() }}
				</span>&nbsp;elementos.
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
		$(window).formularioCrear("{{ url('cuentaContable/create') }}");
	});
</script>
@endpush
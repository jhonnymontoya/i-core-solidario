@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Tarjetahabiente
			<small>Tarjeta</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Tarjeta</a></li>
			<li class="active">Tarjetahabiente</li>
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
				<a href="{{ url('tarjetaHabiente/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $terceros->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Tarjetahabientes</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name'), ['url' => 'tarjetaHabiente', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-6 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$terceros->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron tarjetahabientes <a href="{{ url('tarjetaHabiente/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
							</div>
						</div>
					</p>
				@else
					<br>
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $terceros->appends(Request::only('name'))->render() !!}
						</div>
					</div>
					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>Identificación</th>
									<th>Nombre</th>
									<th>Pagaduría</th>
									<th>Tarjetas</th>
									<th class="text-center">Cupo total</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($terceros as $tercero)
									@php
										$pagaduria = optional($tercero->socio)->pagaduria;
										$pagaduria = optional($pagaduria)->nombre;
										$tarjetas = $tercero->tarjetahabientes;
										$cantidadTarjetas = $tarjetas->count();
										$cupo = 0;
										foreach ($tarjetas as $tarjeta) {
											$cupo += $tarjeta->estado != 'CANCELADA' ? $tarjeta->cupo : 0;
										}
									@endphp
									<tr>
										<td>
											<a href="{{ route('tarjetaHabiente.show', $tercero->id) }}">
												{{ $tercero->tipoIdentificacion->codigo }} - {{ $tercero->numero_identificacion }}
											</a>
										</td>
										<td>
											<a href="{{ route('tarjetaHabiente.show', $tercero->id) }}">
												{{ $tercero->nombre_corto }}
											</a>
										</td>
										<td>{{ $pagaduria }}</td>
										<td>{{ $cantidadTarjetas }}</td>
										<td class="text-right">${{ number_format($cupo) }}</td>
										<td><a href="{{ route('tarjetaHabiente.show', $tercero->id) }}" class="btn btn-info btn-xs"><i class="fa fa-edit"></i></a></td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $terceros->appends(Request::only('name'))->render() !!}
					</div>
				</div>
			</div>
			<div class="box-footer">
				<span class="label label-{{ $terceros->total()?'primary':'danger' }}">
					{{ $terceros->total() }}
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
		$(window).formularioCrear("{{ url('tarjetaHabiente/create') }}");
	});
</script>
@endpush
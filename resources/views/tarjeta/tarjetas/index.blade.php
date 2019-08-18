@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tarjetas
						<small>Tarjeta</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Tarjeta</a></li>
						<li class="breadcrumb-item active">Tarjetas</li>
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
			<div class="col-md-1">
				<a href="{{ url('tarjetas/create') }}" class="btn btn-primary">Crear nuevas</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tarjetas->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Tarjetas</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'tipoCuenta', 'estado'), ['url' => 'tarjetas', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-6 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-5 col-sm-12">
							{!! Form::select('estado', ['DISPONIBLE' => 'Disponible', 'ASIGNADA' => 'Asignada'], null, ['class' => 'form-control select2', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$tarjetas->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tarjetas <a href="{{ url('tarjetas/create') }}" class="btn btn-primary btn-xs">crear nuevas</a>
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-striped table-hover">
								<thead>
									<tr>
										<th>Número tarjeta</th>
										<th>Vencimiento</th>
										<th>Estado</th>
										<th>Propietario</th>
									</tr>
								</thead>
								<tbody>
									@foreach ($tarjetas as $tarjeta)
										<tr>
											@php
												$tercero = optional($tarjeta->tarjetahabientes)->first();
												if($tercero) {
													$tercero = $tercero->tercero->tipoIdentificacion->codigo . ' ' . $tercero->tercero->numero_identificacion . ' - ' . $tercero->tercero->nombre;
												}
											@endphp
											<td>{{ $tarjeta->numeroFormateado }}</td>
											<td>{{ $tarjeta->vencimiento }}</td>
											<td>
												<span class="label label-{{ is_null($tercero) ? 'success' : 'primary' }}">
													{{ is_null($tercero) ? 'Disponible' : 'Asignada' }}
												</span>
											</td>
											<td width="35%">
												{{ $tercero ?? "N/A" }}
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $tarjetas->appends(Request::only('name', 'tipoCuenta', 'estado'))->render() !!}
						</div>
					</div>			
				</div>
				<div class="card-footer">
					<span class="label label-{{ $tarjetas->total()?'primary':'danger' }}">{{ $tarjetas->total() }}</span> elementos.
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
		$(window).formularioCrear("{{ url('tarjetas/create') }}");
		$('.select2').select2();
	});
</script>
@endpush

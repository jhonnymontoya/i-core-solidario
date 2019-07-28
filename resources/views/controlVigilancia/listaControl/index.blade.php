@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Archivos SES
			<small>Listas de control</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Control y Vigilancia</a></li>
			<li class="active">Listas de control</li>
		</ol>
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
		<div class="box box-{{ false?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Listas de control</h3>
			</div>
			<div class="box-body">
				<p>
					Las listas de control de vigilancia,
					son listas emitidas por entidades de control y vigilancia
					con jurisdicción internacional para la prevención de lavado
					de activos y financiación del terrorismo.
				</p>
				<br>
				@if($listas->total())
					<br><br>
					<div class="table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Fecha publicación</th>
									<th class="text-center">Entradas</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($listas as $lista)
									<tr>
										<td>{{ $lista->nombre }}</td>
										<td>{{ $lista->fecha_publicacion }}</td>
										<td class="text-right">{{ $lista->cantidad_detalles }}</td>
										<td>
											<a href="{{ route('listaControl.edit', $lista->id) }}" class="btn btn-info btn-xs" title="Editar">
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
						{!! $listas->appends(Request::only('search'))->render() !!}
					</div>
				</div>
			</div>
			<div class="box-footer">
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
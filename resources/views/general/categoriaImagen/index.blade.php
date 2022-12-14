@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Categorías de imágenes
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Categorías de imágenes</li>
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
				<a href="{{ url('categoriaImagen/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $categoriasImagen->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Categorías de imágenes</h3>

					<div class="card-tools pull-right">
						<button type="button" class="btn btn-card-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
							<i class="fa fa-minus"></i>
						</button>
					</div>
				</div>
				<div class="card-body">
					@if(!$categoriasImagen->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron categorías de imágenes <a href="{{ url('categoriaImagen/create') }}" class="btn btn-outline-primary btn-sm">crear uno nueva</a>
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Nombre</th>
										<th>Ancho</th>
										<th>Alto</th>
										<th>Descripción</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($categoriasImagen as $categoriaImagen)
										<tr>
											<td>{{ $categoriaImagen->nombre }}</td>
											<td>{{ $categoriaImagen->ancho }}</td>
											<td>{{ $categoriaImagen->alto }}</td>
											<td>{{ Str::limit($categoriaImagen->descripcion, 20) }}</td>
											<td>
												<a class="btn btn-outline-info btn-sm" href="{{ route('categoriaImagenEdit', $categoriaImagen) }}"><i class="fa fa-edit"></i></a></td>
											</td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $categoriasImagen->appends([])->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $categoriasImagen->total()?'primary':'danger' }}">
						{{ $categoriasImagen->total() }}
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
		$(window).formularioCrear("{{ url('categoriaImagen/create') }}");
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Menús
			<small>Sistema</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Sistema</a></li>
			<li class="active">Menús</li>
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
				<a href="{{ url('menu/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $menus->count()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Menús</h3>

				<div class="box-tools pull-right">
					<button type="button" class="btn btn-box-tool" data-widget="collapse" data-toggle="tooltip" title="Collapse">
						<i class="fa fa-minus"></i>
					</button>
				</div>
			</div>
			<div class="box-body">
				@if(!$menus->count())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron menus <a href="{{ url('menu/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
							</div>
						</div>
					</p>
				@else
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Ruta</th>
									<th>Icono</th>
									<th>Perfiles</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($menus as $menu)
									<tr>
										<td>{{ $menu->nombre }}</td>
										<td><a href="{{ url($menu->ruta) }}">/{{ $menu->ruta }}</a></td>
										<td><i class="fa fa-{{ $menu->pre_icon }}"></i></td>
										<td>
											<span class="badge bg-{{ $menu->perfiles->count()?'green':'red' }}">{{ $menu->perfiles->count() }}</span>
										</td>
										<td><a class="btn btn-info btn-xs" href="{{ route('menuEdit', $menu) }}"><i class="fa fa-edit"></i></a></td>
									</tr>

									@foreach ($menu->hijos as $menuHijo)
										<tr>
											<td>{{ $menuHijo->padre->nombre }} / {{ $menuHijo->nombre }}</td>
											<td><a href="{{ url($menuHijo->ruta) }}">/{{ $menuHijo->ruta }}</a></td>
											<td><i class="fa fa-{{ $menuHijo->pre_icon }}"></i></td>
											<td>
												<span class="badge bg-{{ $menu->perfiles->count()?'green':'red' }}">{{ $menu->perfiles->count() }}</span>
											</td>
											<td><a class="btn btn-info btn-xs" href="{{ route('menuEdit', $menuHijo) }}"><i class="fa fa-edit"></i></a></td>
										</tr>

										@foreach ($menuHijo->hijos as $menuSubHijo)
											<tr>
												<td>{{ $menuSubHijo->padre->padre->nombre }} / {{ $menuSubHijo->padre->nombre }} / {{ $menuSubHijo->nombre }}</td>
												<td><a href="{{ url($menuSubHijo->ruta) }}">/{{ $menuSubHijo->ruta }}</a></td>
												<td><i class="fa fa-{{ $menuSubHijo->pre_icon }}"></i></td>
												<td>
													<span class="badge bg-{{ $menu->perfiles->count()?'green':'red' }}">{{ $menu->perfiles->count() }}</span>
												</td>
												<td><a class="btn btn-info btn-xs" href="{{ route('menuEdit', $menuSubHijo) }}"><i class="fa fa-edit"></i></a></td>
											</tr>
										@endforeach

									@endforeach

								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th>Nombre</th>
									<th>Ruta</th>
									<th>Icono</th>
									<th>Perfiles</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{{-- $menus->appends([])->render() --}}
					</div>
				</div>
			</div>
			<div class="box-footer">
				{{--
				<span class="label label-{{ $menus->count()?'primary':'danger' }}">
					{{ $menus->count() }}
				</span>&nbsp;elementos.
				--}}
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
		$(window).formularioCrear("{{ url('menu/create') }}");
	});
</script>
@endpush

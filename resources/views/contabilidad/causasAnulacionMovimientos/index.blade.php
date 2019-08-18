@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Causas de Anulación para Movimientos
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Causas anulación Movimientos</li>
		</ol>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-1">
				<a href="{{ url('causaAnulacionMovimiento/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-{{ $causasAnulacion->total()?'primary':'danger' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Causas de anulación para movimientos</h3>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only('name', 'estado'), ['url' => 'causaAnulacionMovimiento', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
					</div>
					<div class="col-md-5 col-sm-12">
						{!! Form::select('estado', ['1' => 'Activo', '0' => 'Inactivo'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if(!$causasAnulacion->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron causas para anulación de movimientos <a href="{{ url('causaAnulacionMovimiento/create') }}" class="btn btn-primary btn-xs">crear una nuevo</a>
							</div>
						</div>
					</p>
				@else
					<br>
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nombre</th>
									<th>Estado</th>
									<th>Movimientos</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($causasAnulacion as $causa)
									<tr>
										<td>{{ $causa->nombre }}</td>
										<td>
											<span class="label label-{{ $causa->esta_activa ? 'success' : 'danger' }}">
												{{ $causa->esta_activa ? 'ACTIVA' : 'INACTIVA' }}
											</span>
										</td>
										<td>{{ $causa->movimientos->count() }}</td>
										<td>
											<a href="{{ route('causaAnulacionMovimientoEdit', $causa->id) }}" class="btn btn-info btn-xs" title="Editar">
												<i class="fa fa-edit"></i>
											</a>
										</td>
									</tr>
								@endforeach
							</tbody>
							<tfoot>
								<tr>
									<th>Nombre</th>
									<th>Estado</th>
									<th>Movimientos</th>
									<th></th>
								</tr>
							</tfoot>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $causasAnulacion->appends(Request::only('name', 'estado'))->render() !!}
					</div>
				</div>			
			</div>
			<div class="card-footer">
				<span class="label label-{{ $causasAnulacion->total()?'primary':'danger' }}">{{ $causasAnulacion->total() }}</span> elementos.
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
	$(window).keydown(function(event) {
		if(event.altKey && event.keyCode == 78) { 
			window.location.href = "{{ url('causaAnulacionMovimiento/create') }}";
			event.preventDefault(); 
		}
	});
</script>
@endpush

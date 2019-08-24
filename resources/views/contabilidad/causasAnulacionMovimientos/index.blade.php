@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Causa de anulación para movimientos
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Causa de anulación para movimientos</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		<div class="row">
			<div class="col-md-1">
				<a href="{{ url('causaAnulacionMovimiento/create') }}" class="btn btn-outline-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $causasAnulacion->total()?'primary':'danger' }} card-outline">
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
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$causasAnulacion->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron causas para anulación de movimientos <a href="{{ url('causaAnulacionMovimiento/create') }}" class="btn btn-outline-primary btn-sm">crear una nuevo</a>
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
												<a href="{{ route('causaAnulacionMovimientoEdit', $causa->id) }}" class="btn btn-outline-info btn-sm" title="Editar">
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

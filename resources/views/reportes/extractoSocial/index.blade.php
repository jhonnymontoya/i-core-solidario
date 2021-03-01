@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Extracto Social
						<small>Reportes</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Reportes</a></li>
						<li class="breadcrumb-item active">Extracto Social</li>
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
			<div class="col-md-12">
				<a href="{{ url('extractoSocial/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $configuraciones->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Configuraciones de extractos sociales</h3>
				</div>
				<div class="card-body">
					@if(!$configuraciones->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron configuraciones de extractos sociales <a href="{{ url('extractoSocial/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
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
										<th>Cuenta</th>
										<th class="text-center">Saldo mínimo.</th>
										<th class="text-center">Días para inactivación</th>
										<th class="text-center">Cuentas de ahorros</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($configuraciones as $tipoCuentaAhorros)
										<tr>
											<td>
												<a href="{{ route('tipoCuentaAhorros.edit', $tipoCuentaAhorros->id) }}" title="Editar">
													{{ $tipoCuentaAhorros->nombre_producto }}
												</a>
											</td>
											<td>{{ Str::limit($tipoCuentaAhorros->capitalCuif->full, 50) }}</td>
											<td class="text-right">${{ number_format($tipoCuentaAhorros->saldo_minimo, 0) }}</td>
											<td class="text-right">{{ number_format($tipoCuentaAhorros->dias_para_inactivacion, 0) }}</td>
											<td class="text-right">
												<a href="{{ url('cuentaAhorros') . '?tipoCuenta=' . $tipoCuentaAhorros->id }}">
													{{ number_format($tipoCuentaAhorros->cuentasAhorros->count(), 0) }}
												</a>
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $tipoCuentaAhorros->esta_activa ? 'success' : 'danger' }}">
													{{ $tipoCuentaAhorros->esta_activa ? 'ACTIVA' : 'INACTIVA' }}
												</span>
											</td>
											<td>
												<a href="{{ route('tipoCuentaAhorros.edit', $tipoCuentaAhorros->id) }}" class="btn btn-outline-info btn-sm" title="Editar">
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
							{!! $configuraciones->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $configuraciones->total()?'primary':'danger' }}">{{ $configuraciones->total() }}</span> elementos.
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
		$(window).formularioCrear("{{ url('extractoSocial/create') }}");
	});
</script>
@endpush

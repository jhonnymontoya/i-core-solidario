@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Tipo cuenta de ahorros
						<small>Ahorros</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Ahorros</a></li>
						<li class="breadcrumb-item active">Tipo cuenta de ahorros</li>
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
				<a href="{{ url('tipoCuentaAhorros/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $tiposCuentasAhorros->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Tipo cuentas de ahorros</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name', 'estado'), ['url' => 'tipoCuentaAhorros', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-3 col-sm-12">
							{!! Form::select('estado', [true => 'Activa', false => 'Inactiva'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>
						</div>
						{!! Form::close() !!}
					</div>
					@if(!$tiposCuentasAhorros->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron tipos de cuentas de ahorros <a href="{{ url('tipoCuentaAhorros/create') }}" class="btn btn-primary btn-xs">crear uno nuevo</a>
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
									@foreach ($tiposCuentasAhorros as $tipoCuentaAhorros)
										<tr>
											<td>
												<a href="{{ route('tipoCuentaAhorros.edit', $tipoCuentaAhorros->id) }}" title="Editar">
													{{ $tipoCuentaAhorros->nombre_producto }}
												</a>
											</td>
											<td>{{ str_limit($tipoCuentaAhorros->capitalCuif->full, 50) }}</td>
											<td class="text-right">${{ number_format($tipoCuentaAhorros->saldo_minimo, 0) }}</td>
											<td class="text-right">{{ number_format($tipoCuentaAhorros->dias_para_inactivacion, 0) }}</td>
											<td class="text-right">
												<a href="{{ url('cuentaAhorros') . '?tipoCuenta=' . $tipoCuentaAhorros->id }}">
													{{ number_format($tipoCuentaAhorros->cuentasAhorros->count(), 0) }}
												</a>
											</td>
											<td>
												<span class="label label-{{ $tipoCuentaAhorros->esta_activa ? 'success' : 'danger' }}">
													{{ $tipoCuentaAhorros->esta_activa ? 'ACTIVA' : 'INACTIVA' }}
												</span>
											</td>
											<td>
												<a href="{{ route('tipoCuentaAhorros.edit', $tipoCuentaAhorros->id) }}" class="btn btn-info btn-xs" title="Editar">
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
							{!! $tiposCuentasAhorros->appends(Request::only('name', 'estado'))->render() !!}
						</div>
					</div>			
				</div>
				<div class="card-footer">
					<span class="label label-{{ $tiposCuentasAhorros->total()?'primary':'danger' }}">{{ $tiposCuentasAhorros->total() }}</span> elementos.
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
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('tipoCuentaAhorros/create') }}");
		$('.select2').select2();
	});
</script>
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cuotas obligatorias
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Cuotas obligatorias</li>
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
				<a href="{{ url('tipoCuotaObligatoria/create') }}" class="btn btn-outline-primary">Crear nueva</a>
			</div>
		</div>
		<br>
		<div class="container-fluid">
			<div class="card card-{{ $cuotas->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Cuotas obligatorias</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('name', 'estado'), ['url' => 'tipoCuotaObligatoria', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row">
						<div class="col-md-6 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off', 'autofocus']); !!}
						</div>
						<div class="col-md-5 col-sm-12">
							{!! Form::select('estado', [true => 'Activa', false => 'Inactiva'], null, ['class' => 'form-control', 'placeholder' => 'Estado']); !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
					@if(!$cuotas->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron cuotas obligatorias <a href="{{ url('tipoCuotaObligatoria/create') }}" class="btn btn-outline-primary btn-sm">crear una nueva</a>
								</div>
							</div>
						</p>
					@else
						<br>
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Cuenta</th>
										<th class="text-center">Reintegrable</th>
										<th>Tipo cálculo</th>
										<th>Valor</th>
										<th>Estado</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($cuotas as $cuota)
										<tr>
											<td>{{ $cuota->codigo }}</td>
											<td>{{ $cuota->nombre }}</td>
											<td>{{ $cuota->cuenta->full }}</td>
											<td class="text-center">
												<span class="badge badge-pill badge-{{ $cuota->es_reintegrable ? 'success' : 'danger' }}">
													{{ $cuota->es_reintegrable ? 'Sí' : 'No' }}
												</span>
											</td>
											<td>
												<?php
													$valor = '';
													switch ($cuota->tipo_calculo){
														case 'PORCENTAJESUELDO':
															$valor = '% del Sueldo';
															break;
														case 'PORCENTAJESMMLV':
															$valor = '% del SMMLV';
															break;
														case 'VALORFIJO':
															$valor = 'Valor fijo';
															break;
													}
												?>
												{{ $valor }}
											</td>
											<td class="text-right">
												<?php
													$valor = '';
													switch ($cuota->tipo_calculo){
														case 'PORCENTAJESUELDO':
															$valor = number_format($cuota->valor, 2) . '%';
															break;
														case 'PORCENTAJESMMLV':
															$valor = number_format($cuota->valor, 2) . '%';
															break;
														case 'VALORFIJO':
															$valor = '$' . number_format($cuota->valor, 2);
															break;
													}
												?>
												{{ $valor }}
											</td>
											<td>
												<span class="badge badge-pill badge-{{ $cuota->esta_activa ? 'success' : 'danger' }}">
													{{ $cuota->esta_activa ? 'ACTIVA' : 'INACTIVA' }}
												</span>
											</td>
											<td>
												<a href="{{ route('tipoCuotaObligatoriaEdit', $cuota->id) }}" class="btn btn-outline-info btn-sm" title="Editar">
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
							{!! $cuotas->appends(Request::only('name', 'estado'))->render() !!}
						</div>
					</div>			
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $cuotas->total()?'primary':'danger' }}">{{ $cuotas->total() }}</span> elementos.
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
			window.location.href = "{{ url('tipoCuotaObligatoria/create') }}";
			event.preventDefault(); 
		}
	});
</script>
@endpush

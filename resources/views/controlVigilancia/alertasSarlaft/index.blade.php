@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Alertas
						<small>SARLAFT</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> SARLAFT</a></li>
						<li class="breadcrumb-item active">Aleras</li>
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
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Alertas</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('nombre'), ['url' => 'alertasSarlaft', 'method' => 'GET', 'role' => 'search']) !!}
					<label class="col-sm-6 control-label">
						Nombre
					</label>
					<div class="row">
						<div class="col-md-11">
							{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre de la alerta']) !!}
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>
						</div>
					</div>
					{!! Form::close() !!}
					<br>
					@if($alertas->count())
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table table-striped table-hover">
									<thead>
										<th>Nombre</th>
										<th>Diario</th>
										<th>Semanal</th>
										<th>Mensual</th>
										<th>Anual</th>
										<th></th>
									</thead>
									<tbody>
										@foreach($alertas as $alerta)
											<tr>
												<td>{{ $alerta->nombre }}</td>
												<td><span class="badge badge-pill badge-{{ $alerta->diario ? 'success' : 'danger' }}">{{ $alerta->diario ? 'Sí' : 'No' }}</span></td>
												<td><span class="badge badge-pill badge-{{ $alerta->semanal ? 'success' : 'danger' }}">{{ $alerta->semanal ? 'Sí' : 'No' }}</span></td>
												<td><span class="badge badge-pill badge-{{ $alerta->mensual ? 'success' : 'danger' }}">{{ $alerta->mensual ? 'Sí' : 'No' }}</span></td>
												<td><span class="badge badge-pill badge-{{ $alerta->anual ? 'success' : 'danger' }}">{{ $alerta->anual ? 'Sí' : 'No' }}</span></td>
												<td><a href="{{ route('alertasSarlaft.edit', $alerta->id) }}" class="btn btn-outline-info btn-sm"><i class="fa fa-edit"></i></a></td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					@else
						<br><br>
						<h4>No se encontraón alertas para SARLAFT</h4>
					@endif
				</div>
				<div class="card-footer">
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
@endpush

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Cupos de crédito
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Cupos de crédito</li>
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
			<div class="card card-{{ $modalidades->total()?'primary':'danger' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Cupos de crédito</h3>
				</div>
				<div class="card-body">
					<div class="row">
						{!! Form::model(Request::only('name'), ['url' => '/cupoCredito', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
						<div class="col-md-5 col-sm-12">
							{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
						</div>
						<div class="col-md-2 col-sm-12">
							<button type="submit" class="btn btn-block btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
						{!! Form::close() !!}
					</div>
					<br><br>
					<div class="row">
						<div class="col-md-12">
							<label>¿Incluye salario como cupo de crédito?</label>&nbsp;&nbsp;
							<span class="badge badge-pill badge-{{ $parametro->indicador ? 'success' : 'default' }}">{{ $parametro->indicador ? 'Sí' : 'No' }}</span>
							@if($parametro->indicador == true)
								<label>Número de veces </label>
								<span class="badge badge-pill badge-success">{{ number_format($parametro->valor, 1) }}</span>&nbsp;&nbsp;
							@endif
							<a href="{{ route('parametroInstitucionalEdit', $parametro->id) }}" class="btn btn-outline-primary btn-sm">Editar</a>
						</div>
					</div>
					<br>
					@if(!$modalidades->total())
						<p>
							<div class="row">
								<div class="col-md-12">
									No se encontraron modalidades de créditos
								</div>
							</div>
						</p>
					@else
						<div class="table-responsive">
							<table class="table table-hover">
								<thead>
									<tr>
										<th>Código</th>
										<th>Nombre</th>
										<th>Veces apalancamiento</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									@foreach ($modalidades as $modalidad)
										<tr>
											<td>{{ $modalidad->codigo }}</td>
											<td>{{ $modalidad->nombre }}</td>
											<td>{{ $modalidad->apalancamiento_cupo }}</td>
											<td><a class="btn btn-outline-info btn-sm" href="{{ route('cupoCreditoEdit', $modalidad) }}"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					@endif
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $modalidades->appends(Request::only('name'))->render() !!}
						</div>
					</div>
				</div>
				<div class="card-footer">
					<span class="badge badge-pill badge-{{ $modalidades->total()?'primary':'danger' }}">
						{{ $modalidades->total() }}
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
		$("input[name='name']").enfocar();
		$(window).formularioCrear("{{ url('modalidadCredito/create') }}");
	});
</script>
@endpush
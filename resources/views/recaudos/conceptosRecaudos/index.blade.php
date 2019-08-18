@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Conceptos de recaudo
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Conceptos de recaudo</li>
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
				<a href="{{ url('conceptosRecaudos/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="card card-{{ $conceptosRecaudos->total()?'primary':'danger' }}">
			<div class="card-header with-border">
				<h3 class="card-title">Conceptos de recaudo</h3>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only(['name', 'pagaduria']), ['url' => '/conceptosRecaudos', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-5 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-5 col-sm-12">
						{!! Form::select('pagaduria', $pagadurias, null, ['class' => 'form-control select2', 'placeholder' => 'Pagaduría', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-2 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				<br>
				@if(!$conceptosRecaudos->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron conceptos de recaudos <a href="{{ url('conceptosRecaudos/create') }}" class="btn btn-primary btn-xs">crear una nuevo</a>
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
									<th>Pagaduría</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($conceptosRecaudos as $concepto)
									<tr>
										<td>{{ $concepto->codigo }}</td>
										<td>{{ $concepto->nombre }}</td>
										<td>{{ $concepto->pagaduria->nombre }}</td>
										<td>
											<a class="btn btn-info btn-xs" href="{{ route('conceptosRecaudosEdit', $concepto) }}"><i class="fa fa-edit"></i></a>
											<a class="btn btn-danger btn-xs disabled"><i class="fa fa-trash"></i></a>
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $conceptosRecaudos->appends(Request::only(['name', 'pagaduria']))->render() !!}
					</div>
				</div>
			</div>
			<div class="card-footer">
				<span class="label label-{{ $conceptosRecaudos->total()?'primary':'danger' }}">
					{{ $conceptosRecaudos->total() }}
				</span>&nbsp;elementos.
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
		$(window).formularioCrear("{{ url('conceptosRecaudos/create') }}");
	});
</script>
@endpush
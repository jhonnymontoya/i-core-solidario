@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Solicitudes de crédito en lote
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Solicitudes de crédito en lote</li>
		</ol>
	</section>

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		<div class="row">
			<div class="col-md-2">
				<a href="{{ url('procesoCreditoLote/create') }}" class="btn btn-primary">Crear nuevo</a>
			</div>
		</div>
		<br>
		<div class="box box-{{ $procesos->total()?'primary':'danger' }}">
			<div class="box-header with-border">
				<h3 class="box-title">Procesos</h3>
			</div>
			<div class="box-body">
				<div class="row">
					{!! Form::model(Request::only('name', 'modalidad'), ['url' => '/procesoCreditoLote', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-6 col-sm-12">
						{!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Buscar', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-4 col-sm-12">
						{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione uno', 'autocomplete' => 'off']); !!}
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-block btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				<br>
				@if(!$procesos->total())
					<p>
						<div class="row">
							<div class="col-md-12">
								No se encontraron procesos <a href="{{ url('procesoCreditoLote/create') }}" class="btn btn-primary btn-xs">crear una nueva</a>
							</div>
						</div>
					</p>
				@else
					<div class="table-responsive">
						<table class="table table-hover">
							<thead>
								<tr>
									<th>Nro Proceso</th>
									<th>Fecha</th>
									<th>Modalidad</th>
									<th>Descripción</th>
									<th>Cantidad</th>
									<th class="text-center">Valor</th>
									<th>Estado</th>
									<th></th>
								</tr>
							</thead>
							<tbody>
								@foreach ($procesos as $proceso)
									<tr>
										<td>{{ $proceso->consecutivo_proceso }}</td>
										<td>{{ $proceso->fecha_proceso }}</td>
										<td>{{ $proceso->modalidad->nombre }}</td>
										<td>{{ str_limit($proceso->descripcion, 80) }}</td>
										<td>{{ $proceso->cantidad_solicitudes_creditos }}</td>
										<td class="text-right">${{ number_format($proceso->total_valor_creditos, 0) }}</td>
										<td>
											@php
												$label = "label-";
												switch($proceso->estado) {
													case 'PRECARGA':
														$label .= 'default';
														break;
													case 'CARGADO':
														$label .= 'info';
														break;
													case 'DESEMBOLSADO':
														$label .= 'success';
														break;
													case 'ANULADO':
														$label .= 'danger';
														break;
													default:
														$label .= 'default';
														break;
												}
											@endphp
											<span class="label {{ $label }}">{{ $proceso->estado }}</span>
										</td>
										<td>
											@if($proceso->estado == 'PRECARGA')
												<a class="btn btn-info btn-xs" title="Editar" href="{{ route('procesoCreditoLoteCargarCreditos', $proceso->id) }}"><i class="fa fa-edit"></i></a>
											@elseif($proceso->estado == 'CARGADO')
												<a class="btn btn-info btn-xs" title="Editar" href="{{ route('procesoCreditoLoteDesembolso', $proceso->id) }}"><i class="fa fa-edit"></i></a>
											@endif
											@if($proceso->estado == 'PRECARGA' || $proceso->estado == 'CARGADO')
												<a class="btn btn-danger btn-xs" title="Anular" href="{{ route('procesoCreditoLoteAnular', $proceso->id) }}"><i class="fa fa-close"></i></a>
											@endif
										</td>
									</tr>
								@endforeach
							</tbody>
						</table>
					</div>
				@endif
				<div class="row">
					<div class="col-md-12 text-center">
						{!! $procesos->appends(Request::only('name', 'odalidad'))->render() !!}
					</div>
				</div>
			</div>
			<div class="box-footer">
				<span class="label label-{{ $procesos->total()?'primary':'danger' }}">
					{{ $procesos->total() }}
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
		$("input[name='name']").enfocar();
		$(".select2").select2();
		$(window).formularioCrear("{{ url('procesoCreditoLote/create') }}");
	});
</script>
@endpush
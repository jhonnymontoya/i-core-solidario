@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Indicadores
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Indicadores</li>
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
		<div class="card card-primary">
			<div class="card-header with-border">
				<h3 class="card-title">Indicadores</h3>
			</div>
			<div class="card-body">
				<div class="row">
					{!! Form::model(Request::only('indicador'), ['url' => 'indicador', 'method' => 'GET', 'class' => 'form-horizontal', 'role' => 'search']) !!}
					<div class="col-md-11">
						<div class="form-group">
							<label class="col-sm-4 control-label">
								@if ($errors->has('indicador'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Seleccione tipo de indicador
							</label>
							<div class="col-sm-8">
								{!! Form::select('indicador', $tiposIndicadores, null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione tipo de indicador']) !!}
							</div>
						</div>
					</div>
					<div class="col-md-1 col-sm-12">
						<button type="submit" class="btn btn-success"><i class="fa fa-search"></i></button>								
					</div>
					{!! Form::close() !!}
				</div>
				@if($tipoIndicador)
					<hr>
					<div class="row">
						<div class="col-md-4">
							<dl class="dl-horizontal">
								<dt>Código</dt>
								<dd>{{ $tipoIndicador->codigo }}</dd>
							</dl>
						</div>
						<div class="col-md-4">
							<dl class="dl-horizontal">
								<dt>Periodicidad</dt>
								<dd>{{ $tipoIndicador->periodicidad }}</dd>
							</dl>
						</div>
						<div class="col-md-4">
							<dl class="dl-horizontal">
								<?php
									$variable = "";
									switch ($tipoIndicador->variable) {
										case 'PORCENTAJE':
											$variable = "%";
											break;
										case 'VALOR':
											$variable = "$";
											break;										
										default:
											$variable = "%";
											break;
									}
								?>
								<dt>Variable</dt>
								<dd>{{ $variable }}</dd>
							</dl>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<p><em>{{ $tipoIndicador->descripcion }}</em></p>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<a href="{{ route('indicadorCreate', $tipoIndicador->id) }}" class="btn btn-primary btn-xs">Actualizar</a>
						</div>
					</div>
					<br><br>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table">
								<thead>
									<th>Fecha inicio</th>
									<th>Fecha Fin</th>
									<th>Valor</th>
									<th></th>
								</thead>
								<tbody>
									@foreach($indicadores as $indicador)
										<tr>
											<td>{{ $indicador->fecha_inicio->toFormattedDateString() }}</td>
											<td>{{ $indicador->fecha_fin->toFormattedDateString() }}</td>
											<td>
												@if($variable == '%')
													{{ $indicador->valor }}{{ $variable }}
												@else
													{{ $variable }}{{ number_format($indicador->valor, 0) }}
												@endif
											</td>
											<td><a href="{{ route('indicadorEdit', $indicador->id) }}" class="btn btn-info btn-xs"><i class="fa fa-edit"></i></a></td>
										</tr>
									@endforeach
								</tbody>
							</table>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 text-center">
							{!! $indicadores->appends(Request::only('indicador'))->render() !!}
						</div>
					</div>
				@else
					<br><br>
					<h4>Seleccione un tipo de indicador</h4>
				@endif
			</div>
			<div class="card-footer">
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
	$(document).ready(function() {
		$(".select2").select2();
	});
</script>
@endpush
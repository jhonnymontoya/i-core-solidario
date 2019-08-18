@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Ajustes créditos en lote
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Ajustes créditos en lote</li>
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
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('error') }}
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				<div class="card card-warning">
					<div class="card-header with-border">
						<h3 class="card-title">Resumen carga archivo de créditos</h3>
					</div>
					{{-- INICIO card BODY --}}
					<div class="card-body">
						@if($cantidadErrores > 0)
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<div class="alert alert-warning">
									<h4>
										<i class="fa fa-warning"></i>&nbsp;Alerta!
									</h4>
									Hay registros con error
								</div>
							</div>
						</div>
						@endif
						<div class="row">
							<div class="col-md-10 col-md-offset-1">
								<dl class="dl-horizontal">
									<dt>Numero proceso:</dt>
									<dd>{{ $proceso->consecutivo_proceso }}</dd>
									<dt>Fecha de proceso:</dt>
									<dd>{{ $proceso->fecha_proceso }}</dd>
								</dl>
								<dl class="dl-horizontal">
									<dt class="text-success">Registros correctos:</dt>
									<dd>{{ $cantidadCorrectos }}</dd>
									<dt class="text-danger">Registros con error:</dt>
									<dd>{{ $cantidadErrores }}</dd>
								</dl>

								@if($cantidadErrores > 0)
								<div class="row">
									<div class="col-md-12 table-responsive">
										<table class="table">
											<thead>
												<tr>
													<th>Lista de errores</th>
												</tr>
											</thead>
											<tbody>
												@foreach($detalleErrores as $detalleError)
													<tr>
														<td>
															{{ $detalleError }}
														</td>
													</tr>
												@endforeach
											</tbody>
										</table>
									</div>
								</div>
								@endif
							</div>
						</div>
					</div>
					{{-- FIN card BODY --}}
					<div class="card-footer">
						@if($cantidadCorrectos == 0)
							<a class="btn btn-success" href="{{ route('ajusteCreditoLoteCargarCreditos', $proceso->id) }}">Continuar</a>
						@else
							<a class="btn btn-success" href="{{ route('ajusteCreditoLoteResumen', $proceso->id) }}">Continuar</a>
						@endif
					</div>
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

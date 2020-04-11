@extends('layouts.admin')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Recaudos nómina
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Recaudos nómina</li>
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
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Recaudos</h3>
				</div>
				<div class="card-body">
					{!! Form::model(Request::only('pagaduria'), ['url' => 'recaudosNomina', 'method' => 'GET', 'role' => 'search']) !!}
					<div class="row form-horizontal">
						<div class="col-md-6">
							<div class="form-group row">
								@php
									$valid = $errors->has('pagaduria') ? 'is-invalid' : '';
								@endphp
								<label class="col-sm-4 control-label">Seleccione pagaduría</label>
								<div class="col-sm-8">
									{!! Form::select('pagaduria', $pagadurias, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione pagaduria']) !!}
									@if ($errors->has('pagaduria'))
										<div class="invalid-feedback">{{ $errors->first('pagaduria') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-1 col-sm-12">
							<button type="submit" class="btn btn-outline-success"><i class="fa fa-search"></i></button>								
						</div>
					</div>
					{!! Form::close() !!}

					@if($pagaduria)
						<br>
						<div class="row">
							<div class="col-md-12">
								<div class="row">
									<div class="col-md-1"><strong>Pagaduría:</strong></div>
									<div class="col-md-2">{{ $pagaduria->nombre }}</div>

									<div class="col-md-2"><strong>Periodicidad:</strong></div>
									<div class="col-md-2">{{ Str::title($pagaduria->periodicidad_pago) }}</div>

									<?php
										$periodo = $pagaduria->calendarioRecaudos()
														->whereEstado('PROGRAMADO')
														->orderBy('fecha_recaudo')
														->first();

									?>
									<div class="col-md-2"><strong>Proximo periodo procesar:</strong></div>
									<div class="col-md-3">{{ $periodo ? $periodo->numero_periodo . '.' . $periodo->fecha_recaudo : 'Sin programación' }}</div>
								</div>							
							</div>
						</div>

						<br>
						<div class="row">
							<div class="col-md-12">
								<button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#confirmacion">Procesar periodo</button>
							</div>
						</div>

						<div class="row">
							<div class="col-md-12 table-responsive">
								<br>
								@if($pagaduria->controlProceso->count())
								<table class="table table-striped table-hover">
									<thead>
										<th>Periodo</th>
										<th>Estado</th>
										<th></th>
									</thead>
									<tbody>
										@php
											$procesos = $pagaduria->controlProceso()->orderBy('id', 'desc')->get();
										@endphp
										@foreach($procesos as $controlProceso)
											<tr>
												<td>{{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</td>
												<td>{{ Str::title($controlProceso->estado) }}</td>
												<td>
													<a class="btn btn-outline-primary btn-sm" href="{{ route('recaudosNominaGestion', $controlProceso->id) }}"><i class="fas fa-external-link-alt"></i></a>
												</td>
											</tr>
										@endforeach
									</tbody>
								</table>
								@endif
							</div>
						</div>

						{!! Form::open(['route' => ['recaudosNominaProcesar', $pagaduria], 'method' => 'put', 'role' => 'form', 'id' => 'formProcesar']) !!}
						<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
							<div class="modal-dialog modal-lg" role="document">
								<div class="modal-content">
									<div class="modal-header">
										<h4 class="modal-title" id="tituloConfirmacion">Generación recaudos</h4>
										<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
									</div>
									<div class="modal-body">
										<div class="row">
											<div class="col-md-12">
												<div class="alert alert-warning">
													<h4>
														<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
													</h4>
													Confirme el proceso de la generación de recaudos
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-md-10 col-md-offset-1">
												<dl class="dl-horizontal">
													<dt>Pagaduría:</dt>
													<dd>{{ $pagaduria->nombre }}</dd>
													<dt>Periodo:</dt>
													<dd>{{ $periodo ? $periodo->numero_periodo . '.' . $periodo->fecha_recaudo : 'Sin programación' }}</dd>
												</dl>
											</div>
										</div>
									</div>
									<div class="modal-footer">
										<a href="#" class="btn btn-outline-success" id="procesar">Procesar</a>
										{{--{!! Form::submit('Procesar', ['class' => 'btn btn-outline-success disabled', 'id' => 'procesar']) !!}--}}
										<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
									</div>
								</div>
							</div>
						</div>
						{!! Form::close() !!}
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
<script type="text/javascript">
	$(function(){
		$(".select2").select2();
	});
	$("#procesar").click(function(e){
		e.preventDefault();
		$("#procesar").addClass("disabled");
		$("#formProcesar").submit();
	});
</script>
@endpush
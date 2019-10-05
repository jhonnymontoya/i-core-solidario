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
					<h3 class="card-title">Aplicación</h3>
				</div>
				<div class="card-body">
					<a class="btn btn-outline-danger" href="{{ route('recaudosNominaGestion', $controlProceso->id) }}">Volver</a>
					<br>
					<br>
					<div class="row">
						<div class="col-md-12">
							<div class="row">
								<div class="col-md-1"><strong>Pagaduría</strong></div>
								<div class="col-md-2">{{ $controlProceso->pagaduria->nombre }}</div>

								<div class="col-md-1"><strong>Periodo</strong></div>
								<div class="col-md-2">{{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</div>

								<div class="col-md-2"><strong>Total cargado</strong></div>
								<div class="col-md-2">${{ number_format($controlProceso->total_aplicar) }}</div>
							</div>							
						</div>
					</div>

					<br>
					<div class="row">
						<div class="col-md-1">
							<h4>Datos</h4>
						</div>
						<div class="col-md-7">
							<a href="{{ route('recaudosNominaGenerarDatosAplicar', $controlProceso->id) }}" class="btn btn-outline-info"><i class="fa fa-upload"></i> Cargar generación</a>
							<a href="#" class="btn btn-outline-info" data-toggle="modal" data-target=".mod_carga"><i class="fa fa-upload"></i> Cargar archivo</a>
							<a href="{{ route('recaudosNominaEliminarDatosAplicar', $controlProceso->id) }}" class="btn btn-outline-warning"><i class="fa fa-eraser"></i> Limpiar carga</a>
						</div>
						<div class="col-md-4 text-right">
							<button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#confirmacion"><i class="far fa-check-circle"></i> Aplicar recaudos</button>
							<a href="#" class="btn btn-outline-danger"><i class="fa fa-exclamation-triangle"></i> Anular aplicación</a>
						</div>
					</div>
					<br>
					@if (Session::has('errores'))
						<div class="row">
							<div class="col-md-12 table-responsive">
								<table class="table">
									<thead>
										<tr>
											<th>Error</th>
										</tr>
									</thead>
									<tbody>
										@foreach (Session::get('errores') as $error)
											<tr>
												<td>{{ $error }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
						<br><br>
					@endif
					<div class="row">
						<div class="col-md-12 table-responsive">
							@if($controlProceso->datosParaAplicar->count())
								<table class="table">
									<thead>
										<tr>
											<th>Nombre</th>
											<th class="text-center">Valor generado</th>
											<th class="text-center">Valor a aplicar</th>
										</tr>
									</thead>
									<tbody>
										@foreach($controlProceso->datosParaAplicar as $datoParaAplicar)
											<tr>
												<td>{{ $datoParaAplicar->tercero->tipoIdentificacion->codigo }} {{ $datoParaAplicar->tercero->numero_identificacion }} -  {{ $datoParaAplicar->tercero->nombre }}</td>
												<td class="text-right">${{ number_format($datoParaAplicar->valor_generado) }}</td>
												<td class="text-right">${{ number_format($datoParaAplicar->valor_descontado) }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							@else
								<br>
								<label>No existen datos para aplicar</label>
							@endif
						</div>
					</div>
					<br>
					{!! Form::open(['route' => ['recaudosNominaProcesarRecaudos', $controlProceso], 'method' => 'put', 'role' => 'form', 'id' => 'formProcesar']) !!}
					<div class="modal fade" id="confirmacion" tabindex="-1" role="dialog" aria-labelledby="tituloConfirmacion">
						<div class="modal-dialog modal-lg" role="document">
							<div class="modal-content">
								<div class="modal-header">
									<h4 class="modal-title" id="tituloConfirmacion">Aplicar recaudos</h4>
									<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
								</div>
								<div class="modal-body">
									<div class="row">
										<div class="col-md-12">
											<div class="alert alert-warning">
												<h4>
													<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
												</h4>
												Confirme el proceso de aplicación de recaudos
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-12">
											<dl class="dl-horizontal">
												<dt>Pagaduría:</dt>
												<dd>{{ $controlProceso->pagaduria->nombre }}</dd>
												<dt>Periodo:</dt>
												<dd>{{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</dd>
												<dt>Total cargado:</dt>
												<dd>${{ number_format($controlProceso->total_aplicar) }}</dd>
											</dl>
										</div>
									</div>
								</div>
								<div class="modal-footer">
									<a class="btn btn-outline-success" id="procesar">Procesar</a>
									<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
								</div>
							</div>
						</div>
					</div>
					{!! Form::close() !!}
				</div>
			</div>
		</div>
	</section>
</div>
{!! Form::model($controlProceso, ['route' => ['recaudosNominaCargarRecaudos', $controlProceso], 'method' => 'put', 'role' => 'form', 'files' => true, 'id' => 'cargaRecaudos']) !!}
<div class="modal fade mod_carga" tabindex="-1" role="dialog">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Cargar archivo de recaudo</h4>
				<button class="close" data-dimiss="modal" aria-label="Cerrar">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<p>Cargue de archivo para el periodo {{ $controlProceso->calendarioRecaudo->numero_periodo . '.' . $controlProceso->calendarioRecaudo->fecha_recaudo }}</p>
				<div class="row">
					<div class="col-md-12">
						<div class="form-group {{ ($errors->has('archivoRecaudo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('archivoRecaudo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Archivo plano recaudos
							</label>
							{!! Form::file('archivoRecaudo') !!}
							@if ($errors->has('archivoRecaudo'))
								<span class="help-block">{{ $errors->first('archivoRecaudo') }}</span>
							@endif
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<a href="#" class="btn btn-outline-primary" id="cargar">Cargar</a>
				<button type="button" class="btn btn-outline-danger" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
	</div>
</div>
{!! Form::close() !!}
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#cargar").click(function(e){
			e.preventDefault();
			$("#cargar").addClass("disabled");
			$("#cargar").text("Cargando archivo...");
			$("#cargaRecaudos").submit();
		});
		$("#procesar").click(function(e){
			e.preventDefault();
			$("#procesar").addClass("disabled");
			$("#formProcesar").submit();
		});
	});
</script>
@endpush
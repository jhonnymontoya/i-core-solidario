@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Solicitudes de crédito en lote
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Solicitudes de crédito en lote</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($proceso, ['route' => ['procesoCreditoLoteCargarCreditosPut', $proceso], 'method' => 'put', 'role' => 'form', 'files' => true, 'id' => 'cargaCreditosLote']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Cargar archivo créditos en lote</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Fecha del proceso
								</label>
								<br>
								{{ $proceso->fecha_proceso }}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Modalidad de crédito
								</label>
								<br>
								{{ $proceso->modalidad->nombre }}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Estado
								</label>
								<br>
								@php
									$label = "badge-";
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
								<span class="badge badge-pill {{ $label }}">{{ $proceso->estado }}</span>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">
									Descripción
								</label>
								<br>
								{{ Str::limit($proceso->descripcion, 50) }}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Cuenta contra partida
								</label>
								<br>
								{{ $proceso->cuif->nombre }}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Tercero contra partida
								</label>
								<br>
								{{ $proceso->tercero->nombre_corto }}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Referencia
								</label>
								<br>
								{{ $proceso->referencia }}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-10">
							<div class="form-group {{ ($errors->has('archivoCredito')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('archivoCredito'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Seleccionar archivo
								</label>
								{!! Form::file('archivoCredito'); !!}
								@if ($errors->has('archivoCredito'))
									<span class="help-block">{{ $errors->first('archivoCredito') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">&nbsp;</label><br>
								<a href="{{ asset('plantillas/cartera/PlantillaCargueSolicitudesCreditoLote.csv') }}" download="PlantillaCargueSolicitudesCreditoLote.csv" class="btn bg-purple">Descargar plantilla</a>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					<a href="#" class="btn btn-outline-success" id="cargar">Continuar</a>
					<a href="{{ url('procesoCreditoLote') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
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
		$("#cargar").click(function(e){
			e.preventDefault();
			$("#cargar").addClass("disabled");
			$("#cargar").text("Cargando archivo...");
			$("#cargaCreditosLote").submit();
		});
	});
</script>
@endpush

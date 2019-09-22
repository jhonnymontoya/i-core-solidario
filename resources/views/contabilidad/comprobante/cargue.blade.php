@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Anular comprobante</li>
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
				<div class="card-header with-border">
					<h3 class="card-title">Cargar registros</h3>
				</div>
				{{-- INICIO card BODY --}}
				<div class="card-body">
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Tipo de Comprobante</label>
								{!! Form::text('tipo_comprobante_id', $comprobante->tipoComprobante->nombre_completo, ['class' => 'form-control', 'form' => 'comprobante', 'readonly']) !!}
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Fecha</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">
											<i class="fa fa-calendar"></i>
										</span>
									</div>
									{!! Form::text('fecha_movimiento', $comprobante->fecha_movimiento->format('d/m/Y'), ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'readonly']) !!}
								</div>
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					{{-- INICIO FILA --}}
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::text('descripcion', $comprobante->descripcion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción', 'form' => 'comprobante', 'readonly']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>
					{{-- FIN FILA --}}
					<div class="row">
						<div class="col-md-12 text-right">
							<a href="{{ url('/plantillas/contabilidad/PlantillaCargueMovimientosContables.csv') }}" download="PlantillaCargueMovimientosContables.csv" class="btn bg-purple">Descargar plantilla</a>
						</div>
					</div>
					<br><br>
					{!! Form::model($comprobante, ['url' => ['comprobante', $comprobante, 'cargue'], 'method' => 'put', 'role' => 'form', 'files' => true, 'id' => 'cargarArchivo']) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								@php
									$valid = $errors->has('archivo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Seleccione archivo</label>
								{!! Form::file('archivo', ['class' => [$valid, 'form-control']]) !!}
								@if ($errors->has('archivo'))
									<div class="invalid-feedback">{{ $errors->first('archivo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<label class="control-label">&nbsp;</label>
							<br>
							{!! Form::submit('Cargar', ['class' => 'btn btn-outline-success']) !!}
						</div>
					</div>
					{!! Form::close() !!}

					@if (!empty($resumen))
						<div class="row">
							<div class="col-md-12">
								<h4>Resumen</h4>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<p class="text-success">Total registros correctos: {{ $resumen["cantidadCorrectos"] }}</p>
							</div>
							<div class="col-md-6">
								<p>Total débitos: ${{ number_format($resumen["debitos"]) }}</p>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<p class="text-danger">Total registros incorrectos: {{ $resumen["cantidadErrores"] }}</p>
							</div>
							<div class="col-md-6">
								<p>Total créditos: ${{ number_format($resumen["creditos"]) }}</p>
							</div>
						</div>

						@if ($resumen["cantidadErrores"])
							<div class="row">
								<div class="col-md-12">
									<h4>Listado errores</h4>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<ul>
										@foreach ($resumen["errores"] as $error)
											<li>{{ $error }}</li>
										@endforeach
									</ul>
								</div>
							</div>
						@endif
					@endif
				</div>
				{{-- FIN card BODY --}}
				<div class="card-footer text-right">
					<a href="{{ route('comprobanteEdit', $comprobante) }}" class="btn btn-outline-danger pull-right">Volver al comprobante</a>
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.ui-menu{
		background-color: #fff;
		border: 0.1em solid #aaa;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$cargando = false;
		$("#cargarArchivo").submit(function(event){
			$form = $(this);
			$submit = $form.find("input[type='submit']");
			$submit.addClass('disabled');
			$submit.val('Cargando....');
			if(!$cargando) {
				$cargando = true;
			}
			else {
				event.preventDefault();
			}
		});
	});
</script>
@endpush

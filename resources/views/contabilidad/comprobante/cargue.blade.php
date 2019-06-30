@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Comprobantes
			<small>Contabilidad</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Contabilidad</a></li>
			<li class="active">Comprobantes</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="row">
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Cargar registros</h3>
					</div>
					{{-- INICIO BOX BODY --}}
					<div class="box-body">
						{{-- INICIO FILA --}}
						<div class="row form-horizontal">
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('tipo_comprobante_id')?'has-error':'') }}">
									<label class="col-sm-4 control-label">
										@if ($errors->has('tipo_comprobante_id'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Tipo de Comprobante
									</label>
									<div class="col-sm-8">
										{!! Form::text('tipo_comprobante_id', $comprobante->tipoComprobante->nombre_completo, ['class' => 'form-control', 'form' => 'comprobante', 'readonly']) !!}
										@if ($errors->has('tipo_comprobante_id'))
											<span class="help-block">{{ $errors->first('tipo_comprobante_id') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<div class="form-group {{ ($errors->has('fecha_movimiento')?'has-error':'') }}">
									<label class="col-sm-3 control-label">
										@if ($errors->has('fecha_movimiento'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Fecha
									</label>
									<div class="col-sm-9">
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											{!! Form::text('fecha_movimiento', $comprobante->fecha_movimiento->format('d/m/Y'), ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'autocomplete' => 'off', 'form' => 'comprobante', 'readonly']) !!}
										</div>
										@if ($errors->has('fecha_movimiento'))
											<span class="help-block">{{ $errors->first('fecha_movimiento') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						{{-- FIN FILA --}}
						{{-- INICIO FILA --}}
						<div class="row form-horizontal">
							<div class="col-md-12">
								<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
									<label class="col-sm-2 control-label">
										@if ($errors->has('descripcion'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Descripción
									</label>
									<div class="col-sm-10">
										{!! Form::text('descripcion', $comprobante->descripcion, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción', 'form' => 'comprobante', 'readonly']) !!}
										@if ($errors->has('descripcion'))
											<span class="help-block">{{ $errors->first('descripcion') }}</span>
										@endif
									</div>
								</div>
							</div>
						</div>
						{{-- FIN FILA --}}
						<div class="row">
							<div class="col-md-12">
								<div class="pull-right">
									<a href="{{ url('/plantillas/contabilidad/PlantillaCargueMovimientosContables.csv') }}" download="PlantillaCargueMovimientosContables.csv" class="btn bg-purple">Descargar plantilla</a>
									<a href="{{ route('comprobanteEdit', $comprobante) }}" class="btn btn-danger">Volver al comprobante</a>
								</div>
							</div>
						</div>
						<br><br>
						<div class="row form-horizontal">
							{!! Form::model($comprobante, ['url' => ['comprobante', $comprobante, 'cargue'], 'method' => 'put', 'role' => 'form', 'files' => true, 'id' => 'cargarArchivo']) !!}
							<div class="col-md-4">
								<div class="form-group {{ ($errors->has('archivo')?'has-error':'') }}">
									<label class="col-md-5 control-label">
										@if ($errors->has('archivo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Seleccione archivo
									</label>
									<div class="col-md-7">
										{!! Form::file('archivo', ['class' => 'form-control', ]) !!}
										@if ($errors->has('archivo'))
											<span class="help-block">{{ $errors->first('archivo') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="col-md-8">
								{!! Form::submit('Cargar', ['class' => 'btn btn-success']) !!}
							</div>
							{!! Form::close() !!}
						</div>

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
					{{-- FIN BOX BODY --}}
					<div class="box-footer">
						<a href="{{ route('comprobanteEdit', $comprobante) }}" class="btn btn-danger pull-right">Volver al comprobante</a>
					</div>
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

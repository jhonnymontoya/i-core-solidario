@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Definir comprobantes
						<small>Contabilidad</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Contabilidad</a></li>
						<li class="breadcrumb-item active">Definir comprobantes</li>
					</ol>
				</div>
			</div>
		</div>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::model($tipoComprobante, ['url' => ['tipoComprobante', $tipoComprobante], 'method' => 'PUT', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Editar tipo de comprobante</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('codigo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Código
								</label>
								{!! Form::text('codigo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Código']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('plantilla_impresion')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('plantilla_impresion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Formato de impresión
								</label>
								{!! Form::select('plantilla_impresion', $formatos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione']) !!}
								@if ($errors->has('plantilla_impresion'))
									<span class="help-block">{{ $errors->first('plantilla_impresion') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('comprobante_diario')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('comprobante_diario'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Comprobante diario
								</label>
								@if(!$tipoComprobante->movimientos->count())
									{!! Form::select('comprobante_diario', $comprobantes, null, ['class' => 'form-control', 'placeholder' => 'Seleccione']) !!}
								@else
									{!! Form::text('comprobante_diario', null, ['class' => 'form-control', 'placeholder' => 'Seleccione', 'readonly']) !!}
								@endif
								@if ($errors->has('comprobante_diario'))
									<span class="help-block">{{ $errors->first('comprobante_diario') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						{{-- INICIO CAMPO --}}
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('tipo_consecutivo')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('tipo_consecutivo'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Tipo consecutivo
								</label>
								<div>
									@if(!$tipoComprobante->movimientos->count())
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary {{ $tipoComprobante->tipo_consecutivo == 'A' | old('tipo_consecutivo') == 'A' ? 'active' : '' }}">
												{!! Form::radio('tipo_consecutivo', 'A', $tipoComprobante->tipo_consecutivo == 'A' | old('tipo_consecutivo') == 'A' ? true : false) !!}Año + Consecutivo
											</label>
											<label class="btn btn-primary {{ $tipoComprobante->tipo_consecutivo == 'B' | old('tipo_consecutivo') == 'B' ? 'active' : '' }}">
												{!! Form::radio('tipo_consecutivo', 'B', $tipoComprobante->tipo_consecutivo == 'B' | old('tipo_consecutivo') == 'B' ? true : false ) !!}Año + Mes + Consecutivo
											</label>
											<label class="btn btn-primary {{ $tipoComprobante->tipoConsecutivo == 'C' | old('tipo_consecutivo') == 'C' ? 'active' : '' }}">
												{!! Form::radio('tipo_consecutivo', 'C', $tipoComprobante->tipo_consecutivo == 'C' | old('tipo_consecutivo') == 'C' ? true : false) !!}Secuencia Continua
											</label>
										</div>
									@else
										<?php
											switch($tipoComprobante->tipo_consecutivo)
											{
												case 'A':
													echo 'Año + Consecutivo';
													break;

												case 'B':
													echo 'Año + Mes + Consecutivo';
													break;

												case 'C':
												default:
													echo 'Secuencia continua';
													break;
											}
										?>
									@endif
									@if ($errors->has('tipo_consecutivo'))
										<span class="help-block">{{ $errors->first('tipo_consecutivo') }}</span>
									@endif
								</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
						{{-- INICIO CAMPO --}}
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">
									Ejemplo de consecutivo
								</label>
								<div id="ejemplo">000001</div>
							</div>
						</div>
						{{-- FIN CAMPO --}}
						<div class="col-md-3">
							<div class="form-group {{ ($errors->has('modulo_id')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('modulo_id'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Módulo
								</label>
								@if(!$tipoComprobante->movimientos->count())
								{!! Form::select('modulo_id', $modulos, null, ['class' => 'form-control', 'placeholder' => 'Seleccione']) !!}
								@else
									<br>
									{{ $tipoComprobante->modulo->nombre }}
								@endif
								@if ($errors->has('modulo_id'))
									<span class="help-block">{{ $errors->first('modulo_id') }}</span>
								@endif
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
					<a href="{{ url('tipoComprobante') }}" class="btn btn-danger pull-right">Cancelar</a>
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
		$("input[name='tipo_consecutivo'][value='A']").change(function() {
			if($(this).is(":checked"))ejemplo('A');
		});
		$("input[name='tipo_consecutivo'][value='B']").change(function() {
			if($(this).is(":checked"))ejemplo('B');
		});
		$("input[name='tipo_consecutivo'][value='C']").change(function() {
			if($(this).is(":checked"))ejemplo('C');
		});

		function ejemplo(tipo){
			var muestra = "";
			switch(tipo)
			{
				case 'A':
					muestra = "{{ date('Y') }}000001";
					break;
				case 'B':
					muestra = "{{ date('Ym') }}000001";
					break;
				case 'C':
				default:
					muestra = "000001";
					break;
			}
			$("#ejemplo").text(muestra);
		}
		ejemplo('{{ $tipoComprobante->tipo_consecutivo }}');
	});
</script>
@endpush

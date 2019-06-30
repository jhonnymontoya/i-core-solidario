@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Editar socio
			<small>Socios</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Socios</a></li>
			<li class="active">Socio</li>
		</ol>
	</section>

	<section class="content">
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="row">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'beneficiarios'], 'method' => 'put', 'role' => 'form', 'class' => 'form-horizontal', 'data-maskMoney-removeMask']) !!}
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation"><a href="{{ route('socioEdit', $socio->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('socioEditLaboral', $socio->id) }}">Información laboral</a></li>
						<li role="presentation"><a href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a></li>
						<li role="presentation" class="active"><a href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a></li>
						<li role="presentation"><a href="{{ route('socioEditImagenes', $socio->id) }}">Imagen y firma</a></li>
						<li role="presentation"><a href="{{ route('socioEditFinanciera', $socio->id) }}">Situación financiera</a></li>
						<li role="presentation"><a href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a></li>
						<li role="presentation"><a href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{{-- INICIO FILA --}}
							<div class="row">
								<div class="col-md-12">
									<h4>Información de beneficiarios</h4>
								</div>
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('tipo_identificacion')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_identificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo identificación
										</label>
										<div class="col-sm-8">
											{!! Form::select('tipo_identificacion', $tiposIdentificacion, null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('tipo_identificacion'))
												<span class="help-block">{{ $errors->first('tipo_identificacion') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('identificacion')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('identificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Identificación
										</label>
										<div class="col-sm-9">
											{!! Form::text('identificacion', null, ['class' => 'form-control', 'placeholder' => 'Identificación', 'autocomplete' => 'off', 'autofocus']) !!}
											@if ($errors->has('identificacion'))
												<span class="help-block">{{ $errors->first('identificacion') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('nombres')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('nombres'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Nombres
										</label>
										<div class="col-sm-8">
											{!! Form::text('nombres', null, ['class' => 'form-control', 'placeholder' => 'Nombres', 'autocomplete' => 'off']) !!}
											@if ($errors->has('nombres'))
												<span class="help-block">{{ $errors->first('nombres') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('apellidos')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('apellidos'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Apellidos
										</label>
										<div class="col-sm-9">
											{!! Form::text('apellidos', null, ['class' => 'form-control', 'placeholder' => 'Apellidos', 'autocomplete' => 'off']) !!}
											@if ($errors->has('apellidos'))
												<span class="help-block">{{ $errors->first('apellidos') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('parentesco')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('parentesco'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Parentesco
										</label>
										<div class="col-sm-8">
											{!! Form::select('parentesco', $parentescos, null, ['class' => 'form-control select2']) !!}
											@if ($errors->has('parentesco'))
												<span class="help-block">{{ $errors->first('parentesco') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('beneficio')?'has-error':'') }}">
										<label class="col-sm-3 control-label">
											@if ($errors->has('beneficio'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Porcentaje benficio
										</label>
										<div class="col-sm-9">
											{!! Form::text('beneficio', 100 - $total, ['class' => 'form-control', 'placeholder' => '%', 'autocomplete' => 'off', 'data-mask' => '000']) !!}
											@if ($errors->has('beneficio'))
												<span class="help-block">{{ $errors->first('beneficio') }}</span>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							{{-- INICIO FILA --}}
							<div class="row">
								{{-- INICIO CAMPO --}}
								<div class="col-md-12">
									@if($beneficiarios->total() > 0)
										<div class="table-responsive">
											<table class="table table-hover" id="id_beneficiario">
												<thead>
													<th>Identificación</th>
													<th>Nombre</th>
													<th>Parentesco</th>
													<th>Beneficio</th>
													<th></th>
												</thead>
												<tbody>
													@foreach ($beneficiarios as $beneficiario)
														<tr data-id='{{ $beneficiario->id }}'>
															<td>{{ $beneficiario->tercero->identificacion }}</td>
															<td>{{ $beneficiario->tercero->nombre_corto }}</td>
															<td>{{ $beneficiario->parentesco->nombre }}</td>
															<td>{{ $beneficiario->porcentaje_beneficio }}%</td>
															<td>
																<a href="#" class="btn btn-danger btn-xs" onclick="javascript:rowDelete(this);">
																	<i class="fa fa-trash"></i>
																</a>
															</td>
														</tr>
													@endforeach
												</tbody>
												<tfoot>
													<th>Identificación</th>
													<th>Nombre</th>
													<th>Parentesco</th>
													<th>Beneficio</th>
													<th></th>
												</tfoot>
											</table>
										</div>
										<div class="row">
											<div class="col-md-12">
												<label class="pull-right">
													Total porcentaje asignado: {{ $total }}%, total porcentaje sin asignar: {{ 100 - $total }}%
												</label>
											</div>
										</div>
									@else
										<div class="col-md-12">
											<h3>No tiene beneficiarios</h3>
										</div>
									@endif
								</div>
								{{-- FIN CAMPO --}}
							</div>
							{{-- FIN FILA --}}
							<div class="row">
								<div class="col-md-12 text-center">
									{!! $beneficiarios->render() !!}
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<div class="col-sm-offset-2 col-sm-9">
									{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-success']) !!}
									<a href="{{ url('socio') }}" class="btn btn-danger">Volver</a>
									<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} pull-right {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	function rowDelete(row){
		row = $(row).parents('tr');
		rowId = row.data('id');
		
		$form = $('<form method="POST" action="{{ route('socio.beneficiario.delete', $socio->id) }}" accept-charset="UTF-8"></form>');
		$form.append('<input name="_method" type="hidden" value="DELETE">');
		$form.append('<input name="_token" type="hidden" value="{{ csrf_token() }}">');
		$form.append('<input name="id" type="hidden" value="' + rowId + '">');
		$('body').append($form);
		$form.submit();
	}
</script>
@endpush

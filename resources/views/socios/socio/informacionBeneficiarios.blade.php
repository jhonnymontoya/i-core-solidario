@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar socio
						<small>Socios</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Socios</a></li>
						<li class="breadcrumb-item active">Editar socio</li>
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
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($socio, ['url' => ['socio', $socio, 'beneficiarios'], 'method' => 'put', 'role' => 'form', 'data-maskMoney-removeMask']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEdit', $socio->id) }}">General</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditLaboral', $socio->id) }}">Laboral</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditContacto', $socio->id) }}">Contacto</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('socioEditBeneficiarios', $socio->id) }}">Beneficiarios</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditImagenes', $socio->id) }}">Imagen</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditFinanciera', $socio->id) }}">Financiera</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditTarjetasCredito', $socio->id) }}">Tarjetas de crédito</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('socioEditObligacionesFinancieras', $socio->id) }}">Obligaciones financieras</a>
						</li>
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
									<div class="form-group">
										@php
											$valid = $errors->has('tipo_identificacion') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Tipo identificación</label>
										{!! Form::select('tipo_identificacion', $tiposIdentificacion, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('tipo_identificacion'))
											<div class="invalid-feedback">{{ $errors->first('tipo_identificacion') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('identificacion') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Identificación</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('identificacion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Identificación', 'autofocus']) !!}
											@if ($errors->has('identificacion'))
												<div class="invalid-feedback">{{ $errors->first('identificacion') }}</div>
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
									<div class="form-group">
										@php
											$valid = $errors->has('nombres') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Nombres</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('nombres', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombres']) !!}
											@if ($errors->has('nombres'))
												<div class="invalid-feedback">{{ $errors->first('nombres') }}</div>
											@endif
										</div>
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('apellidos') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Apellidos</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('apellidos', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Apellidos']) !!}
											@if ($errors->has('apellidos'))
												<div class="invalid-feedback">{{ $errors->first('apellidos') }}</div>
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
									<div class="form-group">
										@php
											$valid = $errors->has('parentesco') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Parentesco</label>
										{!! Form::select('parentesco', $parentescos, null, ['class' => [$valid, 'form-control', 'select2']]) !!}
										@if ($errors->has('parentesco'))
											<div class="invalid-feedback">{{ $errors->first('parentesco') }}</div>
										@endif
									</div>
								</div>
								{{-- FIN CAMPO --}}
								{{-- INICIO CAMPO --}}
								<div class="col-md-6">
									<div class="form-group">
										@php
											$valid = $errors->has('beneficio') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Porcentaje beneficio</label>
										<div class="input-group">
											<div class="input-group-prepend">
												<span class="input-group-text">
													<i class="fa fa-table"></i>
												</span>
											</div>
											{!! Form::text('beneficio', 100 - $total, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => '%', 'data-mask' => '000']) !!}
											@if ($errors->has('beneficio'))
												<div class="invalid-feedback">{{ $errors->first('beneficio') }}</div>
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
																<a href="#" class="btn btn-outline-danger btn-sm" onclick="javascript:rowDelete(this);">
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
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('socio') }}" class="btn btn-outline-danger">Volver</a>
					<a href="{{ route('socioAfiliacion', $socio) }}" class="btn btn-outline-{{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'default' : 'info') }} {{ (($socio->estado == 'ACTIVO' || $socio->estado == 'NOVEDAD') ? 'disabled' : '') }}">Procesar afiliación</a>
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

@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Editar entidad
						<small>General</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> General</a></li>
						<li class="breadcrumb-item active">Editar entidad</li>
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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="row">
			<div class="col-sm-12">
				<div class="nav-tabs-custom">
					<ul class="nav nav-tabs">
						<li role="presentation" class="active"><a href="{{ route('entidadEdit', $entidad->id) }}">Información básica</a></li>
						<li role="presentation"><a href="{{ route('entidadEditImagenes', $entidad->id) }}">Imágenes</a></li>
					</ul>
					<div class="tab-content">
						<div class="tab-pane active">
							{!! Form::model($entidad, ['url' => ['entidad', $entidad], 'method' => 'PUT', 'role' => 'form']) !!}
							<div class="row">
								<div class="col-md-12">
									<div class="form-group {{ ($errors->has('razon')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('razon'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Razón social
										</label>
										{!! Form::text('razon', $entidad->terceroEntidad->razon_social, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Razón social', 'autofocus']) !!}
										@if ($errors->has('razon'))
											<span class="help-block">{{ $errors->first('razon') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('sigla')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('sigla'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Sigla
										</label>
										{!! Form::text('sigla', $entidad->terceroEntidad->sigla, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Sigla']) !!}
										@if ($errors->has('sigla'))
											<span class="help-block">{{ $errors->first('sigla') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-10">
											<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
												<label class="control-label">
													@if ($errors->has('nit'))
														<i class="fa fa-times-circle-o"></i>
													@endif
													Número de identificación tributaria
												</label>
												{!! Form::text('nit', $entidad->terceroEntidad->numero_identificacion, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria', 'readonly']) !!}
												@if ($errors->has('nit'))
													<span class="help-block">{{ $errors->first('nit') }}</span>
												@endif
											</div>
										</div>
										<div class="col-md-2">
											<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
												<label class="control-label">
													DV
												</label>
												<br>
												<label class="dv" style="background-color: #eee;">0</label>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('actividad_economica')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('actividad_economica'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Actividad económica
										</label>
										{!! Form::select('actividad_economica', [], $entidad->terceroEntidad->actividad_economica_id, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una actividad económica']) !!}
										@if ($errors->has('actividad_economica'))
											<span class="help-block">{{ $errors->first('actividad_economica') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('fecha_inicio_contabilidad')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('fecha_inicio_contabilidad'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha de inicio de contabilidad
										</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											{!! Form::text('fecha_inicio_contabilidad', $entidad->fecha_inicio_contabilidad, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
										</div>
										@if ($errors->has('fecha_inicio_contabilidad'))
											<span class="help-block">{{ $errors->first('fecha_inicio_contabilidad') }}</span>
										@endif
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('usa_dependencia')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('usa_dependencia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Usa dependencias?
										</label>
										<br>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary{{ $entidad->usa_dependencia?' active':'' }}">
												{!! Form::radio('usa_dependencia', '1', false) !!}Sí
											</label>
											<label class="btn btn-danger{{ !$entidad->usa_dependencia?' active':'' }}">
												{!! Form::radio('usa_dependencia', '0', true) !!}No
											</label>
										</div>
										@if ($errors->has('usa_dependencia'))
											<span class="help-block">{{ $errors->first('usa_dependencia') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('usa_centro_costos')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('usa_centro_costos'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Usa centros de costo?
										</label>
										<br>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary{{ $entidad->usa_centro_costos?' active':'' }}">
												{!! Form::radio('usa_centro_costos', '1', false) !!}Sí
											</label>
											<label class="btn btn-danger{{ !$entidad->usa_centro_costos?' active':'' }}">
												{!! Form::radio('usa_centro_costos', '0', true) !!}No
											</label>
										</div>
										@if ($errors->has('usa_centro_costos'))
											<span class="help-block">{{ $errors->first('usa_centro_costos') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group {{ ($errors->has('esta_activo')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('esta_activo'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Activo?
										</label>
										<br>
										<div class="btn-group" data-toggle="buttons">
											<label class="btn btn-primary{{ $entidad->terceroEntidad->esta_activo?' active':'' }}">
												{!! Form::radio('esta_activo', '1', $entidad->terceroEntidad->esta_activo?true:false) !!}Sí
											</label>
											<label class="btn btn-danger{{ !$entidad->terceroEntidad->esta_activo?' active':'' }}">
												{!! Form::radio('esta_activo', '0', $entidad->terceroEntidad->esta_activo?false:true) !!}No
											</label>
										</div>
										@if ($errors->has('esta_activo'))
											<span class="help-block">{{ $errors->first('esta_activo') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group {{ ($errors->has('pagina_web')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('pagina_web'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Página web
										</label>
										{!! Form::text('pagina_web', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Página web']) !!}
										@if ($errors->has('pagina_web'))
											<span class="help-block">{{ $errors->first('pagina_web') }}</span>
										@endif
									</div>
								</div>
							</div>

							<h4>Constitución</h4>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('fecha_constitucion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('fecha_constitucion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Fecha de constitución
										</label>
										<div class="input-group">
											<div class="input-group-addon">
												<i class="fa fa-calendar"></i>
											</div>
											{!! Form::text('fecha_constitucion', $entidad->terceroEntidad->fecha_constitucion, ['class' => 'form-control', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
										</div>
										@if ($errors->has('fecha_constitucion'))
											<span class="help-block">{{ $errors->first('fecha_constitucion') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('numero_matricula')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('numero_matricula'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Número matricula mercantil
										</label>
										{!! Form::text('numero_matricula', $entidad->terceroEntidad->numero_matricula, ['class' => 'form-control', 'placeholder' => 'Número matricula mercantil', 'autocomplete' => 'off']) !!}
										@if ($errors->has('numero_matricula'))
											<span class="help-block">{{ $errors->first('numero_matricula') }}</span>
										@endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('direccion_notificacion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('direccion_notificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Dirección de notificación
										</label>
										{!! Form::text('direccion_notificacion', $entidad->terceroEntidad->contactos->count()?$entidad->terceroEntidad->contactos[0]->direccion:null, ['class' => 'form-control', 'placeholder' => 'Dirección de notificación', 'autocomplete' => 'off']) !!}
										@if ($errors->has('direccion_notificacion'))
											<span class="help-block">{{ $errors->first('direccion_notificacion') }}</span>
										@endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group {{ ($errors->has('ciudad_direccion_notificacion')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('ciudad_direccion_notificacion'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Ciudad dirección de notificación
										</label>
										{!! Form::select('ciudad_direccion_notificacion', [], $entidad->terceroEntidad->contactos->count()?$entidad->terceroEntidad->contactos[0]->ciudad_id:null, ['class' => 'form-control select2', 'placeholder' => 'Ciudad dirección de notificación']) !!}
										@if ($errors->has('ciudad_direccion_notificacion'))
											<span class="help-block">{{ $errors->first('ciudad_direccion_notificacion') }}</span>
										@endif
									</div>
								</div>
							</div>
							<br>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<div class="col-sm-offset-2 col-sm-9">
											{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-success']) !!}
											<a href="{{ url('entidad') }}" class="btn btn-danger pull-right">Cancelar</a>
										</div>
									</div>
								</div>
							</div>
							{!! Form::close() !!}
							
							<br>

							<ul class="nav nav-tabs" role="tablist">
								<li role="presentation" class="active">
									<a href="#directivos" aria-controls="directivos" role="tab" data-toggle="tab">Directivos</a>
								</li>
								<li role="presentation">
									<a href="#representantelegal" aria-controls="representantelegal" role="tab" data-toggle="tab">Representante Legal</a>
								</li>
								<li role="presentation">
									<a href="#controlsocial" aria-controls="controlsocial" role="tab" data-toggle="tab">Control social</a>
								</li>
								<li role="presentation">
									<a href="#comiteCartera" aria-controls="comiteCartera" role="tab" data-toggle="tab">Comité de cartera</a>
								</li>
								<li role="presentation">
									<a href="#comiteRiesgoLiquidez" aria-controls="comiteRiesgoLiquidez" role="tab" data-toggle="tab">Comité riesgo de liquidez</a>
								</li>
							</ul>

							<div class="tab-content">
								<div role="tabpanel" class="tab-pane fade in active" id="directivos">
									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-xs-12">
													{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'directivo']) !!}
													{!! Form::hidden('directivo_entidad', $entidad->id) !!}
													<div class="row">
														<div class="col-md-3 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Seleccione socio
																</label>
																{!! Form::select('directivo_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione directivo', 'required']) !!}
															</div>
														</div>
														<div class="col-md-3 col-sm-12">
															<label class="control-label">
																Calidad de nombramiento
															</label>
															<br>
															<div class="btn-group" data-toggle="buttons">
																<label class="btn btn-primary active">
																	{!! Form::radio('directivo_calidad', 'PRINCIPAL', true) !!}Principal
																</label>
																<label class="btn btn-primary">
																	{!! Form::radio('directivo_calidad', 'SUPLENTE', false) !!}Suplente
																</label>
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<label class="control-label">
																Fecha nombramiento
															</label>
															{!! Form::text('directivo_fecha_nombramiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'required', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Periodos
																</label>
																{!! Form::select('directivo_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">&nbsp;</label>
																<br>
																{!! Form::submit('Agregar', ['class' => 'btn btn-success btn-block pull-right']) !!}
															</div>
														</div>
													</div>
													{!! Form::close() !!}
												</div>
											</div>
											<br>
											<div class="table-responsive">
												<table class="table table-hover" id="id_directivo">
													<thead>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														@foreach($organismos as $organismo)
															@if($organismo->tipo_organo == 'DIRECTIVO')
																<tr data-id="{{ $organismo->id }}">
																	<td>{{ $organismo->tercero->numero_identificacion }}</td>
																	<td>{{ $organismo->tercero->nombre_corto }}</td>
																	<td>{{ $organismo->calidad }}</td>
																	<td>{{ $organismo->fecha_nombramiento }}</td>
																	<td>{{ $organismo->periodos }}</td>
																	<td>{{ $organismo->tercero->socio->estado }}</td>
																	<td>
																		<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-danger btn-xs">
																			<i class="fa fa-trash"></i>
																		</a>
																	</td>
																</tr>
															@endif
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="representantelegal">
									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-xs-12">
													{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'id_representantelegal']) !!}
													{!! Form::hidden('legal_entidad', $entidad->id) !!}
													<div class="row">
														<div class="col-md-3 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Seleccione tercero
																</label>
																{!! Form::select('legal_tercero', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione representante legal', 'required']) !!}
															</div>
														</div>
														<div class="col-md-3 col-sm-12">
															<label class="control-label">
																Calidad de nombramiento
															</label>
															<br>
															<div class="btn-group" data-toggle="buttons">
																<label class="btn btn-primary active">
																	{!! Form::radio('legal_calidad', 'PRINCIPAL', true) !!}Principal
																</label>
																<label class="btn btn-primary">
																	{!! Form::radio('legal_calidad', 'SUPLENTE', false) !!}Suplente
																</label>
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<label class="control-label">
																Fecha nombramiento
															</label>
															{!! Form::text('legal_fecha_nombramiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'required', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Periodos
																</label>
																{!! Form::select('legal_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">&nbsp;</label>
																<br>
																{!! Form::submit('Agregar', ['class' => 'btn btn-success btn-block pull-right']) !!}
															</div>
														</div>
													</div>
													{!! Form::close() !!}
												</div>
											</div>
											<br>
											<div class="table-responsive">
												<table class="table table-hover" id="id_legal">
													<thead>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														@foreach($organismos as $organismo)
															@if($organismo->tipo_organo == 'REPRESENTANTE_LEGAL')
																<tr data-id="{{ $organismo->id }}">
																	<td>{{ $organismo->tercero->numero_identificacion }}</td>
																	<td>{{ $organismo->tercero->nombre_corto }}</td>
																	<td>{{ $organismo->calidad }}</td>
																	<td>{{ $organismo->fecha_nombramiento }}</td>
																	<td>{{ $organismo->periodos }}</td>
																	<td>
																		<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-danger btn-xs">
																			<i class="fa fa-trash"></i>
																		</a>
																	</td>
																</tr>
															@endif
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="controlsocial">
									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-xs-12">
													{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'controlsocial']) !!}
													{!! Form::hidden('social_entidad', $entidad->id) !!}
													<div class="row">
														<div class="col-md-3 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Seleccione socio
																</label>
																{!! Form::select('social_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
															</div>
														</div>
														<div class="col-md-3 col-sm-12">
															<label class="control-label">
																Calidad de nombramiento
															</label>
															<br>
															<div class="btn-group" data-toggle="buttons">
																<label class="btn btn-primary active">
																	{!! Form::radio('social_calidad', 'PRINCIPAL', true) !!}Principal
																</label>
																<label class="btn btn-primary">
																	{!! Form::radio('social_calidad', 'SUPLENTE', false) !!}Suplente
																</label>
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<label class="control-label">
																Fecha nombramiento
															</label>
															{!! Form::text('social_fecha_nombramiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'required', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Periodos
																</label>
																{!! Form::select('social_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">&nbsp;</label>
																<br>
																{!! Form::submit('Agregar', ['class' => 'btn btn-success btn-block pull-right']) !!}
															</div>
														</div>
													</div>
													{!! Form::close() !!}
												</div>
											</div>
											<br>
											<div class="table-responsive">
												<table class="table table-hover" id="id_controlsocial">
													<thead>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														@foreach($organismos as $organismo)
															@if($organismo->tipo_organo == 'CONTROL_SOCIAL')
																<tr data-id="{{ $organismo->id }}">
																	<td>{{ $organismo->tercero->numero_identificacion }}</td>
																	<td>{{ $organismo->tercero->nombre_corto }}</td>
																	<td>{{ $organismo->calidad }}</td>
																	<td>{{ $organismo->fecha_nombramiento }}</td>
																	<td>{{ $organismo->periodos }}</td>
																	<td>{{ $organismo->tercero->socio->estado }}</td>
																	<td>
																		<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-danger btn-xs">
																			<i class="fa fa-trash"></i>
																		</a>
																	</td>
																</tr>
															@endif
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="comiteCartera">
									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-xs-12">
													{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'comiteCartera']) !!}
													{!! Form::hidden('comitecartera_entidad', $entidad->id) !!}
													<div class="row">
														<div class="col-md-3 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Seleccione socio
																</label>
																{!! Form::select('comitecartera_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
															</div>
														</div>
														<div class="col-md-3 col-sm-12">
															<label class="control-label">
																Calidad de nombramiento
															</label>
															<br>
															<div class="btn-group" data-toggle="buttons">
																<label class="btn btn-primary active">
																	{!! Form::radio('comitecartera_calidad', 'PRINCIPAL', true) !!}Principal
																</label>
																<label class="btn btn-primary">
																	{!! Form::radio('comitecartera_calidad', 'SUPLENTE', false) !!}Suplente
																</label>
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<label class="control-label">
																Fecha nombramiento
															</label>
															{!! Form::text('comitecartera_fecha_nombramiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'required', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Periodos
																</label>
																{!! Form::select('comitecartera_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">&nbsp;</label>
																<br>
																{!! Form::submit('Agregar', ['class' => 'btn btn-success btn-block pull-right']) !!}
															</div>
														</div>
													</div>
													{!! Form::close() !!}
												</div>
											</div>
											<br>
											<div class="table-responsive">
												<table class="table table-hover" id="id_comiteCartera">
													<thead>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														@foreach($organismos as $organismo)
															@if($organismo->tipo_organo == 'COMITE_CARTERA')
																<tr data-id="{{ $organismo->id }}">
																	<td>{{ $organismo->tercero->numero_identificacion }}</td>
																	<td>{{ $organismo->tercero->nombre_corto }}</td>
																	<td>{{ $organismo->calidad }}</td>
																	<td>{{ $organismo->fecha_nombramiento }}</td>
																	<td>{{ $organismo->periodos }}</td>
																	<td>{{ $organismo->tercero->socio->estado }}</td>
																	<td>
																		<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-danger btn-xs">
																			<i class="fa fa-trash"></i>
																		</a>
																	</td>
																</tr>
															@endif
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
								<div role="tabpanel" class="tab-pane fade" id="comiteRiesgoLiquidez">
									<br>
									<div class="row">
										<div class="col-md-12">
											<div class="row">
												<div class="col-xs-12">
													{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'comiteRiesgoLiquidez']) !!}
													{!! Form::hidden('comiteriesgoliquidez_entidad', $entidad->id) !!}
													<div class="row">
														<div class="col-md-3 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Seleccione socio
																</label>
																{!! Form::select('comiteriesgoliquidez_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
															</div>
														</div>
														<div class="col-md-3 col-sm-12">
															<label class="control-label">
																Calidad de nombramiento
															</label>
															<br>
															<div class="btn-group" data-toggle="buttons">
																<label class="btn btn-primary active">
																	{!! Form::radio('comiteriesgoliquidez_calidad', 'PRINCIPAL', true) !!}Principal
																</label>
																<label class="btn btn-primary">
																	{!! Form::radio('comiteriesgoliquidez_calidad', 'SUPLENTE', false) !!}Suplente
																</label>
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<label class="control-label">
																Fecha nombramiento
															</label>
															{!! Form::text('comiteriesgoliquidez_fecha_nombramiento', null, ['class' => 'form-control pull-right', 'placeholder' => 'dd/mm/yyyy', 'required', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true', 'autocomplete' => 'off']) !!}
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">
																	Periodos
																</label>
																{!! Form::select('comiteriesgoliquidez_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
															</div>
														</div>
														<div class="col-md-2 col-sm-12">
															<div class="form-group">
																<label class="control-label">&nbsp;</label>
																<br>
																{!! Form::submit('Agregar', ['class' => 'btn btn-success btn-block pull-right']) !!}
															</div>
														</div>
													</div>
													{!! Form::close() !!}
												</div>
											</div>
											<br>
											<div class="table-responsive">
												<table class="table table-hover" id="id_comiteRiesgoLiquidez">
													<thead>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</thead>
													<tbody>
														@foreach($organismos as $organismo)
															@if($organismo->tipo_organo == 'COMITE_RIESGO_LIQUIDEZ')
																<tr data-id="{{ $organismo->id }}">
																	<td>{{ $organismo->tercero->numero_identificacion }}</td>
																	<td>{{ $organismo->tercero->nombre_corto }}</td>
																	<td>{{ $organismo->calidad }}</td>
																	<td>{{ $organismo->fecha_nombramiento }}</td>
																	<td>{{ $organismo->periodos }}</td>
																	<td>{{ $organismo->tercero->socio->estado }}</td>
																	<td>
																		<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-danger btn-xs">
																			<i class="fa fa-trash"></i>
																		</a>
																	</td>
																</tr>
															@endif
														@endforeach
													</tbody>
													<tfoot>
														<tr>
															<th>Identificación</th>
															<th>Nombre</th>
															<th>Calidad</th>
															<th>Fecha nombramiento</th>
															<th>Periodos</th>
															<th>Estado</th>
															<th></th>
														</tr>
													</tfoot>
												</table>
											</div>
										</div>
									</div>
								</div>
							</div>

							<br>

						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>

<div class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title">Error</h4>
</div>
<div class="modal-body">
<p></p>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
</div>
</div>
</div>

{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("select[name='actividad_economica']").select2();

		$("input[name='nit']").change(function(){
			digitoVerificacion(this.value, "{{ url('api/tercero/dv') }}", $(".dv"));
		});

		$("input[name='nit']").keyup(function(){
			digitoVerificacion(this.value, "{{ url('api/tercero/dv') }}", $(".dv"));
		});

		$("select[name='ciudad_direccion_notificacion']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_direccion_notificacion') | $entidad->terceroEntidad->contactos->count()?$entidad->terceroEntidad->contactos[0]->ciudad_id:null }}"});
		$("select[name='actividad_economica']").selectAjax("{{ url('ciiu') }}", {id:{{ $entidad->terceroEntidad->actividad_economica_id }}});
		$("select[name='directivo_socio']").selectAjax("{{ url('api/socio') }}", {entidad: {{ $entidad->id }}});
		
		digitoVerificacion($("input[name='nit']").val(), "{{ url('api/tercero/dv') }}", $(".dv"));

		$("form[name='directivo']").on("submit", function(event){
			event.preventDefault();
			$.ajax({
					url: "{{ url('api/entidad/directivo') }}",
					method: 'PUT',
					dataType: 'json',
					data: $(this).serialize()
			}).done(function(data){
				var fila = "<tr data-id='" + data.id + "'><td>" + data.identificacion + "</td><td>" + data.nombre + "</td><td>" + data.calidad;
				fila += "</td><td>" + data.fecha_nombramiento + "</td><td>" + data.periodos + "</td><td>";
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-danger btn-xs'>";
				fila += "<i class='fa fa-trash'></i></a></td></tr>";
				$("#id_directivo > tbody").append(fila);
			}).fail(function(data){
				$(".modal-body > p").html(data.responseText);
				$(".modal").modal("toggle");
			});
		});

		$("form[name='id_representantelegal']").on("submit", function(event){
			event.preventDefault();
			$.ajax({
					url: "{{ url('api/entidad/legal') }}",
					method: 'PUT',
					dataType: 'json',
					data: $(this).serialize()
			}).done(function(data){
				var fila = "<tr data-id='" + data.id + "'><td>" + data.identificacion + "</td><td>" + data.nombre + "</td><td>" + data.calidad;
				fila += "</td><td>" + data.fecha_nombramiento + "</td><td>" + data.periodos + "</td>";
				fila += "<td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-danger btn-xs'>";
				fila += "<i class='fa fa-trash'></i></a></td></tr>";
				$("#id_legal > tbody").append(fila);
			}).fail(function(data){
				$(".modal-body > p").html(data.responseText);
				$(".modal").modal("toggle");
			});
		});

		$("form[name='controlsocial']").on("submit", function(event){
			event.preventDefault();
			$.ajax({
					url: "{{ url('api/entidad/controlSocial') }}",
					method: 'PUT',
					dataType: 'json',
					data: $(this).serialize()
			}).done(function(data){
				var fila = "<tr data-id='" + data.id + "'><td>" + data.identificacion + "</td><td>" + data.nombre + "</td><td>" + data.calidad;
				fila += "</td><td>" + data.fecha_nombramiento + "</td><td>" + data.periodos + "</td><td>";
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-danger btn-xs'>";
				fila += "<i class='fa fa-trash'></i></a></td></tr>";
				$("#id_controlsocial > tbody").append(fila);
			}).fail(function(data){
				$(".modal-body > p").html(data.responseText);
				$(".modal").modal("toggle");
			});
		});

		$("form[name='comiteCartera']").on("submit", function(event){
			event.preventDefault();
			$.ajax({
					url: "{{ url('api/entidad/comiteCartera') }}",
					method: 'PUT',
					dataType: 'json',
					data: $(this).serialize()
			}).done(function(data){
				var fila = "<tr data-id='" + data.id + "'><td>" + data.identificacion + "</td><td>" + data.nombre + "</td><td>" + data.calidad;
				fila += "</td><td>" + data.fecha_nombramiento + "</td><td>" + data.periodos + "</td><td>";
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-danger btn-xs'>";
				fila += "<i class='fa fa-trash'></i></a></td></tr>";
				$("#id_comiteCartera > tbody").append(fila);
			}).fail(function(data){
				$(".modal-body > p").html(data.responseText);
				$(".modal").modal("toggle");
			});
		});

		$("form[name='comiteRiesgoLiquidez']").on("submit", function(event){
			event.preventDefault();
			$.ajax({
					url: "{{ url('api/entidad/comiteRiesgoLiquidez') }}",
					method: 'PUT',
					dataType: 'json',
					data: $(this).serialize()
			}).done(function(data){
				var fila = "<tr data-id='" + data.id + "'><td>" + data.identificacion + "</td><td>" + data.nombre + "</td><td>" + data.calidad;
				fila += "</td><td>" + data.fecha_nombramiento + "</td><td>" + data.periodos + "</td><td>";
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-danger btn-xs'>";
				fila += "<i class='fa fa-trash'></i></a></td></tr>";
				$("#id_comiteRiesgoLiquidez > tbody").append(fila);
			}).fail(function(data){
				$(".modal-body > p").html(data.responseText);
				$(".modal").modal("toggle");
			});
		});

		$('a[data-toggle="tab"]').on('shown.bs.tab', function (e)
		{
			if($(e.target).attr('aria-controls') == "representantelegal")
			{
				$("select[name='legal_tercero']").selectAjax("{{ url('api/tercero') }}", {entidad: {{ $entidad->id }}});
			}
			if($(e.target).attr('aria-controls') == "controlsocial")
			{
				$("select[name='social_socio']").selectAjax("{{ url('api/socio') }}", {entidad: {{ $entidad->id }}});
			}
			if($(e.target).attr('aria-controls') == "comiteCartera")
			{
				$("select[name='comitecartera_socio']").selectAjax("{{ url('api/socio') }}", {entidad: {{ $entidad->id }}});
			}
			if($(e.target).attr('aria-controls') == "comiteRiesgoLiquidez")
			{
				$("select[name='comiteriesgoliquidez_socio']").selectAjax("{{ url('api/socio') }}", {entidad: {{ $entidad->id }}});
			}
		});
	});

	function rowDelete(row){
		row = $(row).parents('tr');
		rowId = row.data('id');
		row.fadeOut();
		var data = "_token={{ csrf_token() }}&id=" + rowId;
		$.ajax({
				url: "{{ url('api/entidad/directivo') }}",
				method: 'DELETE',
				dataType: 'json',
				data: data
		}).done(function(data){
		}).fail(function(data){
			row.fadeIn();
			$(".modal-body > p").html('Error eliminando el registro');
			$(".modal").modal("toggle");
		});
		return false;
	}
</script>
@endpush

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

		<div class="container-fluid">
			{!! Form::model($entidad, ['url' => ['entidad', $entidad], 'method' => 'PUT', 'role' => 'form']) !!}
			<div class="card card-solid">
				<div class="card-body">
					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('entidadEdit', $entidad->id) }}">Información básica</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('entidadEditImagenes', $entidad->id) }}">Imágenes</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane active">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
									    @php
									        $valid = $errors->has('razon') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Razón social</label>
									    {!! Form::text('razon', $entidad->terceroEntidad->razon_social, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Razón social', 'autofocus']) !!}
									    @if ($errors->has('razon'))
									        <div class="invalid-feedback">{{ $errors->first('razon') }}</div>
									    @endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('sigla') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Sigla</label>
									    {!! Form::text('sigla', $entidad->terceroEntidad->sigla, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Sigla']) !!}
									    @if ($errors->has('sigla'))
									        <div class="invalid-feedback">{{ $errors->first('sigla') }}</div>
									    @endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="row">
										<div class="col-md-10">
											<div class="form-group">
											    @php
											        $valid = $errors->has('nit') ? 'is-invalid' : '';
											    @endphp
											    <label class="control-label">Número de identificación tributaria</label>
											    {!! Form::text('nit', $entidad->terceroEntidad->numero_identificacion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria', 'readonly']) !!}
											    @if ($errors->has('nit'))
											        <div class="invalid-feedback">{{ $errors->first('nit') }}</div>
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
									<div class="form-group">
									    @php
									        $valid = $errors->has('actividad_economica') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Actividad económica</label>
									    {!! Form::select('actividad_economica', [], $entidad->terceroEntidad->actividad_economica_id, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
									    @if ($errors->has('actividad_economica'))
									        <div class="invalid-feedback">{{ $errors->first('actividad_economica') }}</div>
									    @endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('fecha_inicio_contabilidad') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Fecha de inicio de contabilidad</label>
									    <div class="input-group">
									        <div class="input-group-prepend">
									            <span class="input-group-text">
									                <i class="fa fa-calendar"></i>
									            </span>
									        </div>
									        {!! Form::text('fecha_inicio_contabilidad', $entidad->fecha_inicio_contabilidad, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									        @if ($errors->has('fecha_inicio_contabilidad'))
									            <div class="invalid-feedback">{{ $errors->first('fecha_inicio_contabilidad') }}</div>
									        @endif
									    </div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
									    <label class="control-label">¿Usa dependencias?</label>
									    <div>
									        @php
									            $valid = $errors->has('usa_dependencia') ? 'is-invalid' : '';
									            $usaDependencia = empty(old('usa_dependencia')) ? $entidad->usa_dependencia : old('usa_dependencia');
									        @endphp
									        <div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary {{ $usaDependencia == 1 ? 'active' : '' }}">
									                {!! Form::radio('usa_dependencia', 1, ($usaDependencia == 1 ? true : false), ['class' => [$valid]]) !!}Sí
									            </label>
									            <label class="btn btn-danger {{ $usaDependencia == 0 ? 'active' : '' }}">
									                {!! Form::radio('usa_dependencia', 0, ($usaDependencia == 0 ? true : false ), ['class' => []]) !!}No
									            </label>
									        </div>
									        @if ($errors->has('usa_dependencia'))
									            <div class="invalid-feedback">{{ $errors->first('usa_dependencia') }}</div>
									        @endif
									    </div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
									    <label class="control-label">¿Usa centros de costo?</label>
									    <div>
									        @php
									            $valid = $errors->has('usa_centro_costos') ? 'is-invalid' : '';
									            $centroCostos = empty(old('usa_centro_costos')) ? $entidad->usa_centro_costos : old('usa_centro_costos');
									        @endphp
									        <div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary {{ $centroCostos == 1 ? 'active' : '' }}">
									                {!! Form::radio('usa_centro_costos', 1, ($centroCostos == 1 ? true : false), ['class' => [$valid]]) !!}Sí
									            </label>
									            <label class="btn btn-danger {{ $centroCostos == 0 ? 'active' : '' }}">
									                {!! Form::radio('usa_centro_costos', 0, ($centroCostos == 0 ? true : false ), ['class' => []]) !!}No
									            </label>
									        </div>
									        @if ($errors->has('usa_centro_costos'))
									            <div class="invalid-feedback">{{ $errors->first('usa_centro_costos') }}</div>
									        @endif
									    </div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="form-group">
									    <label class="control-label">¿Activo?</label>
									    <div>
									        @php
									            $valid = $errors->has('esta_activo') ? 'is-invalid' : '';
									            $estaActivo = empty(old('esta_activo')) ? $entidad->terceroEntidad->esta_activo : old('esta_activo');
									        @endphp
									        <div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary {{ $estaActivo == 1 ? 'active' : '' }}">
									                {!! Form::radio('esta_activo', 1, ($estaActivo == 1 ? true : false), ['class' => [$valid]]) !!}Sí
									            </label>
									            <label class="btn btn-danger {{ $estaActivo == 0 ? 'active' : '' }}">
									                {!! Form::radio('esta_activo', 0, ($estaActivo == 0 ? true : false ), ['class' => []]) !!}No
									            </label>
									        </div>
									        @if ($errors->has('esta_activo'))
									            <div class="invalid-feedback">{{ $errors->first('esta_activo') }}</div>
									        @endif
									    </div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
									    @php
									        $valid = $errors->has('pagina_web') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Página web</label>
									    {!! Form::text('pagina_web', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Página web']) !!}
									    @if ($errors->has('pagina_web'))
									        <div class="invalid-feedback">{{ $errors->first('pagina_web') }}</div>
									    @endif
									</div>
								</div>
							</div>

							<h4>Constitución</h4>
							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('fecha_constitucion') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Fecha de constitución</label>
									    <div class="input-group">
									        <div class="input-group-prepend">
									            <span class="input-group-text">
									                <i class="fa fa-calendar"></i>
									            </span>
									        </div>
									        {!! Form::text('fecha_constitucion', $entidad->terceroEntidad->fecha_constitucion, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
									        @if ($errors->has('fecha_constitucion'))
									            <div class="invalid-feedback">{{ $errors->first('fecha_constitucion') }}</div>
									        @endif
									    </div>
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('numero_matricula') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Número matricula mercantil</label>
									    {!! Form::text('numero_matricula', $entidad->terceroEntidad->numero_matricula, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número matricula mercantil']) !!}
									    @if ($errors->has('numero_matricula'))
									        <div class="invalid-feedback">{{ $errors->first('numero_matricula') }}</div>
									    @endif
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('direccion_notificacion') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Dirección de notificación</label>
									    {!! Form::text('direccion_notificacion', $entidad->terceroEntidad->contactos->count()?$entidad->terceroEntidad->contactos[0]->direccion:null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Dirección de notificación']) !!}
									    @if ($errors->has('direccion_notificacion'))
									        <div class="invalid-feedback">{{ $errors->first('direccion_notificacion') }}</div>
									    @endif
									</div>
								</div>

								<div class="col-md-6">
									<div class="form-group">
									    @php
									        $valid = $errors->has('ciudad_direccion_notificacion') ? 'is-invalid' : '';
									    @endphp
									    <label class="control-label">Ciudad direcciónde notificación</label>
									    {!! Form::select('ciudad_direccion_notificacion', [], ($entidad->terceroEntidad->ciudad_direccion_notificacion ? $entidad->terceroEntidad->ciudad_direccion_notificacion : null), ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
									    @if ($errors->has('ciudad_direccion_notificacion'))
									        <div class="invalid-feedback">{{ $errors->first('ciudad_direccion_notificacion') }}</div>
									    @endif
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar y continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('entidad') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
			</div>
			{!! Form::close() !!}
		</div>

		<div class="container-fluid">
			<ul class="nav nav-tabs" id="myTab" role="tablist">
				<li class="nav-item">
					<a class="nav-link active" id="directivos-tab" data-toggle="tab" href="#directivos" role="tab" aria-controls="directivos" aria-selected="true">Directivos</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="representante_legal-tab" data-toggle="tab" href="#representante_legal" role="tab" aria-controls="representante_legal" aria-selected="false">Representante Legal</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="control_social-tab" data-toggle="tab" href="#control_social" role="tab" aria-controls="control_social" aria-selected="false">Control social</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="comite_cartera-tab" data-toggle="tab" href="#comite_cartera" role="tab" aria-controls="comite_cartera" aria-selected="false">Comité de cartera</a>
				</li>
				<li class="nav-item">
					<a class="nav-link" id="comite_riesgo_liquidez-tab" data-toggle="tab" href="#comite_riesgo_liquidez" role="tab" aria-controls="comite_riesgo_liquidez" aria-selected="false">Comité riesgo de liquidez</a>
				</li>
			</ul>

			<div class="tab-content" id="myTabContent">
				<div class="tab-pane fade show active" id="directivos" role="tabpanel" aria-labelledby="directivos-tab">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'directivo']) !!}
									{!! Form::hidden('directivo_entidad', $entidad->id) !!}
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label class="control-label">Seleccione socio</label>
												{!! Form::select('directivo_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione directivo', 'required']) !!}
											</div>
										</div>
										<div class="col-md-2 col-sm-12">
											<label class="control-label">Calidad de nombramiento</label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary active">
									                {!! Form::radio('directivo_calidad', 'PRINCIPAL', true) !!}Principal
									            </label>
									            <label class="btn btn-primary">
									                {!! Form::radio('directivo_calidad', 'SUPLENTE', false) !!}Suplente
									            </label>
									        </div>
										</div>
										<div class="col-md-3 col-sm-12">
											<label class="control-label">Fecha nombramiento</label>
										    <div class="input-group">
										        <div class="input-group-prepend">
										            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										        </div>
										        {!! Form::text('directivo_fecha_nombramiento', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										    </div>
										</div>
										<div class="col-md-2 col-sm-12">
											<div class="form-group">
												<label class="control-label">Periodos</label>
												{!! Form::select('directivo_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
											</div>
										</div>
										<div class="col-md-1 col-sm-12">
											<div class="form-group">
												<label class="control-label">&nbsp;</label>
												<br>
												{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success btn-block pull-right']) !!}
											</div>
										</div>
									</div>
									{!! Form::close() !!}

									<br>
									<div class="table-responsive">
										<table class="table table-hover table-striped" id="id_directivo">
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
																<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-outline-danger btn-sm">
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
				</div>
				<div class="tab-pane fade" id="representante_legal" role="tabpanel" aria-labelledby="representante_legal-tab">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'id_representantelegal']) !!}
									{!! Form::hidden('legal_entidad', $entidad->id) !!}
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Seleccione tercero
												</label>
												{!! Form::select('legal_tercero', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione representante legal', 'required']) !!}
											</div>
										</div>
										<div class="col-md-2 col-sm-12">
											<label class="control-label">Calidad de nombramiento</label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary active">
									                {!! Form::radio('legal_calidad', 'PRINCIPAL', true) !!}Principal
									            </label>
									            <label class="btn btn-primary">
									                {!! Form::radio('legal_calidad', 'SUPLENTE', false) !!}Suplente
									            </label>
									        </div>
										</div>
										<div class="col-md-3 col-sm-12">
											<label class="control-label">Fecha nombramiento</label>
										    <div class="input-group">
										        <div class="input-group-prepend">
										            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										        </div>
										        {!! Form::text('directivo_fecha_nombramiento', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										    </div>
										</div>
										<div class="col-md-2 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Periodos
												</label>
												{!! Form::select('legal_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
											</div>
										</div>
										<div class="col-md-1 col-sm-12">
											<div class="form-group">
												<label class="control-label">&nbsp;</label>
												<br>
												{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success btn-block pull-right']) !!}
											</div>
										</div>
									</div>
									{!! Form::close() !!}

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
																<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-outline-danger btn-sm">
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
					</div>
				</div>
				<div class="tab-pane fade" id="control_social" role="tabpanel" aria-labelledby="control_social-tab">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'controlsocial']) !!}
									{!! Form::hidden('social_entidad', $entidad->id) !!}
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Seleccione socio
												</label>
												{!! Form::select('social_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
											</div>
										</div>
										<div class="col-md-2 col-sm-12">
											<label class="control-label">Calidad de nombramiento</label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary active">
									                {!! Form::radio('social_calidad', 'PRINCIPAL', true) !!}Principal
									            </label>
									            <label class="btn btn-primary">
									                {!! Form::radio('directivo_calidad', 'SUPLENTE', false) !!}Suplente
									            </label>
									        </div>
										</div>
										<div class="col-md-3 col-sm-12">
											<label class="control-label">Fecha nombramiento</label>
										    <div class="input-group">
										        <div class="input-group-prepend">
										            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										        </div>
										        {!! Form::text('social_fecha_nombramiento', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										    </div>
										</div>
										<div class="col-md-2 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Periodos
												</label>
												{!! Form::select('social_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
											</div>
										</div>
										<div class="col-md-1 col-sm-12">
											<div class="form-group">
												<label class="control-label">&nbsp;</label>
												<br>
												{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success btn-block pull-right']) !!}
											</div>
										</div>
									</div>
									{!! Form::close() !!}

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
																<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-outline-danger btn-sm">
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
				</div>
				<div class="tab-pane fade" id="comite_cartera" role="tabpanel" aria-labelledby="comite_cartera-tab">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'comiteCartera']) !!}
									{!! Form::hidden('comitecartera_entidad', $entidad->id) !!}
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Seleccione socio
												</label>
												{!! Form::select('comitecartera_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
											</div>
										</div>
										<div class="col-md-2 col-sm-12">
											<label class="control-label">Calidad de nombramiento</label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary active">
									                {!! Form::radio('comitecartera_calidad', 'PRINCIPAL', true) !!}Principal
									            </label>
									            <label class="btn btn-primary">
									                {!! Form::radio('comitecartera_calidad', 'SUPLENTE', false) !!}Suplente
									            </label>
									        </div>
										</div>
										<div class="col-md-3 col-sm-12">
											<label class="control-label">Fecha nombramiento</label>
										    <div class="input-group">
										        <div class="input-group-prepend">
										            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										        </div>
										        {!! Form::text('comitecartera_fecha_nombramiento', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										    </div>
										</div>
										<div class="col-md-2 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Periodos
												</label>
												{!! Form::select('comitecartera_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
											</div>
										</div>
										<div class="col-md-1 col-sm-12">
											<div class="form-group">
												<label class="control-label">&nbsp;</label>
												<br>
												{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success btn-block pull-right']) !!}
											</div>
										</div>
									</div>
									{!! Form::close() !!}

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
																<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-outline-danger btn-sm">
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
				</div>
				<div class="tab-pane fade" id="comite_riesgo_liquidez" role="tabpanel" aria-labelledby="comite_riesgo_liquidez-tab">
					<div class="card">
						<div class="card-body">
							<div class="row">
								<div class="col-md-12">
									{!! Form::open(['url' => '', 'method' => 'put', 'name' => 'comiteRiesgoLiquidez']) !!}
									{!! Form::hidden('comiteriesgoliquidez_entidad', $entidad->id) !!}
									<div class="row">
										<div class="col-md-4 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Seleccione socio
												</label>
												{!! Form::select('comiteriesgoliquidez_socio', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione socio', 'required']) !!}
											</div>
										</div>
										<div class="col-md-2 col-sm-12">
											<label class="control-label">Calidad de nombramiento</label>
											<br>
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
									            <label class="btn btn-primary active">
									                {!! Form::radio('comiteriesgoliquidez_calidad', 'PRINCIPAL', true) !!}Principal
									            </label>
									            <label class="btn btn-primary">
									                {!! Form::radio('comiteriesgoliquidez_calidad', 'SUPLENTE', false) !!}Suplente
									            </label>
									        </div>
										</div>
										<div class="col-md-3 col-sm-12">
											<label class="control-label">Fecha nombramiento</label>
										    <div class="input-group">
										        <div class="input-group-prepend">
										            <span class="input-group-text"><i class="fa fa-calendar"></i></span>
										        </div>
										        {!! Form::text('comiteriesgoliquidez_fecha_nombramiento', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'dd/mm/yyyy', 'data-provide' => 'datepicker', 'data-date-format' => 'dd/mm/yyyy', 'data-date-autoclose' => 'true']) !!}
										    </div>
										</div>
										<div class="col-md-2 col-sm-12">
											<div class="form-group">
												<label class="control-label">
													Periodos
												</label>
												{!! Form::select('comiteriesgoliquidez_periodo', [1=>1,2=>2,3=>3,4=>4,5=>5], 1, ['class' => 'form-control select2', 'placeholder' => 'Seleccione periodos', 'required']) !!}
											</div>
										</div>
										<div class="col-md-1 col-sm-12">
											<div class="form-group">
												<label class="control-label">&nbsp;</label>
												<br>
												{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success btn-block pull-right']) !!}
											</div>
										</div>
									</div>
									{!! Form::close() !!}

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
																<a href="#" onclick="javascript:return rowDelete(this);" class="btn btn-outline-danger btn-sm">
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
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Close</button>
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
			digitoVerificacion(this.value, "{{ url('tercero/dv') }}", $(".dv"));
		});

		$("input[name='nit']").keyup(function(){
			digitoVerificacion(this.value, "{{ url('tercero/dv') }}", $(".dv"));
		});

		$("select[name='ciudad_direccion_notificacion']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_direccion_notificacion') | $entidad->terceroEntidad->contactos->count()?$entidad->terceroEntidad->contactos[0]->ciudad_id:null }}"});
		$("select[name='actividad_economica']").selectAjax("{{ url('ciiu') }}", {id:{{ $entidad->terceroEntidad->actividad_economica_id }}});
		$("select[name='directivo_socio']").selectAjax("{{ url('api/socio') }}", {entidad: {{ $entidad->id }}});

		digitoVerificacion($("input[name='nit']").val(), "{{ url('tercero/dv') }}", $(".dv"));

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
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-outline-danger btn-sm'>";
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
				fila += "<td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-outline-danger btn-sm'>";
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
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-outline-danger btn-sm'>";
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
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-outline-danger btn-sm'>";
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
				fila += data.estado + "</td><td><a href='#' onclick='javascript:return rowDelete(this);' class='btn btn-outline-danger btn-sm'>";
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

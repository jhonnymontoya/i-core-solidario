@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Modalidades de créditos
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Modalidades de créditos</li>
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
					<h3 class="card-title">Editar modalidad</h3>
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
								{!! Form::text('codigo', $modalidad->codigo, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<span class="help-block">{{ $errors->first('codigo') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('nombre'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Nombre
								</label>
								{!! Form::text('nombre', $modalidad->nombre, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<span class="help-block">{{ $errors->first('nombre') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('es_exclusivo_de_socios')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('es_exclusivo_de_socios'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									¿Exclusiva para socios?
								</label>
								<br>
								<?php
									$es_exclusivo_de_socios = $modalidad->es_exclusivo_de_socios;
									if(old('es_exclusivo_de_socios') == '0')
									{
										$es_exclusivo_de_socios = false;
									}
									elseif(old('es_exclusivo_de_socios') == '1')
									{
										$es_exclusivo_de_socios = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-primary {{ $es_exclusivo_de_socios ? 'active' : ''}}">
										{!! Form::radio('es_exclusivo_de_socios', '1', $es_exclusivo_de_socios ? true : false) !!}Sí
									</label>
									<label class="btn btn-danger {{ $es_exclusivo_de_socios ? '' : 'active'}}">
										{!! Form::radio('es_exclusivo_de_socios', '0', $es_exclusivo_de_socios? false : true) !!}No
									</label>
								</div>
								@if ($errors->has('es_exclusivo_de_socios'))
									<span class="help-block">{{ $errors->first('es_exclusivo_de_socios') }}</span>
								@endif
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group {{ ($errors->has('esta_activa')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('esta_activa'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Estado
								</label>
								<br>
								<?php
									$esta_activa = $modalidad->esta_activa;
									if(old('esta_activa') == '0')
									{
										$esta_activa = false;
									}
									elseif(old('esta_activa') == '1')
									{
										$esta_activa = true;
									}
								?>
								<div class="btn-group" data-toggle="buttons">
									<label class="btn btn-primary {{ $esta_activa ? 'active' : ''}}">
										{!! Form::radio('esta_activa', '1', $esta_activa ? true : false) !!}Activa
									</label>
									<label class="btn btn-danger {{ $esta_activa ? '' : 'active'}}">
										{!! Form::radio('esta_activa', '0', $esta_activa? false : true) !!}Inactiva
									</label>
								</div>
								@if ($errors->has('esta_activa'))
									<span class="help-block">{{ $errors->first('esta_activa') }}</span>
								@endif
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group {{ ($errors->has('descripcion')?'has-error':'') }}">
								<label class="control-label">
									@if ($errors->has('descripcion'))
										<i class="fa fa-times-circle-o"></i>
									@endif
									Descripción
								</label>
								{!! Form::textarea('descripcion', $modalidad->descripcion, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<span class="help-block">{{ $errors->first('descripcion') }}</span>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-tabs" role="tablist">
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li role="presentation" class="active">
							<a href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li role="presentation">
							<a href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
					</ul>

					<div class="tab-content">
						<div role="tabpanel" class="tab-pane fade in active">
							<br>
							<div class="row">
								<div class="col-md-3">
									<div class="form-group {{ ($errors->has('tipo_garantia')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('tipo_garantia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo garantía
										</label>
										{{--{!! Form::select('tipo_garantia', ['PERSONAL' => 'Personal', 'REAL' => 'Real', 'FONDOGARANTIAS' => 'Fondo garantías'], null, ['class' => 'form-control select2', 'placeholder' => 'Tipo garantía']) !!}--}}
										{!! Form::select('tipo_garantia', ['PERSONAL' => 'Personal'], null, ['class' => 'form-control']) !!}
										@if ($errors->has('tipo_garantia'))
											<span class="help-block">{{ $errors->first('tipo_garantia') }}</span>
										@endif
									</div>
								</div>
								<div class="col-md-5">
									<div class="form-group {{ ($errors->has('garantia')?'has-error':'') }}">
										<label class="control-label">
											@if ($errors->has('garantia'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo garantía
										</label>
										{!! Form::select('garantia', $tiposGarantias, null, ['class' => 'form-control select2']) !!}
									</div>
								</div>
								<div class="col-md-1">
									<div class="form-group">
										<label class="control-label">&nbsp;</label>
										<a class="btn btn-success agregarGarantia">Agregar</a>
									</div>
								</div>
							</div>

							<br>
							<br>
							<div class="row">
								<div class="col-md-12 table-responsive">
									<table class="table table-hover t_garantias">
										<thead>
											<tr>
												<th>Nombre</th>
												<th>Tipo garantía</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach($modalidad->tiposGarantias as $tipoGarantia)
												<tr data-id="{{ $tipoGarantia->id }}">
													<td>{{ $tipoGarantia->nombre }}</td>
													<td>{{ $tipoGarantia->tipo_garantia }}</td>
													<td>
														<a class="btn btn-danger btn-sm eliminar"><i class="fa fa-trash"></i></a>
													</td>
												</tr>
											@endforeach
										</tbody>
									</table>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="card-footer">
					<a href="{{ route('modalidadCreditoEdit', $modalidad) }}" class="btn btn-success">Continuar</a>
					<a href="{{ url('modalidadCredito') }}" class="btn btn-danger pull-right">Cancelar</a>
				</div>
			</div>
		</div>
	</section>
	<form id="adicionGarantia">
		{{ csrf_field() }}
		<input type="hidden" name="garantia" value="">
	</form>
</div>
{{-- Fin de contenido principal de la página --}}

<div class="modal fade" tabindex="-1" role="dialog">
<div class="modal-dialog" role="document">
<div class="modal-content">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
<h4 class="modal-title" style="color: #dd4b39;"><i class="fa fa-times-circle-o"></i> Error</h4>
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
@endsection

@push('style')
<style type="text/css">
	textarea{
		height: 150px !important;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(".select2").select();

		$(".agregarGarantia").click(function(){
			$("#adicionGarantia").find("input[name='garantia']").val($("select[name='garantia']").val());
			$data = $("#adicionGarantia").serialize();
			$("#adicionGarantia").find("input[name='garantia']").val('');
			$.ajax({
				url: "{{ route('modalidadCreditoUpdateGarantias', $modalidad->id) }}",
				type: 'PUT',
				data: $data,
				success: function(result){
					botonEliminar = $("<a></a>").addClass("btn btn-danger btn-sm eliminar");
					botonEliminar.append($("<i></i>").addClass("fa fa-trash"));
					garantia = $("<tr></tr>").append($("<td></td>").text(result.nombre));
					garantia.append($("<td></td>").append(result.tipoGarantia));
					garantia.append($("<td></td>").append(botonEliminar));					
					garantia.attr("data-id", result.id);
					garantia.hide();
					$(".t_garantias").find('tbody').append(garantia);
					garantia.show(200);
				},
				error : function(result){
					errores = result.responseJSON.errors;
					mensajesErrores = '';
					$.each(errores, function( index, value )
					{
						mensajesErrores += value[0] + "<br>";
					});
					$(".modal-body > p").html(mensajesErrores);
					$(".modal").modal("toggle");
				}
			});
		});

		$(".eliminar").click(function(e){
			e.preventDefault();
			var garantia = $(this).parent().parent();
			var id = garantia.data("id");
			var url = "{{ url('modalidadCredito') }}/{{ $modalidad->id }}/{id}".replace("{id}", id);
			garantia.hide(200);
			$.ajax({
				url: url,
				type: 'DELETE',
				data: "_token={{ csrf_token() }}",
				success: function(result){
				},
				error : function(result){
					garantia.show(200);
					$(".modal-body > p").html("Error eliminando el garantia");
					$(".modal").modal("toggle");
				}
			});
		});
	});
</script>
@endpush

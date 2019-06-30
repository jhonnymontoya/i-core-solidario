@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Modalidades de créditos
			<small>Créditos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Créditos</a></li>
			<li class="active">Modalidades de créditos</li>
		</ol>
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
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'cupo'], 'method' => 'put', 'role' => 'form']) !!}
			<div class="col-md-12">
				<div class="box box-{{ $errors->count()?'danger':'success' }}">
					<div class="box-header with-border">
						<h3 class="box-title">Editar modalidad</h3>
					</div>
					<div class="box-body">
						<div class="row">
							<div class="col-md-2">
								<div class="form-group {{ ($errors->has('codigo')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('codigo'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Código
									</label>
									{!! Form::text('codigo', null, ['class' => 'form-control', 'placeholder' => 'Código', 'autocomplete' => 'off', 'readonly']) !!}
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
									{!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Nombre', 'autocomplete' => 'off', 'autofocus']) !!}
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
									{!! Form::textarea('descripcion', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
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
							<li role="presentation" class="active">
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
							<li role="presentation">
								<a href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
							</li>
							<li role="presentation">
								<a href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
							</li>
						</ul>

						<div class="tab-content">
							<div role="tabpanel" class="tab-pane fade in active">
								<br>
								<div class="row form-horizontal">
									<div class="col-md-12">
										<div class="form-group {{ ($errors->has('afecta_cupo')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('afecta_cupo'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Afecta cupo de crédito?
											</label>
											<div class="col-sm-8">
												<?php
													$afectaCupo = $modalidad->afecta_cupo;
													if(old('afecta_cupo') == '0')
													{
														$afectaCupo = false;
													}
													elseif(old('afecta_cupo') == '1')
													{
														$afectaCupo = true;
													}
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $afectaCupo ? 'active' : ''}}">
														{!! Form::radio('afecta_cupo', '1', $afectaCupo ? true : false) !!}Sí
													</label>
													<label class="btn btn-primary {{ $afectaCupo ? '' : 'active'}}">
														{!! Form::radio('afecta_cupo', '0', $afectaCupo? false : true) !!}No
													</label>
												</div>
												@if ($errors->has('afecta_cupo'))
													<span class="help-block">{{ $errors->first('afecta_cupo') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<div class="row form-horizontal">
									<div class="col-md-12">
										<div class="form-group {{ ($errors->has('es_monto_condicionado')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('es_monto_condicionado'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Monto condicionado?
											</label>
											<div class="col-sm-8">
												<?php
													$montoCondicionado = $modalidad->es_monto_condicionado;
													if(old('es_monto_condicionado') == '0')
													{
														$montoCondicionado = false;
													}
													elseif(old('es_monto_condicionado') == '1')
													{
														$montoCondicionado = true;
													}
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $montoCondicionado ? 'active' : ''}}">
														{!! Form::radio('es_monto_condicionado', '1', $montoCondicionado ? true : false) !!}Sí
													</label>
													<label class="btn btn-primary {{ $montoCondicionado ? '' : 'active'}}">
														{!! Form::radio('es_monto_condicionado', '0', $montoCondicionado? false : true) !!}No
													</label>
												</div>
												@if ($errors->has('es_monto_condicionado'))
													<span class="help-block">{{ $errors->first('es_monto_condicionado') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<br>
								<div class="row form-horizontal" id="sinMontoCondicionado">
									<div class="col-sm-6">
										<div class="form-group {{ ($errors->has('monto')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('monto'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Monto máximo
											</label>
											<div class="col-sm-8">
												{!! Form::number('monto', null, ['class' => 'form-control', 'placeholder' => 'Monto', 'autocomplete' => 'off']) !!}
												@if ($errors->has('monto'))
													<span class="help-block">{{ $errors->first('monto') }}</span>
												@endif
											</div>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group {{ ($errors->has('es_monto_cupo')?'has-error':'') }}">
											<label class="col-sm-7 control-label">
												@if ($errors->has('es_monto_cupo'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Monto máximo es igual a cupo disponible?
											</label>
											<div class="col-sm-5">
												<?php
													$esMontoCupo = $modalidad->es_monto_cupo;
													if(old('es_monto_cupo') == '0')
													{
														$esMontoCupo = false;
													}
													elseif(old('es_monto_cupo') == '1')
													{
														$esMontoCupo = true;
													}
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $esMontoCupo ? 'active' : ''}}">
														{!! Form::radio('es_monto_cupo', '1', $esMontoCupo ? true : false) !!}Sí
													</label>
													<label class="btn btn-primary {{ $esMontoCupo ? '' : 'active'}}">
														{!! Form::radio('es_monto_cupo', '0', $esMontoCupo? false : true) !!}No
													</label>
												</div>
												@if ($errors->has('es_monto_cupo'))
													<span class="help-block">{{ $errors->first('es_monto_cupo') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<div id="conMontoCondicionado">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group {{ ($errors->has('condicionPor')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('condicionPor'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Condicionado por
											</label>
											{!! Form::select('condicionPor', ['PLAZO' => 'Plazo', 'ANTIGUEDADENTIDAD' => 'Antigüedad entidad', 'ANTIGUEDADEMPRESA' => 'Antigüedad empresa'], ($modalidad->es_monto_condicionado ? $modalidad->condicionesModalidad->where('tipo_condicion', 'MONTO')->first()->condicionado_por : null), ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Seleccione una opción']) !!}
											@if ($errors->has('condicionPor'))
												<span class="help-block">{{ $errors->first('condicionPor') }}</span>
											@endif
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label class="control-label" style="height: 20px !important;">&nbsp;</label><br>
											{!! Form::submit('Guardar y completar condición', ['class' => 'btn btn-success']) !!}
										</div>
									</div>
								</div>

								<?php
									$hayCondicion = false;
									$condicion = $modalidad->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();
									if($condicion == null)
									{
										$hayCondicion = false;
									}
									else
									{
										$hayCondicion = true;
									}
								?>
								@if($hayCondicion)
									<br>
									<label>Rangos de condición</label>
									<br><br>

									<div class="row">
										<div class="col-md-11">
											<div class="row">
												<?php
													$condicionadoPor = '';
													switch($condicion->condicionado_por)
													{
														case 'ANTIGUEDADENTIDAD':
															$condicionadoPor = 'Antigüedad entidad';
															break;

														case 'ANTIGUEDADEMPRESA':
															$condicionadoPor = 'Antigüedad empresa';
															break;

														case 'PLAZO':
															$condicionadoPor = 'Plazo';
															break;
														
														default:
															$condicionadoPor = '';
															break;
													}

													$tipoCondicion = '';
													switch($condicion->tipo_condicion)
													{
														case 'TASA':
															$tipoCondicion = 'Tasa';
															break;

														case 'PLAZO':
															$tipoCondicion = 'Plazo';
															break;

														case 'MONTO':
															$tipoCondicion = 'Monto';
															break;
														
														default:
															$tipoCondicion = '';
															break;
													}
												?>
												<div class="col-md-3">
													<div class="form-group {{ ($errors->has('condicionadoDesde')?'has-error':'') }}">
														<label class="control-label">
															@if ($errors->has('condicionadoDesde'))
																<i class="fa fa-times-circle-o"></i>
															@endif
															{{ $condicionadoPor }} desde
														</label>
														{!! Form::number('condicionadoDesde', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Desde', 'form' => 'adicionRango']) !!}
														@if ($errors->has('condicionadoDesde'))
															<span class="help-block">{{ $errors->first('condicionadoDesde') }}</span>
														@endif
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group {{ ($errors->has('condicionadoHasta')?'has-error':'') }}">
														<label class="control-label">
															@if ($errors->has('condicionadoHasta'))
																<i class="fa fa-times-circle-o"></i>
															@endif
															{{ $condicionadoPor }} hasta
														</label>
														{!! Form::number('condicionadoHasta', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Hasta', 'form' => 'adicionRango']) !!}
														@if ($errors->has('condicionadoHasta'))
															<span class="help-block">{{ $errors->first('condicionadoHasta') }}</span>
														@endif
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group {{ ($errors->has('tipoCondicionMinimo')?'has-error':'') }}">
														<label class="control-label">
															@if ($errors->has('tipoCondicionMinimo'))
																<i class="fa fa-times-circle-o"></i>
															@endif
															{{ $tipoCondicion }} mínimo
														</label>
														{!! Form::number('tipoCondicionMinimo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Mínimo', 'form' => 'adicionRango']) !!}
														@if ($errors->has('tipoCondicionMinimo'))
															<span class="help-block">{{ $errors->first('tipoCondicionMinimo') }}</span>
														@endif
													</div>
												</div>

												<div class="col-md-3">
													<div class="form-group {{ ($errors->has('tipoCondicionMaximo')?'has-error':'') }}">
														<label class="control-label">
															@if ($errors->has('tipoCondicionMaximo'))
																<i class="fa fa-times-circle-o"></i>
															@endif
															{{ $tipoCondicion }} máximo
														</label>
														{!! Form::number('tipoCondicionMaximo', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Máximo', 'form' => 'adicionRango']) !!}
														@if ($errors->has('tipoCondicionMaximo'))
															<span class="help-block">{{ $errors->first('tipoCondicionMaximo') }}</span>
														@endif
													</div>
												</div>
											</div>
										</div>

										<div class="col-md-1">
											<label>&nbsp;</label><br>
											{!! Form::submit('Agregar', ['class' => 'btn btn-success', 'form' => 'adicionRango']) !!}
										</div>
									</div>
									<br>
									<br>
									<div class="row">
										<div class="col-md-12 table-responsive">
											<table class="table table-hover rangos">
												<thead>
													<tr>
														<th>{{ $condicionadoPor }} desde</th>
														<th>{{ $condicionadoPor }} hasta</th>
														<th>{{ $tipoCondicion }} mínimo</th>
														<th>{{ $tipoCondicion }} máximo</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@foreach($condicion->rangosCondicionesModalidad as $rango)
														<tr data-id="{{ $rango->id }}">
															<td>{{ number_format($rango->condicionado_desde, 0) }}</td>
															<td>{{ number_format($rango->condicionado_hasta, 0) }}</td>
															<td>{{ number_format($rango->tipo_condicion_minimo, 0) }}</td>
															<td>{{ number_format($rango->tipo_condicion_maximo, 0) }}</td>
															<td>
																<a class="btn btn-danger btn-xs eliminar"><i class="fa fa-trash"></i></a>
															</td>
														</tr>
													@endforeach
												</tbody>
											</table>
										</div>
									</div>
								@endif
								</div>
							</div>
						</div>
					</div>
					<div class="box-footer">
						{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
						<a href="{{ url('modalidadCredito') }}" class="btn btn-danger pull-right">Cancelar</a>
					</div>
				</div>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
	<form id="adicionRango">
		{{ csrf_field() }}
		@if(!empty($condicion))
			<input type="hidden" name="modalidad" value="{{ $modalidad->id }}">
			<input type="hidden" name="condicion" value="{{ $condicion->id }}">
		@endif
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
	@if($montoCondicionado)
	#sinMontoCondicionado{
		display: none;
	}
	@else
	#conMontoCondicionado{
		display: none;
	}
	@endif
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){

		$('input[name="es_monto_condicionado"]').change(function(){
			var montoCondicionado = ($(this).val() == '1' ? true : false);
			
			if(montoCondicionado)
			{
				$("#sinMontoCondicionado").hide();
				$("input[name='monto']").val('');
				$("#conMontoCondicionado").show(300);
			}
			else
			{
				$("#sinMontoCondicionado").show(300);
				$("#conMontoCondicionado").hide();
			}
		});

		$('input[name="es_monto_cupo"]').change(function(){
			var esMontoCupo = ($(this).val() == '1' ? true : false);
			
			if(esMontoCupo)
			{
				$('input[name="monto"]').prop("readonly", true);
			}
			else
			{
				$('input[name="monto"]').prop("readonly", false);
			}
		});
		@if($esMontoCupo)
			$('input[name="monto"]').prop("readonly", true);
		@else
			$('input[name="monto"]').prop("readonly", false);
		@endif

		$("#adicionRango").submit(function(e){
			e.preventDefault();
			var data = $(this).serialize();
			$.ajax({
				url: 'updateCupo',
				type: 'PUT',
				data: data,
				success: function(result){
					botonEliminar = $("<a></a>").addClass("btn btn-danger btn-xs eliminar");
					botonEliminar.append($("<i></i>").addClass("fa fa-trash"));
					rango = $("<tr></tr>").append($("<td></td>").text(result.condicionado_desde));
					rango.append($("<td></td>").text(result.condicionado_hasta));
					rango.append($("<td></td>").text(result.tipo_condicion_minimo));
					rango.append($("<td></td>").text(result.tipo_condicion_maximo));
					rango.append($("<td></td>").append(botonEliminar));					
					rango.attr("data-id", result.id);
					rango.hide();
					$(".rangos").find('tbody').append(rango);
					rango.show(200);
					$("#adicionRango")[0].reset();
				},
				error : function(result){
					errores = jQuery.parseJSON(result.responseText);
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
			var rango = $(this).parent().parent();
			var id = rango.data("id");
			var url = "{{ url('modalidadCredito') }}/{id}/cupo".replace("{id}", id);
			rango.hide(200);
			$.ajax({
				url: url,
				type: 'DELETE',
				data: "_token={{ csrf_token() }}",
				success: function(result){
				},
				error : function(result){
					rango.show(200);
					$(".modal-body > p").html("Error eliminando el rango");
					$(".modal").modal("toggle");
				}
			});
		});
	});
</script>
@endpush

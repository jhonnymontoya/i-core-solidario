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
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif

		<div class="container-fluid">
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'cupo'], 'method' => 'put', 'role' => 'form']) !!}
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Editar modalidad</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-2">
							<div class="form-group">
								@php
									$valid = $errors->has('codigo') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Código</label>
								{!! Form::text('codigo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Código', 'readonly']) !!}
								@if ($errors->has('codigo'))
									<div class="invalid-feedback">{{ $errors->first('codigo') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre', 'autofocus']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label">¿Exclusiva para socios?</label>
								<div>
									@php
										$valid = $errors->has('es_exclusivo_de_socios') ? 'is-invalid' : '';
										$exclusivoSocios = empty(old('es_exclusivo_de_socios')) ? $modalidad->es_exclusivo_de_socios : old('es_exclusivo_de_socios');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 1, ($exclusivoSocios ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$exclusivoSocios ? 'active' : '' }}">
											{!! Form::radio('es_exclusivo_de_socios', 0, (!$exclusivoSocios ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('es_exclusivo_de_socios'))
										<div class="invalid-feedback">{{ $errors->first('es_exclusivo_de_socios') }}</div>
									@endif
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Activa?</label>
								<div>
									@php
										$valid = $errors->has('esta_activa') ? 'is-invalid' : '';
										$estaActivo = empty(old('esta_activa')) ? $modalidad->esta_activa : old('esta_activa');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 1, ($estaActivo ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$estaActivo ? 'active' : '' }}">
											{!! Form::radio('esta_activa', 0, (!$estaActivo ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('esta_activa'))
										<div class="invalid-feedback">{{ $errors->first('esta_activa') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								@php
									$valid = $errors->has('descripcion') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Descripción</label>
								{!! Form::textarea('descripcion', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Descripción']) !!}
								@if ($errors->has('descripcion'))
									<div class="invalid-feedback">{{ $errors->first('descripcion') }}</div>
								@endif
							</div>
						</div>
					</div>

					<ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEdit', $modalidad) }}">Plazo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTasa', $modalidad) }}">Tasa</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditGarantias', $modalidad) }}">Garantías</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditTarjeta', $modalidad) }}">Tarjeta</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditConsultaAsociado', $modalidad) }}">Consulta Asociado</a>
						</li>
					</ul>

					<div class="tab-content">
						<div class="tab-pane fade show active">
							<br>
							<div class="row">
								<div class="col-md-12 text-center">
									<div class="form-group">
										<label class="control-label">¿Afecta cupo de crédito?</label>
										<div>
											@php
												$valid = $errors->has('afecta_cupo') ? 'is-invalid' : '';
												$afectaCupo = empty(old('afecta_cupo')) ? $modalidad->afecta_cupo : old('afecta_cupo');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $afectaCupo ? 'active' : '' }}">
													{!! Form::radio('afecta_cupo', 1, ($afectaCupo ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$afectaCupo ? 'active' : '' }}">
													{!! Form::radio('afecta_cupo', 0, (!$afectaCupo ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('afecta_cupo'))
												<div class="invalid-feedback">{{ $errors->first('afecta_cupo') }}</div>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="row">
								<div class="col-md-12 text-center">
									<div class="form-group">
										<label class="control-label">¿Monto condicionado?</label>
										<div>
											@php
												$valid = $errors->has('es_monto_condicionado') ? 'is-invalid' : '';
												$montoCondicionado = empty(old('es_monto_condicionado')) ? $modalidad->es_monto_condicionado : old('es_monto_condicionado');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $montoCondicionado ? 'active' : '' }}">
													{!! Form::radio('es_monto_condicionado', 1, ($montoCondicionado ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$montoCondicionado ? 'active' : '' }}">
													{!! Form::radio('es_monto_condicionado', 0, (!$montoCondicionado ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('es_monto_condicionado'))
												<div class="invalid-feedback">{{ $errors->first('es_monto_condicionado') }}</div>
											@endif
										</div>
									</div>
								</div>
							</div>

							<br>
							<div class="row" id="sinMontoCondicionado">
								<div class="col-sm-6">
									<div class="form-group">
										@php
											$valid = $errors->has('monto') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Monto máximo</label>
										{!! Form::text('monto', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Monto']) !!}
										@if ($errors->has('monto'))
											<div class="invalid-feedback">{{ $errors->first('monto') }}</div>
										@endif
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label class="control-label">¿Monto máximo es igual a cupo disponible?</label>
										<div>
											@php
												$valid = $errors->has('es_monto_cupo') ? 'is-invalid' : '';
												$esMontoCupo = empty(old('es_monto_cupo')) ? $modalidad->es_monto_cupo : old('es_monto_cupo');
											@endphp
											<div class="btn-group btn-group-toggle" data-toggle="buttons">
												<label class="btn btn-primary {{ $esMontoCupo ? 'active' : '' }}">
													{!! Form::radio('es_monto_cupo', 1, ($esMontoCupo ? true : false), ['class' => [$valid]]) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$esMontoCupo ? 'active' : '' }}">
													{!! Form::radio('es_monto_cupo', 0, (!$esMontoCupo ? true : false ), ['class' => [$valid]]) !!}No
												</label>
											</div>
											@if ($errors->has('es_monto_cupo'))
												<div class="invalid-feedback">{{ $errors->first('es_monto_cupo') }}</div>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div id="conMontoCondicionado">
							<div class="row">
								<div class="col-md-4">
									<div class="form-group">
										@php
											$valid = $errors->has('condicionPor') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Condicionado por</label>
										{!! Form::select('condicionPor', ['PLAZO' => 'Plazo', 'ANTIGUEDADENTIDAD' => 'Antigüedad entidad', 'ANTIGUEDADEMPRESA' => 'Antigüedad empresa'], ($modalidad->es_monto_condicionado ? $modalidad->condicionesModalidad->where('tipo_condicion', 'MONTO')->first()->condicionado_por : null), ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Seleccione una opción']) !!}
										@if ($errors->has('condicionPor'))
											<div class="invalid-feedback">{{ $errors->first('condicionPor') }}</div>
										@endif
									</div>
								</div>

								<div class="col-md-4">
									<div class="form-group">
										<label class="control-label">&nbsp;</label><br>
										{!! Form::submit('Guardar y completar condición', ['class' => 'btn btn-outline-success']) !!}
									</div>
								</div>
							</div>

							<?php
								$hayCondicion = false;
								$condicion = $modalidad->condicionesModalidad->where('tipo_condicion', 'MONTO')->first();
								if($condicion == null) {
									$hayCondicion = false;
								}
								else {
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
												switch($condicion->condicionado_por) {
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
												switch($condicion->tipo_condicion) {
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
												<div class="form-group">
													@php
														$valid = $errors->has('condicionadoDesde') ? 'is-invalid' : '';
													@endphp
													<label class="control-label">{{ $condicionadoPor }} desde</label>
													{!! Form::number('condicionadoDesde', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Desde', 'form' => 'adicionRango']) !!}
													@if ($errors->has('condicionadoDesde'))
														<div class="invalid-feedback">{{ $errors->first('condicionadoDesde') }}</div>
													@endif
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													@php
														$valid = $errors->has('condicionadoHasta') ? 'is-invalid' : '';
													@endphp
													<label class="control-label">{{ $condicionadoPor }} hasta</label>
													{!! Form::number('condicionadoHasta', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Hasta', 'form' => 'adicionRango']) !!}
													@if ($errors->has('condicionadoHasta'))
														<div class="invalid-feedback">{{ $errors->first('condicionadoHasta') }}</div>
													@endif
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													@php
														$valid = $errors->has('tipoCondicionMinimo') ? 'is-invalid' : '';
													@endphp
													<label class="control-label">{{ $tipoCondicion }} mínimo</label>
													{!! Form::number('tipoCondicionMinimo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Mínimo', 'form' => 'adicionRango']) !!}
													@if ($errors->has('tipoCondicionMinimo'))
														<div class="invalid-feedback">{{ $errors->first('tipoCondicionMinimo') }}</div>
													@endif
												</div>
											</div>

											<div class="col-md-3">
												<div class="form-group">
													@php
														$valid = $errors->has('tipoCondicionMaximo') ? 'is-invalid' : '';
													@endphp
													<label class="control-label">{{ $tipoCondicion }} máximo</label>
													{!! Form::number('tipoCondicionMaximo', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Máximo', 'form' => 'adicionRango']) !!}
													@if ($errors->has('tipoCondicionMaximo'))
														<div class="invalid-feedback">{{ $errors->first('tipoCondicionMaximo') }}</div>
													@endif
												</div>
											</div>
										</div>
									</div>

									<div class="col-md-1">
										<label>&nbsp;</label><br>
										{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success', 'form' => 'adicionRango']) !!}
									</div>
								</div>
								<br>
								<br>
								<div class="row">
									<div class="col-md-12 table-responsive">
										<table class="table table-striped table-hover rangos">
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
															<a href="#" class="btn btn-outline-danger btn-sm eliminar"><i class="far fa-trash-alt"></i></a>
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
				<div class="card-footer text-right">
					{!! Form::submit('Continuar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('modalidadCredito') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
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
				<h4 class="modal-title" style="color: #dd4b39;"><i class="fa fa-times-circle-o"></i> Error</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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

			if(montoCondicionado) {
				$("#sinMontoCondicionado").hide();
				$("input[name='monto']").val('');
				$("#conMontoCondicionado").show(300);
			}
			else {
				$("#sinMontoCondicionado").show(300);
				$("#conMontoCondicionado").hide();
			}
		});

		$('input[name="es_monto_cupo"]').change(function(){
			var esMontoCupo = ($(this).val() == '1' ? true : false);

			if(esMontoCupo) {
				$('input[name="monto"]').prop("readonly", true);
			}
			else {
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
					botonEliminar = $("<a></a>").addClass("btn btn-outline-danger btn-sm eliminar").attr("href", "#");
					botonEliminar.append($("<i></i>").addClass("far fa-trash-alt"));
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
					$.each(errores.errors, function(index, value) {
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

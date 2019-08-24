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
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'tasa'], 'method' => 'put', 'role' => 'form']) !!}
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
						<li role="presentation" class="active">
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
									<div class="form-group {{ ($errors->has('tipo_tasa')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tipo_tasa'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tipo tasa
										</label>
										<div class="col-sm-8">
											<?php
												$tipo_tasa = empty($modalidad->tipo_tasa) ? 'FIJA' : $modalidad->tipo_tasa;
												$tipo_tasa = empty(old('tipo_tasa')) ? $tipo_tasa : old('tipo_tasa');
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-primary {{ $tipo_tasa == 'FIJA' ? 'active' : '' }}">
													{!! Form::radio('tipo_tasa', 'FIJA', ($tipo_tasa == 'FIJA' ? true : false)) !!}Fija
												</label>
												<label class="btn btn-primary {{ $tipo_tasa == 'VARIABLE' ? 'active' : '' }} disabled">
													{!! Form::radio('tipo_tasa', 'VARIABLE', ($tipo_tasa == 'VARIABLE' ? true : false)) !!}Variable
												</label>
												<label class="btn btn-primary {{ $tipo_tasa == 'SINTASA' ? 'active' : '' }}">
													{!! Form::radio('tipo_tasa', 'SINTASA', ($tipo_tasa == 'SINTASA' ? true : false)) !!}Sin tasa
												</label>
											</div>
											@if ($errors->has('tipo_tasa'))
												<span class="help-block">{{ $errors->first('tipo_tasa') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="row form-horizontal">
								<div class="col-md-3"></div>
								<div class="col-md-3 text-right">
									<div class="form-group {{ ($errors->has('aplica_mora')?'has-error':'') }}">
										<label class="col-sm-7 control-label text-right">
											@if ($errors->has('aplica_mora'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											¿Aplica tasa de mora?
										</label>
										<div class="col-sm-5">
											<?php
												$aplica_mora = $modalidad->aplica_mora;
												if(old('aplica_mora') == '0')
												{
													$aplica_mora = false;
												}
												elseif(old('aplica_mora') == '1')
												{
													$aplica_mora = true;
												}
											?>
											<div class="btn-group" data-toggle="buttons">
												<label class="btn btn-primary {{ $aplica_mora ? 'active' : '' }}">
													{!! Form::radio('aplica_mora', '1', ($aplica_mora ? true : false)) !!}Sí
												</label>
												<label class="btn btn-primary {{ !$aplica_mora ? 'active' : '' }}">
													{!! Form::radio('aplica_mora', '0', (!$aplica_mora ? true : false)) !!}No
												</label>
											</div>
											@if ($errors->has('aplica_mora'))
												<span class="help-block">{{ $errors->first('aplica_mora') }}</span>
											@endif
										</div>
									</div>
								</div>

								<div class="col-md-6 tasaMora">
									<div class="form-group {{ ($errors->has('tasa_mora')?'has-error':'') }}">
										<label class="col-sm-4 control-label">
											@if ($errors->has('tasa_mora'))
												<i class="fa fa-times-circle-o"></i>
											@endif
											Tasa de mora
										</label>
										<div class="col-sm-8">
											{!! Form::text('tasa_mora', null, ['class' => 'form-control', 'placeholder' => 'Tasa de mora', 'autocomplete' => 'off']) !!}
											@if ($errors->has('tasa_mora'))
												<span class="help-block">{{ $errors->first('tasa_mora') }}</span>
											@endif
										</div>
									</div>
								</div>
							</div>

							<div class="tipoTasa" id="tasaFija">
								<div class="row form-horizontal">
									<div class="col-md-12">
										<div class="form-group {{ ($errors->has('pago_interes')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('pago_interes'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Como se pagan los intereses?
											</label>
											<div class="col-sm-8">
												<?php
													$pago_interes = empty($modalidad->pago_interes) ? 'VENCIDOS' : $modalidad->pago_interes;
													$pago_interes = empty(old('pago_interes')) ? $pago_interes : old('pago_interes');
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $pago_interes == 'VENCIDOS' ? 'active' : '' }}">
														{!! Form::radio('pago_interes', 'VENCIDOS', ($pago_interes == 'VENCIDOS' ? true : false)) !!}Vencidos
													</label>
													<label class="btn btn-primary {{ $pago_interes == 'ANTICIPADOS' ? 'active' : '' }}">
														{!! Form::radio('pago_interes', 'ANTICIPADOS', ($pago_interes == 'ANTICIPADOS' ? true : false)) !!}Anticipados
													</label>
												</div>
												@if ($errors->has('pago_interes'))
													<span class="help-block">{{ $errors->first('pago_interes') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<div class="tipoTasa" id="tasaVariable"><h1>Tasa variable</h1></div>

								<br>
								<div class="row form-horizontal">
									<div class="col-md-12">
										<div class="form-group {{ ($errors->has('tasa_condicionada')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('tasa_condicionada'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												¿Tasa condicionada?
											</label>
											<div class="col-sm-8">
												<?php
													$tasaCondicionada = $modalidad->es_tasa_condicionada;
													if(old('tasa_condicionada') == '0')
													{
														$tasaCondicionada = false;
													}
													elseif(old('tasa_condicionada') == '1')
													{
														$tasaCondicionada = true;
													}
												?>
												<div class="btn-group" data-toggle="buttons">
													<label class="btn btn-primary {{ $tasaCondicionada ? 'active' : ''}}">
														{!! Form::radio('tasa_condicionada', '1', $tasaCondicionada ? true : false) !!}Sí
													</label>
													<label class="btn btn-primary {{ $tasaCondicionada ? '' : 'active'}}">
														{!! Form::radio('tasa_condicionada', '0', $tasaCondicionada? false : true) !!}No
													</label>
												</div>
												@if ($errors->has('tasa_condicionada'))
													<span class="help-block">{{ $errors->first('tasa_condicionada') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<br>
								<div class="row form-horizontal" id="sinTasaCondicionada">
									<div class="col-md-12">
										<div class="form-group {{ ($errors->has('tasa')?'has-error':'') }}">
											<label class="col-sm-4 control-label">
												@if ($errors->has('tasa'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Tasa
											</label>
											<div class="col-sm-2">
												{!! Form::number('tasa', $modalidad->tasa == null ? 0 : number_format($modalidad->tasa, 2), ['class' => 'form-control', 'placeholder' => 'Tasa', 'autocomplete' => 'off', 'step' => '0.01']) !!}
												@if ($errors->has('tasa'))
													<span class="help-block">{{ $errors->first('tasa') }}</span>
												@endif
											</div>
										</div>
									</div>
								</div>

								<div id="conTasaCondicionada">
								<div class="row">
									<div class="col-md-4">
										<div class="form-group {{ ($errors->has('condicionPor')?'has-error':'') }}">
											<label class="control-label">
												@if ($errors->has('condicionPor'))
													<i class="fa fa-times-circle-o"></i>
												@endif
												Condicionado por
											</label>
											{!! Form::select('condicionPor', ['PLAZO' => 'Plazo', 'MONTO' => 'Monto', 'ANTIGUEDADENTIDAD' => 'Antigüedad entidad', 'ANTIGUEDADEMPRESA' => 'Antigüedad empresa'], ($modalidad->es_tasa_condicionada ? $modalidad->condicionesModalidad->where('tipo_condicion', 'TASA')->first()->condicionado_por : null), ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Seleccione una opción']) !!}
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
									$condicion = $modalidad->condicionesModalidad->where('tipo_condicion', 'TASA')->first();
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

														case 'MONTO':
															$condicionadoPor = 'Monto';
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
												<div class="col-md-4">
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

												<div class="col-md-4">
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

												<div class="col-md-4">
													<div class="form-group {{ ($errors->has('tipoCondicionTasa')?'has-error':'') }}">
														<label class="control-label">
															@if ($errors->has('tipoCondicionTasa'))
																<i class="fa fa-times-circle-o"></i>
															@endif
															Tasa
														</label>
														{!! Form::number('tipoCondicionTasa', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Tasa', 'form' => 'adicionRango', 'step'=> '0.01']) !!}
														@if ($errors->has('tipoCondicionTasa'))
															<span class="help-block">{{ $errors->first('tipoCondicionTasa') }}</span>
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
														<th>Tasa</th>
														<th></th>
													</tr>
												</thead>
												<tbody>
													@foreach($condicion->rangosCondicionesModalidad as $rango)
														<tr data-id="{{ $rango->id }}">
															<td>{{ number_format($rango->condicionado_desde, 0) }}</td>
															<td>{{ number_format($rango->condicionado_hasta, 0) }}</td>
															<td>{{ number_format($rango->tipo_condicion_minimo, 2) }}</td>
															<td>
																<a class="btn btn-danger btn-sm eliminar"><i class="fa fa-trash"></i></a>
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
				</div>
				<div class="card-footer">
					{!! Form::submit('Continuar', ['class' => 'btn btn-success']) !!}
					<a href="{{ url('modalidadCredito') }}" class="btn btn-danger pull-right">Cancelar</a>
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
	@if($tasaCondicionada)
	#sinTasaCondicionada{
		display: none;
	}
	@else
	#conTasaCondicionada{
		display: none;
	}
	@endif
	.tipoTasa{
		display: none;
	}
	@if(!$aplica_mora)
	.tasaMora{
		display: none;
	}
	@endif
	@if($tipo_tasa == 'FIJA')
		#tasaFija{
			display: block;
		}
	@endif
	@if($tipo_tasa == 'VARIABLE')
		#tasaFija{
			display: block;
		}
		#tasaVariable{
			display: block;
		}
	@endif
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){

		$('input[name="tasa_condicionada"]').change(function(){
			var tasaCondicionada = ($(this).val() == '1' ? true : false);
			
			if(tasaCondicionada)
			{
				$("#sinTasaCondicionada").hide();
				$("#conTasaCondicionada").show(300);
			}
			else
			{
				$("#sinTasaCondicionada").show(300);
				$("#conTasaCondicionada").hide();
			}
		});

		$('input[name="aplica_mora"]').change(function(){
			var aplicaMora = $(this).val() == '1' ? true : false;
			if(aplicaMora)
			{
				$(".tasaMora").show(200);
			}
			else
			{
				$(".tasaMora").hide(200);
			}
		});

		$('input[name="tipo_tasa"]').change(function(){
			var tipoTasa = $(this).val();

			$(".tipoTasa").hide();
			switch(tipoTasa)
			{
				case 'FIJA':{
					$("#tasaVariable").hide(200);
					$("#tasaFija").show(200);
					break;
				}
				case 'VARIABLE':{
					$("#tasaVariable").show();
					$("#tasaFija").show(200);
					break;
				}
				case 'SINTASA':{
					$("#tasaFija").hide(200);
					$("#tasaVariable").hide(200);
					break;
				}
			}
		});

		$("#adicionRango").submit(function(e){
			e.preventDefault();
			var data = $(this).serialize();
			$.ajax({
				url: 'updateTasa',
				type: 'PUT',
				data: data,
				success: function(result){
					botonEliminar = $("<a></a>").addClass("btn btn-danger btn-sm eliminar");
					botonEliminar.append($("<i></i>").addClass("fa fa-trash"));
					rango = $("<tr></tr>").append($("<td></td>").text(result.condicionado_desde));
					rango.append($("<td></td>").text(result.condicionado_hasta));
					rango.append($("<td></td>").text(result.tipo_condicion_tasa));
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
			var url = "{{ url('modalidadCredito') }}/{id}/tasa".replace("{id}", id);
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

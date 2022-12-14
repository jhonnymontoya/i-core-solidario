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
			{!! Form::model($modalidad, ['url' => ['modalidadCredito', $modalidad, 'documentacion'], 'method' => 'put', 'role' => 'form']) !!}
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
							<a class="nav-link" href="{{ route('modalidadCreditoEditCupo', $modalidad) }}">Cupo</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditAmortizacion', $modalidad) }}">Amortización</a>
						</li>
						<li class="nav-item">
							<a class="nav-link" href="{{ route('modalidadCreditoEditCondiciones', $modalidad) }}">Condiciones</a>
						</li>
						<li class="nav-item">
							<a class="nav-link active" href="{{ route('modalidadCreditoEditDocumentacion', $modalidad) }}">Documentación</a>
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
								<div class="col-md-11">
									<div class="row">
										<div class="col-md-8">
											<div class="form-group">
												@php
													$valid = $errors->has('documento') ? 'is-invalid' : '';
												@endphp
												<label class="control-label">Nombre</label>
												{!! Form::text('documento', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre documento', 'form' => 'adicionRango']) !!}
												@if ($errors->has('documento'))
													<div class="invalid-feedback">{{ $errors->first('documento') }}</div>
												@endif
											</div>
										</div>

										<div class="col-md-4">
											<div class="form-group">
												<label class="control-label">¿Status?</label>
												<div>
													@php
														$valid = $errors->has('obligatorio') ? 'is-invalid' : '';
														$status = empty(old('obligatorio')) ? true : old('obligatorio');
													@endphp
													<div class="btn-group btn-group-toggle" data-toggle="buttons">
														<label class="btn btn-primary {{ $status ? 'active' : '' }}">
															{!! Form::radio('obligatorio', 1, ($status ? true : false), ['class' => [$valid], 'form' => 'adicionRango']) !!}Obligatorio
														</label>
														<label class="btn btn-primary {{ !$status ? 'active' : '' }}">
															{!! Form::radio('obligatorio', 0, (!$status ? true : false ), ['class' => [$valid], 'form' => 'adicionRango']) !!}Opcional
														</label>
													</div>
													@if ($errors->has('obligatorio'))
														<div class="invalid-feedback">{{ $errors->first('obligatorio') }}</div>
													@endif
												</div>
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
												<th>Nombre</th>
												<th>Status</th>
												<th></th>
											</tr>
										</thead>
										<tbody>
											@foreach($modalidad->documentacionModalidad as $documento)
												<tr data-id="{{ $documento->id }}">
													<td>{{ $documento->documento }}</td>
													<td>
														<span class="badge badge-pill badge-primary">{{ ($documento->obligatorio ? 'Obligatorio' : 'Opcional') }}</span>
													</td>
													<td>
														<a href="#" class="btn btn-outline-danger btn-sm eliminar"><i class="far fa-trash-alt"></i></a>
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
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#adicionRango").submit(function(e){
			e.preventDefault();
			var data = $(this).serialize();
			$.ajax({
				url: 'updateDocumentacion',
				type: 'PUT',
				data: data,
				success: function(result){
					botonEliminar = $("<a></a>").addClass("btn btn-outline-danger btn-sm eliminar").attr("href", "#");
					botonEliminar.append($("<i></i>").addClass("far fa-trash-alt"));
					documento = $("<tr></tr>").append($("<td></td>").text(result.documento));
					obligatorio = $("<span></span>").addClass("label label-primary");
					obligatorio.text(result.obligatorio);
					documento.append($("<td></td>").append(obligatorio));
					documento.append($("<td></td>").append(botonEliminar));
					documento.attr("data-id", result.id);
					documento.hide();
					$(".rangos").find('tbody').append(documento);
					documento.show(200);
					$("#adicionRango")[0].reset();
				},
				error : function(result){
					errores = jQuery.parseJSON(result.responseText);
					mensajesErrores = '';
					$.each(errores.errors, function( index, value ) {
						mensajesErrores += value[0] + "<br>";
					});
					$(".modal-body > p").html(mensajesErrores);
					$(".modal").modal("toggle");
				}
			});
		});

		$(".eliminar").click(function(e){
			e.preventDefault();
			var documento = $(this).parent().parent();
			var id = documento.data("id");
			var url = "{{ url('modalidadCredito') }}/{id}/documentacion".replace("{id}", id);
			documento.hide(200);
			$.ajax({
				url: url,
				type: 'DELETE',
				data: "_token={{ csrf_token() }}",
				success: function(result){
				},
				error : function(result){
					documento.show(200);
					$(".modal-body > p").html("Error eliminando el documento");
					$(".modal").modal("toggle");
				}
			});
		});
	});
</script>
@endpush

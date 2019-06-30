@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<h1>
			Pagaduría
			<small>Recaudos</small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
			<li><a href="#">Recaudos</a></li>
			<li class="active">Pagaduría</li>
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
		<div class="box box-{{ $errors->count()?'danger':'success' }}">
			{!! Form::model($pagaduria, ['url' => ['pagaduria', $pagaduria], 'method' => 'put', 'role' => 'form', 'name' => 'formularioPagaduria']) !!}
			<div class="box-header with-border">
				<h3 class="box-title">Editar pagaduría</h3>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-4">
						<div class="form-group {{ ($errors->has('nombre')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('nombre'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('nombre', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Nombre de la pagaduría', 'autofocus']) !!}
							@if ($errors->has('nombre'))
								<span class="help-block">{{ $errors->first('nombre') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-2">
						<div class="form-group {{ ($errors->has('periodicidad_pago')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('periodicidad_pago'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Periodicidad de pago
							</label>
							{!! Form::select('periodicidad_pago', $periodicidades, null, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'Periodicidad de pago']) !!}
							@if ($errors->has('periodicidad_pago'))
								<span class="help-block">{{ $errors->first('periodicidad_pago') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('cuenta_por_cobrar_patronal_cuif_id')?'has-error':'') }}">
							<label class="control-label" data-toggle="tooltip" title="Cuenta por cobrar patronal">
								@if ($errors->has('cuenta_por_cobrar_patronal_cuif_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								CXC Patronal <sup>?</sup>
							</label>
							{!! Form::select('cuenta_por_cobrar_patronal_cuif_id', [], null, ['class' => 'form-control select2', 'autocomplete' => 'off', 'placeholder' => 'CXC Patronal']) !!}
							@if ($errors->has('cuenta_por_cobrar_patronal_cuif_id'))
								<span class="help-block">{{ $errors->first('cuenta_por_cobrar_patronal_cuif_id') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('paga_prima')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('paga_prima'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								¿Paga prima?
							</label>
							<br>
							<div class="btn-group" data-toggle="buttons">
								<?php
									$pagaPrima = empty(old('paga_prima')) ? false : old('paga_prima');
									$pagaPrima == '1' ? true : $pagaPrima;
								?>
								<label class="btn btn-primary {{ $pagaPrima ? 'active' : '' }}">
									{!! Form::radio('paga_prima', '1', $pagaPrima) !!}Sí
								</label>
								<label class="btn btn-danger {{ $pagaPrima ? '' : 'active' }}">
									{!! Form::radio('paga_prima', '0', !$pagaPrima) !!}No
								</label>
							</div>
							@if ($errors->has('paga_prima'))
								<span class="help-block">{{ $errors->first('paga_prima') }}</span>
							@endif
						</div>
					</div>
				</div>

				<h4>Empresa</h4>

				<div class="row">
					<div class="col-md-4">
						<div class="row">
							<div class="col-md-9">
								<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
									<label class="control-label">
										@if ($errors->has('nit'))
											<i class="fa fa-times-circle-o"></i>
										@endif
										Número de identificación tributaria
									</label>
									{!! Form::text('nit', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria']) !!}
									@if ($errors->has('nit'))
										<span class="help-block">{{ $errors->first('nit') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group {{ ($errors->has('nit')?'has-error':'') }}">
									<label class="control-label">
										DV
									</label>
									<br>
									<label class="dv">0</label>
								</div>
							</div>
						</div>
					</div>

					<div class="col-md-8">
						<div class="form-group {{ ($errors->has('razonSocial')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('razonSocial'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Razón social
							</label>
							{!! Form::text('razonSocial', null, ['class' => 'form-control', 'placeholder' => 'Razón social']) !!}
							@if ($errors->has('razonSocial'))
								<span class="help-block">{{ $errors->first('razonSocial') }}</span>
							@endif
						</div>
					</div>
				</div>

				<h4>Persona contacto</h4>

				<div class="row">

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('contacto')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('contacto'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Nombre
							</label>
							{!! Form::text('contacto', null, ['class' => 'form-control', 'placeholder' => 'Nombre persona contacto']) !!}
							@if ($errors->has('contacto'))
								<span class="help-block">{{ $errors->first('contacto') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('contacto_email')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('contacto_email'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Email
							</label>
							{!! Form::text('contacto_email', null, ['class' => 'form-control', 'placeholder' => 'Email persona contacto']) !!}
							@if ($errors->has('contacto_email'))
								<span class="help-block">{{ $errors->first('contacto_email') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('contacto_telefono')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('contacto_telefono'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Teléfono
							</label>
							{!! Form::text('contacto_telefono', null, ['class' => 'form-control', 'placeholder' => 'Teléfono persona contacto']) !!}
							@if ($errors->has('contacto_telefono'))
								<span class="help-block">{{ $errors->first('contacto_telefono') }}</span>
							@endif
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('ciudad_id')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('ciudad_id'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Ciudad
							</label>
							{!! Form::select('ciudad_id', [], null, ['class' => 'form-control', 'placeholder' => 'Ciudad persona contacto']) !!}
							@if ($errors->has('ciudad_id'))
								<span class="help-block">{{ $errors->first('ciudad_id') }}</span>
							@endif
						</div>
					</div>
				</div>

				<div class="row">
					
					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('fecha_inicio_recaudo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('fecha_inicio_recaudo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha inicio recaudo
							</label>
							<div>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									<?php
										$propiedadesFechas = [
											'class'			=> 'form-control',
											'placeholder' 	=> 'dd/mm/yyyy',
											'autocomplete' => 'off',
										];
										if($pagaduria->calendarioRecaudos->count())
										{
											array_push($propiedadesFechas, 'readonly');
										}
										else
										{
											$propiedadesFechas['data-provide'] = 'datepicker';
											$propiedadesFechas['data-date-format'] = 'dd/mm/yyyy';
											$propiedadesFechas['data-date-autoclose'] = 'true';
										}
									?>
									{!! Form::text('fecha_inicio_recaudo', null, $propiedadesFechas) !!}
								</div>
								@if ($errors->has('fecha_inicio_recaudo'))
									<span class="help-block">{{ $errors->first('fecha_inicio_recaudo') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('fecha_inicio_reporte')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('fecha_inicio_reporte'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Fecha inicio reportes
							</label>
							<div>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
									{!! Form::text('fecha_inicio_reporte', null, $propiedadesFechas) !!}
								</div>
								@if ($errors->has('fecha_inicio_reporte'))
									<span class="help-block">{{ $errors->first('fecha_inicio_reporte') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group {{ ($errors->has('anioPeriodo')?'has-error':'') }}">
							<label class="control-label">
								@if ($errors->has('anioPeriodo'))
									<i class="fa fa-times-circle-o"></i>
								@endif
								Año programación periodos
							</label>
							<div>
								<div class="input-group">
									<div class="input-group-addon">
										<i class="fa fa-calendar"></i>
									</div>
								{!! Form::text('anioPeriodo', date('Y'), ['class' => 'form-control', 'placeholder' => 'yyyy' ]) !!}
								</div>
								@if ($errors->has('anioPeriodo'))
									<span class="help-block">{{ $errors->first('anioPeriodo') }}</span>
								@endif
							</div>
						</div>
					</div>

					<div class="col-md-3">
						<div class="form-group">
							<label class="control-label">
								&nbsp;
							</label>
							<div>
								<a class="btn btn-primary programar">Programar</a>
							</div>
						</div>
					</div>

				</div>

				@if($pagaduria->calendarioRecaudos->count())
					<br>
					<div class="row">
						<div class="col-md-12">
							<label>Periodos</label>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<div class="col-md-10 col-md-offset-1">
								<table class="table table-hover">
									<thead>
										<tr>
											<th>Número periodo</th>
											<th>Fecha de recaudo</th>
											<th>Fecha de reporte</th>
											<th>Estado</th>
										</tr>
									</thead>

									<tbody>
										<?php
											$calendarios = $pagaduria->calendarioRecaudos()
												->orderBy('estado', 'desc')
												->orderBy('fecha_recaudo', 'asc')
												->get();
										?>
										@foreach ($calendarios as $calendarioRecaudo)
											<tr>
												<td>{{ $calendarioRecaudo->numero_periodo }}</td>
												<td>{{ $calendarioRecaudo->fecha_recaudo }}</td>
												<td>{{ $calendarioRecaudo->fecha_reporte }}</td>
												<td>{{ $calendarioRecaudo->estado }}</td>
											</tr>
										@endforeach
									</tbody>
								</table>
							</div>
						</div>
					</div>
				@endif
				
			</div>
			<div class="box-footer">
				{!! Form::submit('Guardar', ['class' => 'btn btn-success']) !!}
				<a href="{{ url('pagaduria') }}" class="btn btn-danger pull-right">Cancelar</a>
			</div>
			{!! Form::close() !!}
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.dv {
		display: block;
		width: 50%;
		height: 34px;
		padding: 6px 10px;
		font-size: 14px;
		line-height: 1.42857143;
		color: #555;
		background-color: #fff;
		background-image: none;
		border: 1px solid #ccc;
		border-radius: 4px;
		-webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		box-shadow: inset 0 1px 1px rgba(0, 0, 0, .075);
		-webkit-transition: border-color ease-in-out .15s, -webkit-box-shadow ease-in-out .15s;
		-o-transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
		transition: border-color ease-in-out .15s, box-shadow ease-in-out .15s;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){
		$(document).ready(function() {
			$('.table').DataTable( {
				"ordering": false,
			});
		});
		$("select[name='ciudad_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_id') | $pagaduria->ciudad_id }}"});
		$("select[name='cuenta_por_cobrar_patronal_cuif_id']").select2({
			allowClear: true,
			placeholder: "Seleccione una cuenta",
			ajax: {
				url: '{{ url('cuentaContable/getCuentaConParametros') }}',
				dataType: 'json',
				delay: 250,
				data: function (params) {
					return {
						q: params.term,
						page: params.page,
						estado: 1,
						tipoCuenta: 'AUXILIAR',
						modulo: 2
					};
				},
				processResults: function (data, params) {
					params.page = params.page || 1;
					return {
						results: data.items,
						pagination: {
							more: (params.page * 30) < data.total_count
						}
					};
				},
				cache: true
			}
		});

		@if(!empty(old('cuenta_por_cobrar_patronal_cuif_id')) || $pagaduria->cuenta_por_cobrar_patronal_cuif_id)
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta_por_cobrar_patronal_cuif_id') | $pagaduria->cuenta_por_cobrar_patronal_cuif_id }} }}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$('<option>').val(element.id).text(element.text).appendTo($("select[name='cuenta_por_cobrar_patronal_cuif_id']"));
					$("select[name='cuenta_por_cobrar_patronal_cuif_id']").val(element.id).trigger("change");
				}
			});
		@endif

		$("input[name='nit']").on("change", function(event){
			$nit = $(this).val();

			$.ajax({
				url: '{{ url('tercero/getTerceroConParametros') }}',
				dataType: 'json',
				data: {
					tipo: 'JURÍDICA',
					tipoIdentificacion: 'NIT',
					q: $nit,
					tipoCoincidencia: 'COMPLETA'
				}
			}).done(function(data){
				if(data.total_count == 1)
				{
					element = data.items[0];
					$(".dv").html(element.digitoVerificacion);
					$("input[name='razonSocial']").val(element.nombre);
					$("input[name='razonSocial']").prop('readOnly', true);
				}
				else
				{
					$("input[name='razonSocial']").prop('readOnly', false);
					$("input[name='razonSocial']").val('');
					$.ajax({
						url: '{{ url('tercero/dv') }}',
						dataType: 'json',
						data: {
							numeroIdentificacion: $nit,
						}
					}).done(function(data){
						$(".dv").html(data.digitoVerificacion);
					}).fail(function(data){
						$(".dv").html('0');
					});
				}
			});
		});

		$.ajax({
			url: '{{ url('tercero/getTerceroConParametros') }}',
			dataType: 'json',
			data: {
				tipo: 'JURÍDICA',
				tipoIdentificacion: 'NIT',
				id: {{ $pagaduria->tercero_empresa_id }},
				tipoCoincidencia: 'COMPLETA'
			}
		}).done(function(data){
			if(data.total_count == 1)
			{
				element = data.items[0];
				$("input[name='nit']").val(element.numeroIdentificacion);
				$(".dv").html(element.digitoVerificacion);
				$("input[name='razonSocial']").val(element.nombre);
				$("input[name='razonSocial']").prop('readOnly', true);
			}
			else
			{
				$("input[name='razonSocial']").prop('readOnly', false);
				$("input[name='razonSocial']").val('');
				$.ajax({
					url: '{{ url('tercero/dv') }}',
					dataType: 'json',
					data: {
						numeroIdentificacion: $nit,
					}
				}).done(function(data){
					$(".dv").html(data.digitoVerificacion);
				}).fail(function(data){
					$(".dv").html('0');
				});
			}
		});

		$("input[name='anioPeriodo']").datepicker( {
			format: "yyyy",
			viewMode: "years", 
			minViewMode: "years",
			autoclose: "true"
		});

		$(".programar").click(function(event){
			event.preventDefault();
			$("form[name='formularioPagaduria']").append("<input type='hidden' name='programar' value='true'>");
			$("form[name='formularioPagaduria']").submit();
		});

	});
</script>
@endpush

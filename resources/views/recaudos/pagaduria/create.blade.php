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
		<div class="card card-{{ $errors->count()?'danger':'success' }}">
			{!! Form::open(['url' => 'pagaduria', 'method' => 'post', 'role' => 'form']) !!}
			<div class="card-header with-border">
				<h3 class="card-title">Crear nueva pagaduría</h3>
			</div>
			<div class="card-body">
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
				
			</div>
			<div class="card-footer">
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
		$("select[name='ciudad_id']").selectAjax("{{ url('api/ciudad') }}", {id:"{{ old('ciudad_id') }}"});
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
		
		@if(!empty(old('cuenta_por_cobrar_patronal_cuif_id')))
			$.ajax({url: '{{ url('cuentaContable/getCuentaConParametros') }}', dataType: 'json', data: {id: {{ old('cuenta_por_cobrar_patronal_cuif_id') }} }}).done(function(data){
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

	});
</script>
@endpush

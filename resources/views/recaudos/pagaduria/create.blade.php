@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Pagaduría
						<small>Recaudos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Recaudos</a></li>
						<li class="breadcrumb-item active">Pagaduría</li>
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
		<div class="container-fluid">
			<div class="card card-{{ $errors->count()?'danger':'success' }} card-outline">
				{!! Form::open(['url' => 'pagaduria', 'method' => 'post', 'role' => 'form']) !!}
				<div class="card-header with-border">
					<h3 class="card-title">Crear nueva pagaduría</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								@php
									$valid = $errors->has('nombre') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('nombre', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre de la pagaduría']) !!}
								@if ($errors->has('nombre'))
									<div class="invalid-feedback">{{ $errors->first('nombre') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('periodicidad_pago') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Periodicidad de pago</label>
								{!! Form::select('periodicidad_pago', $periodicidades, null, ['class' => [$valid, 'form-control', 'select2'], 'placeholder' => 'Periodicidad de pago']) !!}
								@if ($errors->has('periodicidad_pago'))
									<div class="invalid-feedback">{{ $errors->first('periodicidad_pago') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('cuenta_por_cobrar_patronal_cuif_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label" data-toggle="tooltip" title="Cuenta por cobrar patronal">
									CXC Patronal
								</label>
								{!! Form::select('cuenta_por_cobrar_patronal_cuif_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('cuenta_por_cobrar_patronal_cuif_id'))
									<div class="invalid-feedback">{{ $errors->first('cuenta_por_cobrar_patronal_cuif_id') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">¿Paga prima?</label>
								<div>
									@php
										$valid = $errors->has('paga_prima') ? 'is-invalid' : '';
										$pagaPrima = empty(old('paga_prima')) ? false : old('paga_prima');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $pagaPrima ? 'active' : '' }}">
											{!! Form::radio('paga_prima', 1, ($pagaPrima ? true : false), ['class' => [$valid]]) !!}Sí
										</label>
										<label class="btn btn-danger {{ !$pagaPrima ? 'active' : '' }}">
											{!! Form::radio('paga_prima', 0, (!$pagaPrima ? true : false ), ['class' => [$valid]]) !!}No
										</label>
									</div>
									@if ($errors->has('paga_prima'))
										<div class="invalid-feedback">{{ $errors->first('paga_prima') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					<h4>Empresa</h4>

					<div class="row">
						<div class="col-md-5">
							<div class="row">
								<div class="col-md-9">
									<div class="form-group">
										@php
											$valid = $errors->has('nit') ? 'is-invalid' : '';
										@endphp
										<label class="control-label">Número de identificación tributaria</label>
										{!! Form::text('nit', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Número de identificación tributaria']) !!}
										@if ($errors->has('nit'))
											<div class="invalid-feedback">{{ $errors->first('nit') }}</div>
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

						<div class="col-md-7">
							<div class="form-group">
								@php
									$valid = $errors->has('razonSocial') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Razón social</label>
								{!! Form::text('razonSocial', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Razón social']) !!}
								@if ($errors->has('razonSocial'))
									<div class="invalid-feedback">{{ $errors->first('razonSocial') }}</div>
								@endif
							</div>
						</div>
					</div>

					<h4>Persona contacto</h4>

					<div class="row">

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('contacto') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Nombre</label>
								{!! Form::text('contacto', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Nombre persona contacto']) !!}
								@if ($errors->has('contacto'))
									<div class="invalid-feedback">{{ $errors->first('contacto') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('contacto_email') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Email</label>
								{!! Form::text('contacto_email', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Email persona contacto']) !!}
								@if ($errors->has('contacto_email'))
									<div class="invalid-feedback">{{ $errors->first('contacto_email') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('contacto_telefono') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Teléfono</label>
								{!! Form::text('contacto_telefono', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Teléfono persona contacto']) !!}
								@if ($errors->has('contacto_telefono'))
									<div class="invalid-feedback">{{ $errors->first('contacto_telefono') }}</div>
								@endif
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								@php
									$valid = $errors->has('ciudad_id') ? 'is-invalid' : '';
								@endphp
								<label class="control-label">Ciudad</label>
								{!! Form::select('ciudad_id', [], null, ['class' => [$valid, 'form-control', 'select2']]) !!}
								@if ($errors->has('ciudad_id'))
									<div class="invalid-feedback">{{ $errors->first('ciudad_id') }}</div>
								@endif
							</div>
						</div>
					</div>
					
				</div>
				<div class="card-footer text-right">
					{!! Form::submit('Guardar', ['class' => 'btn btn-outline-success']) !!}
					<a href="{{ url('pagaduria') }}" class="btn btn-outline-danger pull-right">Cancelar</a>
				</div>
				{!! Form::close() !!}
			</div>
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

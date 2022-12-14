@extends('layouts.admin')

@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Parámetros contables
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Parámetros contables</li>
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
		<br>
		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Parámetros contables</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Tipo de cartera</label>
								<div>
									@php
										$valid = $errors->has('tipo_cartera') ? 'is-invalid' : '';
										$tipoCartera = empty(old('tipo_cartera')) ?'CONSUMO' : old('tipo_cartera');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $tipoCartera ? 'active' : '' }}">
											{!! Form::radio('tipo_cartera', 'CONSUMO', ($tipoCartera ? true : false), ['class' => [$valid]]) !!}Consumo
										</label>
										<label class="btn btn-primary {{ !$tipoCartera ? 'active' : '' }} disabled">
											{!! Form::radio('tipo_cartera', 'VIVIENDA', (!$tipoCartera ? true : false ), ['class' => [$valid]]) !!}Vivienda
										</label>
										<label class="btn btn-primary {{ !$tipoCartera ? 'active' : '' }} disabled">
											{!! Form::radio('tipo_cartera', 'COMERCIAL', (!$tipoCartera ? true : false ), ['class' => [$valid]]) !!}Comercial
										</label>
										<label class="btn btn-primary {{ !$tipoCartera ? 'active' : '' }} disabled">
											{!! Form::radio('tipo_cartera', 'MICROCREDITO', (!$tipoCartera ? true : false ), ['class' => [$valid]]) !!}Microcrédido
										</label>
									</div>
									@if ($errors->has('tipo_cartera'))
										<div class="invalid-feedback">{{ $errors->first('tipo_cartera') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">Tipo de garantía</label>
								<div>
									@php
										$valid = $errors->has('tipo_garantia') ? 'is-invalid' : '';
										$tipoGarantia = empty(old('tipo_garantia')) ? 'GARANTIA ADMISIBLE (REAL) CON LIBRANZA' : old('tipo_garantia');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $tipoGarantia ? 'active' : '' }}">
											{!! Form::radio('tipo_garantia', 'GARANTIA ADMISIBLE (REAL) CON LIBRANZA', ($tipoGarantia ? true : false), ['class' => [$valid]]) !!}Real con libranza
										</label>
										<label class="btn btn-primary {{ !$tipoGarantia ? 'active' : '' }}">
											{!! Form::radio('tipo_garantia', 'OTRAS GARANTIAS (PERSONAL) CON LIBRANZA', (!$tipoGarantia ? true : false ), ['class' => [$valid]]) !!}Personal con libranza
										</label>
										<label class="btn btn-primary {{ !$tipoGarantia ? 'active' : '' }}">
											{!! Form::radio('tipo_garantia', 'GARANTIA ADMISIBLE (REAL) SIN LIBRANZA', (!$tipoGarantia ? true : false ), ['class' => [$valid]]) !!}Real sin libranza
										</label>
										<label class="btn btn-primary {{ !$tipoGarantia ? 'active' : '' }}">
											{!! Form::radio('tipo_garantia', 'OTRAS GARANTIAS (PERSONAL) SIN LIBRANZA', (!$tipoGarantia ? true : false ), ['class' => [$valid]]) !!}Personal sin libranza
										</label>
									</div>
									@if ($errors->has('tipo_garantia'))
										<div class="invalid-feedback">{{ $errors->first('tipo_garantia') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12 table-responsive">
							<table class="table table-hover table-striped">
								<thead>
									<th></th>
									<th class="text-center">A</th>
									<th class="text-center">B</th>
									<th class="text-center">C</th>
									<th class="text-center">D</th>
									<th class="text-center">E</th>
								</thead>
								<tbody>
									<tr>
										<th>Capital</th>
										<td class="capital-a text-center"></td>
										<td class="capital-b text-center"></td>
										<td class="capital-c text-center"></td>
										<td class="capital-d text-center"></td>
										<td class="capital-e text-center"></td>
									</tr>
									<tr>
										<th>Intereses ingreso</th>
										<td class="interesIngreso-a text-center"></td>
										<td class="interesIngreso-b text-center"></td>
										<td class="interesIngreso-c text-center"></td>
										<td class="interesIngreso-d text-center"></td>
										<td class="interesIngreso-e text-center"></td>
									</tr>
									<tr>
										<th>Intereses por cobrar</th>
										<td class="interesCobrar-a text-center"></td>
										<td class="interesCobrar-b text-center"></td>
										<td class="interesCobrar-c text-center"></td>
										<td class="interesCobrar-d text-center"></td>
										<td class="interesCobrar-e text-center"></td>
									</tr>
									<tr>
										<th>Intereses anticipados</th>
										<td class="interesAnticipados-a text-center"></td>
										<td class="interesAnticipados-b text-center"></td>
										<td class="interesAnticipados-c text-center"></td>
										<td class="interesAnticipados-d text-center"></td>
										<td class="interesAnticipados-e text-center"></td>
									</tr>
									<tr>
										<th>Deterioro capital</th>
										<td class="deterioroCapital-a text-center"></td>
										<td class="deterioroCapital-b text-center"></td>
										<td class="deterioroCapital-c text-center"></td>
										<td class="deterioroCapital-d text-center"></td>
										<td class="deterioroCapital-e text-center"></td>
									</tr>
									<tr>
										<th>Deterioro intereses</th>
										<td class="deterioroIntereses-a text-center"></td>
										<td class="deterioroIntereses-b text-center"></td>
										<td class="deterioroIntereses-c text-center"></td>
										<td class="deterioroIntereses-d text-center"></td>
										<td class="deterioroIntereses-e text-center"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<div class="card-footer">
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
<style type="text/css">
	.disabled {
		cursor: not-allowed;
	}
</style>
@endpush

@push('scripts')
<script type="text/javascript">
	$(function(){

		$("input[name='tipo_garantia']").change(function(e){
			var garantia = $(this).val();
			cargarCuentas($("input[name='tipo_cartera']:checked").val(), garantia);
		});

		function cargarCuentas(cartera, garantia)
		{
			limpiar();
			$.get({
				url: 'parametrosContablesCreditos/cuentas',
				data: {tipo_cartera: cartera, tipo_garantia: garantia}
			}).done(function(result){
				$(".capital-a").text(result.capital.A.cuenta);
				$(".capital-b").text(result.capital.B.cuenta);
				$(".capital-c").text(result.capital.C.cuenta);
				$(".capital-d").text(result.capital.D.cuenta);
				$(".capital-e").text(result.capital.E.cuenta);

				$(".interesIngreso-a").text(result.interesIngreso.A.cuenta);
				$(".interesIngreso-b").text(result.interesIngreso.B.cuenta);
				$(".interesIngreso-c").text(result.interesIngreso.C.cuenta);
				$(".interesIngreso-d").text(result.interesIngreso.D.cuenta);
				$(".interesIngreso-e").text(result.interesIngreso.E.cuenta);

				$(".interesCobrar-a").text(result.interesCobrar.A.cuenta);
				$(".interesCobrar-b").text(result.interesCobrar.B.cuenta);
				$(".interesCobrar-c").text(result.interesCobrar.C.cuenta);
				$(".interesCobrar-d").text(result.interesCobrar.D.cuenta);
				$(".interesCobrar-e").text(result.interesCobrar.E.cuenta);

				$(".interesAnticipados-a").text(result.interesAnticipados.A.cuenta);
				$(".interesAnticipados-b").text(result.interesAnticipados.B.cuenta);
				$(".interesAnticipados-c").text(result.interesAnticipados.C.cuenta);
				$(".interesAnticipados-d").text(result.interesAnticipados.D.cuenta);
				$(".interesAnticipados-e").text(result.interesAnticipados.E.cuenta);

				$(".deterioroCapital-a").text(result.deterioroCapital.A.cuenta);
				$(".deterioroCapital-b").text(result.deterioroCapital.B.cuenta);
				$(".deterioroCapital-c").text(result.deterioroCapital.C.cuenta);
				$(".deterioroCapital-d").text(result.deterioroCapital.D.cuenta);
				$(".deterioroCapital-e").text(result.deterioroCapital.E.cuenta);

				$(".deterioroIntereses-a").text(result.deterioroIntereses.A.cuenta);
				$(".deterioroIntereses-b").text(result.deterioroIntereses.B.cuenta);
				$(".deterioroIntereses-c").text(result.deterioroIntereses.C.cuenta);
				$(".deterioroIntereses-d").text(result.deterioroIntereses.D.cuenta);
				$(".deterioroIntereses-e").text(result.deterioroIntereses.E.cuenta);
			}).fail(function(result){
			});

		}

		function limpiar()
		{
			$(".capital-a").text('');
			$(".capital-b").text('');
			$(".capital-c").text('');
			$(".capital-d").text('');
			$(".capital-e").text('');
			$(".interesIngreso-a").text('');
			$(".interesIngreso-b").text('');
			$(".interesIngreso-c").text('');
			$(".interesIngreso-d").text('');
			$(".interesIngreso-e").text('');
			$(".interesCobrar-a").text('');
			$(".interesCobrar-b").text('');
			$(".interesCobrar-c").text('');
			$(".interesCobrar-d").text('');
			$(".interesCobrar-e").text('');
			$(".interesAnticipados-a").text('');
			$(".interesAnticipados-b").text('');
			$(".interesAnticipados-c").text('');
			$(".interesAnticipados-d").text('');
			$(".interesAnticipados-e").text('');
			$(".deterioroCapital-a").text('');
			$(".deterioroCapital-b").text('');
			$(".deterioroCapital-c").text('');
			$(".deterioroCapital-d").text('');
			$(".deterioroCapital-e").text('');
			$(".deterioroIntereses-a").text('');
			$(".deterioroIntereses-b").text('');
			$(".deterioroIntereses-c").text('');
			$(".deterioroIntereses-d").text('');
			$(".deterioroIntereses-e").text('');			
		}
		cargarCuentas('CONSUMO', 'GARANTIA ADMISIBLE (REAL) CON LIBRANZA');
	});
</script>
@endpush
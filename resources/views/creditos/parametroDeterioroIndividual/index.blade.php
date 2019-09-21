@extends('layouts.admin')

@section('content')

{{-- Contenido principal de la página --}}
<div class="content-wrapper">
	<section class="content-header">
		<div class="container-fluid">
			<div class="row mb-2">
				<div class="col-6">
					<h1>
						Parámetros Deterioro Individual
						<small>Créditos</small>
					</h1>
				</div>
				<div class="col-6">
					<ol class="breadcrumb float-sm-right">
						<li class="breadcrumb-item"><a href="#"><i class="fa fa-dashboard"></i> Inicio</a></li>
						<li class="breadcrumb-item"><a href="#"> Créditos</a></li>
						<li class="breadcrumb-item active">Parámetros Deterioro Individual</li>
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
					<h3 class="card-title">Parámetros deterioro individual</h3>
				</div>
				<div class="card-body">
					{!! Form::open(['method' => 'post', 'id' => 'idAgregarDeterioro']) !!}
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Tipo de cartera</label>
								<div>
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary active">
											{!! Form::radio('tipo_cartera', 'CONSUMO', true) !!}Consumo
										</label>
										<label class="btn btn-primary disabled">
											{!! Form::radio('tipo_cartera', 'VIVIENDA', false) !!}Vivienda
										</label>
										<label class="btn btn-primary disabled">
											{!! Form::radio('tipo_cartera', 'COMERCIAL', false) !!}Comercial
										</label>
										<label class="btn btn-primary disabled">
											{!! Form::radio('tipo_cartera', 'MICROCREDITO', false) !!}Micricrédito
										</label>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">Clase</label>
								<div>
									@php
										$valid = $errors->has('clase') ? 'is-invalid' : '';
										$clase = empty(old('clase')) ? 'CAPITAL' : old('clase');
									@endphp
									<div class="btn-group btn-group-toggle" data-toggle="buttons">
										<label class="btn btn-primary {{ $clase == 'CAPITAL' ? 'active' : '' }}">
											{!! Form::radio('clase', 'CAPITAL', ($clase == 'CAPITAL' ? true : false), ['class' => [$valid]]) !!}Capital
										</label>
										<label class="btn btn-primary {{ $clase == 'INTERES' ? 'active' : '' }}">
											{!! Form::radio('clase', 'INTERES', ($clase == 'INTERES' ? true : false ), ['class' => [$valid]]) !!}Interes
										</label>
									</div>
									@if ($errors->has('clase'))
										<div class="invalid-feedback">{{ $errors->first('clase') }}</div>
									@endif
								</div>
							</div>
						</div>
					</div>

					{{-- componente de actualización de deterioro --}}
					@component('creditos.parametroDeterioroIndividual.componentes.agregar', ['parametros' => $parametros])
					@endcomponent
					{!! Form::close() !!}
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
		$("input[name='clase']").on('change', function(event){
			var $tipoCartera = $("input[name='tipo_cartera']:checked").val();
			var $clase = $(this).val();
			var $data = "tipo_cartera=" + $tipoCartera + "&clase=" + $clase;
			$.get({
				url: "{{ url('parametrosDeterioroIndividual/parametros') }}",
				dataType: 'json',
				data: $data
			}).done(function(data){
				limpiar();
				$.each(data, function (index, item) {
					agregarParametro(item);
				});
			}).fail(function(data){
			});
		});
		$(".aLimpiar").click(function(event){
			var $parametro = $(this).parent().parent();
			var $id = $parametro.data("id");
			var $data = "_token={{ csrf_token() }}";
			$parametro.hide();
			$.ajax({
				url: '{{ url('parametrosDeterioroIndividual') }}/' + $id,
				type: 'DELETE',
				dataType: 'json',
				data: $data
			}).done(function(data){
				$parametro.remove();
			}).fail(function(data){
				$parametroshow();
			});
		});
	});
	function limpiar() {
		$("#res").empty();
	}
	function agregarParametro(data) {
		var $parametro = $("<tr>");
		$parametro.data("id",  data.id);
		$parametro.append($("<td>").addClass("text-center").text(data.dias_desde));
		$parametro.append($("<td>").addClass("text-center").text(data.dias_hasta));
		$parametro.append($("<td>").addClass("text-center").text(data.deterioro + "%"));
		$parametro.append($("<td>").html(
			"<a href=\"#\" class=\"btn btn-outline-danger btn-sm aLimpiar\"><i class=\"far fa-trash-alt\"></i></a>"
		));
		$("#res").append($parametro);
		$(".aLimpiar").click(function(event){
			event.preventDefault();
			var $parametro = $(this).parent().parent();
			var $id = $parametro.data("id");
			var $data = "_token={{ csrf_token() }}";
			$parametro.hide();
			$.ajax({
				url: '{{ url('parametrosDeterioroIndividual') }}/' + $id,
				type: 'DELETE',
				dataType: 'json',
				data: $data
			}).done(function(data){
				$parametro.remove();
			}).fail(function(data){
				$parametroshow();
			});
		});
	}
</script>
@endpush
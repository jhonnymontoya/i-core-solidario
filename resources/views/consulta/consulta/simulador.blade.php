@extends('layouts.consulta')
@section('content')
{{-- Contenido principal de la página --}}
<div class="content-wrapper">

	<section class="content">
		@if (Session::has('message'))
			<div class="alert alert-success alert-dismissible" data-closable>
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
			   {{ Session::get('message') }}
			</div>
		@endif
		@if (Session::has('error'))
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>{{ Session::get('error') }}</p>
			</div>
		@endif
		@if ($errors->count())
			<div class="alert alert-danger alert-dismissible">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true"><i class="fa fa-times"></i></button>
				<h4>Error!</h4>
				<p>Se ha{{ $errors->count() > 1?'n':'' }} encontrado <strong>{{ $errors->count() }}</strong> error{{ $errors->count() > 1?'es':'' }}, por favor corrigalo{{ $errors->count() > 1?'s':'' }} antes de proseguir.</p>
			</div>
		@endif
		<br>

		<div class="container-fluid">
			<div class="card card-primary card-outline">
				<div class="card-header with-border">
					<h3 class="card-title">Simulador de crédito</h3>
				</div>
				<div class="card-body">
					<div class="row">
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Modalidad de crédito
								</label>
								{!! Form::select('modalidad', $modalidades, null, ['class' => 'form-control select2', 'placeholder' => 'Modalidad de crédito', 'autocomplete' => 'off']) !!}
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">Valor crédito</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">$</span>
									</div>
									{!! Form::text('valorCredito', null, ['class' => 'form-control text-right', 'autofocus', 'data-maskMoney']) !!}
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label class="control-label">
									Plazo
								</label>
								{!! Form::text('plazo', null, ['class' => 'form-control text-right']) !!}
							</div>
						</div>
						<div class="col-md-4">
							<div class="form-group">
								<label class="control-label">
									Periodicidad de pago
								</label>
								{!! Form::select('periodicidad', [], null, ['class' => 'form-control select2', 'placeholder' => 'Seleccione una periodicidad']) !!}
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-md-4">
							<a href="#" class="btn btn-outline-primary simular">Simular</a>
						</div>
					</div>
					<div class="row">
						<div class="col-md-12">
							<h4 id="error" style="display: none; color: #dd4b39;">&nbsp;</h4>
						</div>
					</div>
					<div class="row tableSimulador" style="display:none;">
						<div class="col-md-12">
							<br>
							<div class="row">
								<div class="col-md-2 text-right"><label>Fecha crédito:</label></div>
								<div class="col-md-2 fechaCredito"></div>
								<div class="col-md-2 text-right"><label>Tasa:</label></div>
								<div class="col-md-2 tasa"></div>
							</div>
							<div class="row">
								<div class="col-md-12 col-md-12 table-responsive">
									<table class="table">
										<thead>
											<tr>
												<th>Cuota</th>
												<th>Fecha pago</th>
												<th class="text-center">Capital</th>
												<th class="text-center">Intereses</th>
												<th class="text-center">Total cuota</th>
												<th class="text-center">Nuevo saldo</th>
											</tr>
										</thead>
										<tbody>
										</tbody>
									</table>
								</div>
							</div>	
						</div>
					</div>
				</div>
			</div>
		</div>
	</section>
</div>
{{-- Fin de contenido principal de la página --}}
@endsection

@push('style')
@endpush

@push('scripts')
<script type="text/javascript">
	$("select[name='modalidad']").on("change", function(){
		var $modalidad = $("select[name='modalidad'] option:selected").val();
		$("select[name='periodicidad']").find('option').remove().end().append('<option>Seleccione una periodicidad</option>');
		$.ajax({
			url: '{{ url('consulta/obtenerPeriodicidadesPorModalidad') }}',
			dataType: 'json',
			data: {modalidad: $modalidad}
		}).done(function(data){
			jQuery.each(data, function(index, value){
				$('<option>').val(index).text(value).appendTo($("select[name='periodicidad']"));
			});
		});
	});
	$(".simular").click(function(e){
		e.preventDefault();
		var $data = "socio={{ $socio->id }}&fechaConsulta={{ $fechaConsulta }}&modalidad=";
		$data += $("select[name='modalidad']").val() + "&valorCredito=";
		$data += $("input[name='valorCredito']").maskMoney("cleanvalue") + "&plazo=";
		$data += $("input[name='plazo']").maskMoney("cleanvalue") + "&periodicidad=";
		$data += $("select[name='periodicidad']").val();
		$.ajax({
			url: '{{ url('consulta/simularCredito') }}',
			dataType: 'json',
			data: $data
		}).done(function(data){
			$(".tableSimulador").show();
			$(".tableSimulador").find("tbody").empty();
			$(".tableSimulador").find(".fechaCredito").text(data.fechaCredito);
			$(".tableSimulador").find(".tasa").text(data.tasa + "% M.V.");
			jQuery.each(data.amortizacion, function(index, value){
				$tr = $("<tr>");
				$('<td>').text(value.numeroCuota).appendTo($tr);
				$('<td>').text(value.fechaCuota).appendTo($tr);
				$('<td>').text("$" + value.capital).addClass("text-right").appendTo($tr);
				$('<td>').text("$" + value.intereses).addClass("text-right").appendTo($tr);
				$('<td>').text("$" + value.total).addClass("text-right").appendTo($tr);
				$('<td>').text("$" + value.nuevoSaldoCapital).addClass("text-right").appendTo($tr);
				$tr.appendTo($(".tableSimulador").find("tbody"));
			});
		}).fail(function(data){
			$(".tableSimulador").hide();
			var $error = jQuery.parseJSON(data.responseText);
			$("#error").html($error.error);
			$("#error").show();
			$("#error").fadeOut(5000);
		});
	});
</script>
@endpush
<?php
	$esCondicionado = $cobro->es_condicionado;
	if(strlen(old('es_condicionado')) > 0) {
		$esCondicionado = old('es_condicionado') == "1" ? true : false;
	}
	$esMonto = $cobro->condicion == 'MONTO';
?>
<hr>
<div class="row con-condicion" style="display: {{ $esCondicionado ? 'block' : 'none' }};">
	<div class="col-md-2">
		<div class="form-group">
			@php
				$valid = $errors->has('d') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Desde</label>
			<div class="input-group">
				@if ($esMonto)
					<div class="input-group-prepend">
						<span class="input-group-text">$</span>
					</div>
				@endif
				{!! Form::text('d', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Desde', 'min' => 0, "form" => "adicionarCondicion"]) !!}
				@if ($errors->has('d'))
					<div class="invalid-feedback">{{ $errors->first('d') }}</div>
				@endif
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			@php
				$valid = $errors->has('h') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Hasta</label>
			<div class="input-group">
				<div class="input-group-prepend">
					@if ($esMonto)
						<span class="input-group-text">$</span>
					@endif
				</div>
				{!! Form::text('h', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Hasta', 'min' => 0, "form" => "adicionarCondicion"]) !!}
				@if ($errors->has('h'))
					<div class="invalid-feedback">{{ $errors->first('h') }}</div>
				@endif
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label class="control-label">Base de cobro</label>
			<div>
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-primary active">
						<input type="radio" name="bc" value="VALORCREDITO" checked="checked" form="adicionarCondicion">Crédito
					</label>
					@if ($cobro->efecto != "ADICIONCREDITO")
						<label class="btn btn-primary">
							<input type="radio" name="bc" value="VAORDESCUBIERTO" form="adicionarCondicion">Descubierto
						</label>
					@endif
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label class="control-label">Factor de cálculo</label>
			<div>
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-primary active">
						{!! Form::radio('fc', 'VALORFIJO', true, ["form" => "adicionarCondicion"]) !!}Fijo
					</label>
					<label class="btn btn-primary">
						{!! Form::radio('fc', 'PORCENTAJEBASE', false, ["form" => "adicionarCondicion"]) !!}Porcentaje
					</label>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label class="control-label">Valor</label>
			<div class="input-group">
				<div class="input-group-prepend valor">
					<span class="input-group-text">$</span>
				</div>
				{!! Form::text('v', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor', "form" => "adicionarCondicion"]) !!}
				<div class="input-group-append porcentaje" style="display: none;">
					<span class="input-group-text">%</span>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group">
			<label class="control-label">&nbsp;</label><br>
			{!! Form::submit('Agregar', ['class' => 'btn btn-outline-success', "form" => "adicionarCondicion"]) !!}
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-md-12 text-danger mostrarError" style="height: 30px;"></div>
</div>
<br>
<div class="row">
	<div class="col-md-12 table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th>Desde</th>
					<th>Hasta</th>
					<th>Base de cobro</th>
					<th>Factor de cálculo</th>
					<th>Valor</th>
					<th></th>
				</tr>
			</thead>
			<tbody class="rango">
				@foreach ($cobro->rangoCondiciones as $rango)
					@php
						$data = $rango->paraMostrar();
					@endphp
					<tr data-id="{{ $rango->id }}">						
						<td>{{ $data["condicion_desde"] }}</td>
						<td>{{ $data["condicion_hasta"] }}</td>
						<td>{{ $data["base_cobro"] }}</td>
						<td>{{ $data["factor_calculo"] }}</td>
						<td>{{ $data["valor"] }}</td>
						<td><a class="btn btn-outline-danger btn-sm eliminar"><i class="fa fa-trash"></i></a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>

@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='fc']").change(function(event){
			$valor = $("input[name='v']").parent().parent();
			if($(this).val() == 'VALORFIJO') {
				$valor.parent().parent().find('.valor').show();
				$valor.parent().parent().find('.porcentaje').hide();
			}
			else {
				$valor.parent().parent().find('.porcentaje').show();
				$valor.parent().parent().find('.valor').hide();
			}
		});
		$("#adicionarCondicion").submit(function(event){
			event.preventDefault();
			$data = $(this).serialize();
			$.ajax({
				url: "{{ url('cobrosAdministrativos', $cobro) }}",
				type: 'POST',
				dataType: 'json',
				data: $data
			}).done(function(data){
				$eliminar = $("<a>").addClass("btn").addClass("btn-outline-danger").addClass("btn-sm");
				$eliminar.html("<i class=\"fa fa-trash\"></i>");
				$eliminar.click(function(event){eliminar(this);});
				$rango = $("<tr>")
							.data("id", data.id)
							.append($("<td>").text(data.condicion_desde))
							.append($("<td>").text(data.condicion_hasta))
							.append($("<td>").text(data.base_cobro))
							.append($("<td>").text(data.factor_calculo))
							.append($("<td>").text(data.valor))
							.append($("<td>").append($eliminar));
				$rango.hide();
				$(".rango").append($rango);
				$rango.show(400);
			}).fail(function(data){
				$error = Object.values(data.responseJSON.errors)[0][0];
				$error = $("<p>").text($error);
				$(".mostrarError").empty().append($error);
				$error.click(function(){
					$(this).fadeOut(2000);
				});
			});
		});
		$(".eliminar").click(function(event){
			eliminar(this);
		});
		function eliminar(obj) {
			$rango = $(obj).parent().parent();
			$data = "_token={{ csrf_token() }}&condicion=" + $rango.data("id");
			$rango.hide();
			$.ajax({
				url: "{{ url('cobrosAdministrativos', $cobro) }}",
				type: 'DELETE',
				dataType: 'json',
				data: $data
			}).done(function(data){
				//
			}).fail(function(data){
				$error = Object.values(data.responseJSON.errors)[0][0];
				$error = $("<p>").text($error);
				$(".mostrarError").empty().append($error);
				$error.click(function(){
					$(this).fadeOut(2000);
				});
				$rango.show(500);
			});
		}
	});
</script>
@endpush
<hr>
<br>
<div class="row">
	<div class="col-md-4">
		<div class="form-group">
			@php
				$valid = $errors->has('dias_desde') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Días de mora</label>
			{!! Form::number('dias_desde', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Desde']) !!}
			@if ($errors->has('dias_desde'))
				<div class="invalid-feedback">{{ $errors->first('dias_desde') }}</div>
			@endif
		</div>
	</div>

	<div class="col-md-4">
		<div class="form-group">
			@php
				$valid = $errors->has('dias_hasta') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Días mora hasta</label>
			{!! Form::number('dias_hasta', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Hasta']) !!}
			@if ($errors->has('dias_hasta'))
				<div class="invalid-feedback">{{ $errors->first('dias_hasta') }}</div>
			@endif
		</div>
	</div>

	<div class="col-md-3">
		<div class="form-group">
			@php
				$valid = $errors->has('deterioro') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Porcentaje deterioro</label>
			<div class="input-group">
				{!! Form::number('deterioro', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Porcentaje']) !!}
				<div class="input-group-append">
					<span class="input-group-text">%</span>
				</div>
				@if ($errors->has('deterioro'))
					<div class="invalid-feedback">{{ $errors->first('deterioro') }}</div>
				@endif
			</div>
		</div>
	</div>

	<div class="col-md-1">
		<div class="form-group">
			<label class="control-label">&nbsp;</label>
			<input type="submit" class="btn btn-outline-success" value="Agregar">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-danger">&nbsp;</p>
	</div>
</div>

<div class="row">
	<div class="col-md-12 table-responsive">
		<table class="table table-striped table-hover">
			<thead>
				<tr>
					<th class="text-center">Desde</th>
					<th class="text-center">Hasta</th>
					<th class="text-center">Deterioro</th>
					<th></th>
				</tr>
			</thead>
			<tbody id="res">
				@foreach ($parametros as $parametro)
					<tr data-id="{{ $parametro->id }}">
						<td class="text-center">{{ $parametro->dias_desde }}</td>
						<td class="text-center">{{ $parametro->dias_hasta }}</td>
						<td class="text-center">{{ $parametro->deterioro }}%</td>
						<td><a href="#" class="btn btn-outline-danger btn-sm aLimpiar"><i class="far fa-trash-alt"></i></a></td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#idAgregarDeterioro").submit(function(event){
			event.preventDefault();
			var $data = $(this).serialize();
			$.post({
				url: "{{ url('parametrosDeterioroIndividual') }}",
				dataType: 'json',
				data: $data
			}).done(function(data){
				agregarParametro(data);
			}).fail(function(data){
				var $error = jQuery.parseJSON(data.responseText);
				error($error);
			});
		});
	});
	function error(data) {
		$msg = "";
		$.each(data.errors, function (index, childData) {
			$msg += childData + "<br>";
		});
		$(".text-danger").html($msg);
		$(".text-danger").show();
		$(".text-danger").fadeOut(5000);
	}
</script>
@endpush
<hr>
<br>
<div class="row">
	<div class="col-md-4">
		<div class="form-group {{ ($errors->has('dias_desde')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('dias_desde'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Días mora desde
			</label>
			{!! Form::number('dias_desde', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Desde', 'autofocus']) !!}
			@if ($errors->has('dias_desde'))
				<span class="help-block">{{ $errors->first('dias_desde') }}</span>
			@endif
		</div>
	</div>

	<div class="col-md-4">
		<div class="form-group {{ ($errors->has('dias_hasta')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('dias_hasta'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Días mora hasta
			</label>
			{!! Form::number('dias_hasta', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Hasta']) !!}
			@if ($errors->has('dias_hasta'))
				<span class="help-block">{{ $errors->first('dias_hasta') }}</span>
			@endif
		</div>
	</div>

	<div class="col-md-3">
		<div class="form-group {{ ($errors->has('deterioro')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('deterioro'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Porcentaje deterioro
			</label>
			<div class="input-group">
				{!! Form::number('deterioro', null, ['class' => 'form-control', 'autocomplete' => 'off', 'placeholder' => 'Porcentaje']) !!}
				<span class="input-group-addon">%</span>
			</div>
			@if ($errors->has('deterioro'))
				<span class="help-block">{{ $errors->first('deterioro') }}</span>
			@endif
		</div>
	</div>

	<div class="col-md-1">
		<div class="form-group">
			<label class="control-label">&nbsp;</label>
			<input type="submit" class="btn btn-success" value="Agregar">
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<p class="text-danger">&nbsp;</p>
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 table-responsive">
		<table class="table table-striped">
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
						<td><a class="btn btn-danger btn-xs aLimpiar"><i class="fa fa-trash"></i></a></td>
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
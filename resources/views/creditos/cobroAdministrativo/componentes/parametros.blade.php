<div class="row form-horizontal">
	<div class="col-md-12">
		<div class="form-group {{ ($errors->has('es_condicionado')?'has-error':'') }}">
			<label class="control-label col-md-4">
				@if ($errors->has('es_condicionado'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				¿Cobro condicionado?
			</label>
			<?php
				$esCondicionado = $cobro->es_condicionado;
				if(strlen(old('es_condicionado')) > 0) {
					$esCondicionado = old('es_condicionado') == "1" ? true : false;
				}
			?>
			<col class="col-md-8">
				<div class="btn-group" data-toggle="buttons">
					<label class="btn btn-outline-primary {{ $esCondicionado ? 'active' : ''}}">
						{!! Form::radio('es_condicionado', 1, $esCondicionado ? true : false) !!}Sí
					</label>
					<label class="btn btn-outline-primary {{ $esCondicionado ? '' : 'active'}}">
						{!! Form::radio('es_condicionado', 0, $esCondicionado ? false : true) !!}No
					</label>
					@if ($errors->has('es_condicionado'))
						<br><br>
						<span class="help-block">{{ $errors->first('es_condicionado') }}</span>
					@endif
				</div>
			</col>
		</div>
	</div>
</div>

<div class="row sin-condicion" style="display: {{ $esCondicionado ? 'none' : 'block' }};">
	<div class="col-md-3 col-md-offset-1">
		<div class="form-group {{ ($errors->has('base_cobro')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('base_cobro'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Base de cobro
			</label>
			<br>
			<?php
				$baseCobro = $cobro->base_cobro;
				if(!empty(old('base_cobro'))) {
					$baseCobro = old('base_cobro');
				}
				if($cobro->efecto == "ADICIONCREDITO")$baseCobro = 'VALORCREDITO';
			?>
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-outline-primary {{ $baseCobro == 'VALORCREDITO' ? 'active' : ''}}">
					<input type="radio" name="base_cobro" value="VALORCREDITO" {{ $baseCobro == 'VALORCREDITO' ? 'checked' : '' }}>Valor crédito
				</label>
				@if ($cobro->efecto != "ADICIONCREDITO")
					<label class="btn btn-outline-primary {{ $baseCobro == 'VALORCREDITO' ? '' : 'active'}}">
						<input type="radio" name="base_cobro" value="VAORDESCUBIERTO" {{ $baseCobro == 'VALORCREDITO' ? '' : 'checked' }}>Valor descubierto
					</label>
				@endif
			</div>
			@if ($errors->has('base_cobro'))
				<span class="help-block">{{ $errors->first('base_cobro') }}</span>
			@endif
		</div>
	</div>
	<div class="col-md-3">
		<div class="form-group {{ ($errors->has('factor_calculo')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('factor_calculo'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Factor de cálculo
			</label>
			<br>
			<?php
				$factorCalculo = $cobro->factor_calculo;
				if(!empty(old('factor_calculo'))) {
					$factorCalculo = old('factor_calculo');
				}
			?>
			<div class="btn-group" data-toggle="buttons">
				<label class="btn btn-outline-primary {{ $factorCalculo == 'VALORFIJO' ? 'active' : ''}}">
					{!! Form::radio('factor_calculo', 'VALORFIJO', $factorCalculo == 'VALORFIJO' ? true : false) !!}Valor fijo
				</label>
				<label class="btn btn-outline-primary {{ $factorCalculo == 'VALORFIJO' ? '' : 'active'}}">
					{!! Form::radio('factor_calculo', 'PORCENTAJEBASE', $factorCalculo == 'VALORFIJO' ? false : true) !!}Porcentaje de base
				</label>
			</div>
			@if ($errors->has('factor_calculo'))
				<span class="help-block">{{ $errors->first('factor_calculo') }}</span>
			@endif
		</div>
	</div>
	<div class="col-md-2">
		<div class="form-group {{ ($errors->has('valor')?'has-error':'') }}">
			<label class="control-label">
				@if ($errors->has('valor'))
					<i class="fa fa-times-circle-o"></i>
				@endif
				Valor
			</label>
			<div class="input-group">
				@php
					$valor = is_null(old("valor")) ? $cobro->valor : old("valor");
					$valor = floatval($valor);
				@endphp
				<div class="input-group-addon valor" style="display: {{ $factorCalculo == 'VALORFIJO' ? 'table-cell' : 'none' }};">$</div>
				{!! Form::number('valor', $valor, ['class' => 'form-control text-right', 'placeholder' => 'Valor', 'autocomplete' => 'off', 'step' => '0.01', 'min' => 0]) !!}
				<div class="input-group-addon porcentaje" style="display: {{ $factorCalculo == 'VALORFIJO' ? 'none' : 'table-cell' }};">%</div>
			</div>
			@if ($errors->has('valor'))
				<span class="help-block">{{ $errors->first('valor') }}</span>
			@endif
		</div>
	</div>
</div>
<div class="con-condicion" style="display: {{ $esCondicionado ? 'block' : 'none' }};">
	<div class="row form-horizontal">
		<div class="col-md-5">
			<div class="form-group {{ ($errors->has('condicion')?'has-error':'') }}">
				<label class="control-label col-md-4">
					@if ($errors->has('condicion'))
						<i class="fa fa-times-circle-o"></i>
					@endif
					Condicionado por
				</label>
				<div class="col-md-8">
					{!! Form::select('condicion', ['MONTO' => 'Monto', 'PLAZO' => 'Plazo'], null, ['class' => 'form-control', 'autocomplete' => 'off']) !!}
				</div>
				@if ($errors->has('condicion'))
					<span class="help-block">{{ $errors->first('condicion') }}</span>
				@endif
			</div>
		</div>
		<div class="col-md-3">
			{!! Form::submit('Guardar y completar condición', ['class' => 'btn btn-outline-success']) !!}
		</div>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("input[name='es_condicionado']").change(function(event){
			if($(this).val() == 1) {
				$(".sin-condicion").hide();
				$(".con-condicion").show(100);
			}
			else {
				$(".con-condicion").hide();
				$(".sin-condicion").show(100);
			}
		});
		$("input[name='factor_calculo']").change(function(event){
			$valor = $("input[name='valor']").parent().parent();
			if($(this).val() == 'VALORFIJO') {
				$valor.parent().parent().find('.valor').show();
				$valor.parent().parent().find('.porcentaje').hide();
			}
			else {
				$valor.parent().parent().find('.porcentaje').show();
				$valor.parent().parent().find('.valor').hide();
			}
		});
	});
</script>
@endpush
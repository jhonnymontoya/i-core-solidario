<div class="row form-horizontal">
	<div class="col-md-12">
		<div class="form-group">
			<label class="control-label">¿Cobro condicionado?</label>
			<div>
				@php
					$valid = $errors->has('es_condicionado') ? 'is-invalid' : '';
					$cobroCondicionado = empty(old('es_condicionado')) ? $cobro->es_condicionado : old('es_condicionado');
				@endphp
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-primary {{ $cobroCondicionado ? 'active' : '' }}">
						{!! Form::radio('es_condicionado', 1, ($cobroCondicionado ? true : false), ['class' => [$valid]]) !!}Sí
					</label>
					<label class="btn btn-primary {{ !$cobroCondicionado ? 'active' : '' }}">
						{!! Form::radio('es_condicionado', 0, (!$cobroCondicionado ? true : false ), ['class' => [$valid]]) !!}No
					</label>
				</div>
				@if ($errors->has('es_condicionado'))
					<div class="invalid-feedback">{{ $errors->first('es_condicionado') }}</div>
				@endif
			</div>
		</div>
	</div>
</div>

<div class="row sin-condicion" style="display: {{ $cobroCondicionado ? 'none' : 'block' }};">
	<div class="col-4">
		<div class="form-group">
			<label class="control-label">Base de cobro</label>
			<div>
				@php
					$valid = $errors->has('base_cobro') ? 'is-invalid' : '';
					$baseCobro = empty(old('base_cobro')) ? $cobro->base_cobro : old('base_cobro');
				@endphp
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-primary {{ $baseCobro == 'VALORCREDITO' ? 'active' : '' }}">
						{!! Form::radio('base_cobro', 'VALORCREDITO', ($baseCobro == 'VALORCREDITO' ? true : false), ['class' => [$valid]]) !!}Valor crédito
					</label>
					<label class="btn btn-primary {{ $baseCobro == 'ADICIONCREDITO' ? 'active' : '' }}">
						{!! Form::radio('base_cobro', 'ADICIONCREDITO', ($baseCobro == 'ADICIONCREDITO' ? true : false ), ['class' => [$valid]]) !!}Valor descubierto
					</label>
				</div>
				@if ($errors->has('base_cobro'))
					<div class="invalid-feedback">{{ $errors->first('base_cobro') }}</div>
				@endif
			</div>
		</div>
	</div>
	<div class="col-4">
		<div class="form-group">
			<label class="control-label">Factor de cálculo</label>
			<div>
				@php
					$valid = $errors->has('factor_calculo') ? 'is-invalid' : '';
					$factorCalculo = empty(old('factor_calculo')) ? $cobro->factor_calculo : old('factor_calculo');
				@endphp
				<div class="btn-group btn-group-toggle" data-toggle="buttons">
					<label class="btn btn-primary {{ $factorCalculo == 'VALORFIJO' ? 'active' : '' }}">
						{!! Form::radio('factor_calculo', 'VALORFIJO', ($factorCalculo == 'VALORFIJO' ? true : false), ['class' => [$valid]]) !!}Valor fijo
					</label>
					<label class="btn btn-primary {{ $factorCalculo == 'PORCENTAJEBASE' ? 'active' : '' }}">
						{!! Form::radio('factor_calculo', 'PORCENTAJEBASE', ($factorCalculo == 'PORCENTAJEBASE' ? true : false ), ['class' => [$valid]]) !!}Porcentaje de base
					</label>
				</div>
				@if ($errors->has('factor_calculo'))
					<div class="invalid-feedback">{{ $errors->first('factor_calculo') }}</div>
				@endif
			</div>
		</div>
	</div>
	<div class="col-2">
		<div class="form-group">
			@php
				$valid = $errors->has('valor') ? 'is-invalid' : '';
			@endphp
			<label class="control-label">Valor</label>
			<div class="input-group">
				<div class="input-group-prepend valor" style="display: {{ $factorCalculo == 'VALORFIJO' ? 'table-cell' : 'none' }};">
					<span class="input-group-text">$</span>
				</div>
				@php
					$valor = is_null(old("valor")) ? $cobro->valor : old("valor");
					$valor = floatval($valor);
				@endphp
				{!! Form::text('valor', null, ['class' => [$valid, 'form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor']) !!}
				<div class="input-group-append porcentaje" style="display: {{ $factorCalculo == 'VALORFIJO' ? 'none' : 'table-cell' }};">
					<span class="input-group-text">%</span>
				</div>
				@if ($errors->has('valor'))
					<div class="invalid-feedback">{{ $errors->first('valor') }}</div>
				@endif
			</div>
		</div>
	</div>
</div>
<div class="con-condicion" style="display: {{ $cobroCondicionado ? 'block' : 'none' }};">
	<div class="row form-horizontal">
		<div class="col-md-5">
			<div class="form-group">
				@php
					$valid = $errors->has('condicion') ? 'is-invalid' : '';
				@endphp
				<label class="control-label">Condicionado por</label>
				{!! Form::select('condicion', ['MONTO' => 'Monto', 'PLAZO' => 'Plazo'], null, ['class' => [$valid, 'form-control']]) !!}
				@if ($errors->has('condicion'))
					<div class="invalid-feedback">{{ $errors->first('condicion') }}</div>
				@endif
			</div>
		</div>
		<div class="col-md-3">
			<label class="control-label">&nbsp;</label>
			<br>
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
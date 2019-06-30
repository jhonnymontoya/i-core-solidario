<div class="modal fade" id="mCreditos" tabindex="-1" role="dialog" aria-labelledby="mCreditosLabel">
	<div class="modal-dialog modal-lg" role="document">
		<form id="mfCreditos"><input type="hidden" name="modalidadCreditoId" value="">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hiden="true">&times;</span>
				</button>
				<h3 class="modal-title" id="mCreditosLabel">Recaudo créditos</h3>
			</div>

			<div class="modal-body">
				<div class="row form-horizontal">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label col-md-3">Valor abono</label>
							<div class="input-group col-md-8">
								<div class="input-group-addon">$</div>
								{!! Form::text('valorAbonoCredito', null, ['class' => 'form-control', 'placeholder' => 'Valor abono', 'data-maskMoney']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<a class="btn btn-default cSugerirSaldo">Sugerir saldo: $0</a>
						<a class="btn btn-default cSugerirCuota">Sugerir cuota: $0</a>
						<a class="btn btn-default cSugerirIncluidoRecaudo">Incluido recaudo: $0</a>
					</div>
				</div>
			</div>

			<div class="modal-footer">
        		<input type="submit" class="btn btn-primary" value="Guardar">
				<button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		</form>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#mCreditos").on("show.bs.modal", function(event){
			var info = $(event.relatedTarget).parent().parent();
			var modal = $(this);
			var id = info.data("credito-id");
			var credito = info.data("credito");
			var cuota = parseInt(info.data("cuota"));
			var capital = parseInt(info.data("capital"));
			var intereses = parseInt(info.data("intereses"));
			var seguro = parseInt(info.data("seguro"));
			var recaudo = parseInt(info.data("recaudo"));
			var saldo = capital + (intereses > 0 ? intereses : 0) + (seguro > 0 ? seguro : 0); 
			var valor = 0;
			modal.find("#mCreditosLabel").text(credito);
			modal.find(".cSugerirSaldo").data("saldo", saldo).text("Sugerir Saldo: $" + $().formatoMoneda(saldo));
			modal.find(".cSugerirCuota").data("cuota", cuota).text("Sugerir cuota: $" + $().formatoMoneda(cuota));
			modal.find(".cSugerirIncluidoRecaudo").data("recaudo", recaudo).text("Incluido recaudo: $" + $().formatoMoneda(recaudo));
			data.creditos.forEach(function(item){
				if(item.id == id) valor = item.total;
			});
			$("input[name='modalidadCreditoId']").val(id);
			$("input[name='valorAbonoCredito']").val(valor).maskMoney('mask');
		});
		$("#mCreditos").on('shown.bs.modal', function () {
			$("input[name='valorAbonoCredito']").focus();
		})
		$(".cSugerirCuota").click(function(event){
			var boton = $(this);
			$("input[name='valorAbonoCredito']").val(boton.data("cuota")).maskMoney('mask');
		});
		$(".cSugerirSaldo").click(function(event){
			var boton = $(this);
			$("input[name='valorAbonoCredito']").val(boton.data("saldo")).maskMoney('mask');
		});
		$(".cSugerirIncluidoRecaudo").click(function(event){
			var boton = $(this);
			$("input[name='valorAbonoCredito']").val(boton.data("recaudo")).maskMoney('mask');
		});
		$("#mfCreditos").submit(function(event){
			event.preventDefault();
			var valor = parseInt($("input[name='valorAbonoCredito']").maskMoney("cleanvalue"));
			var creditoId = $("input[name='modalidadCreditoId']").val();
			var credito = $("tr[data-credito-id='" + creditoId + "']");
			var capital = parseInt(credito.data("capital"));
			var intereses = parseInt(credito.data("intereses"));
			var seguro = parseInt(credito.data("seguro"));
			if(isNaN(valor)) valor = 0;
			if(valor != 0) {
				var credito = null;
				data.creditos.forEach(function(item){
					if(item.id == creditoId) credito = item;
				});
				if(credito == null) {
					credito = new Object();
					credito.id = creditoId;
					credito.visible = false;
					data.creditos.push(credito);
				}
				if(valor > (capital + intereses + seguro))valor = capital + intereses + seguro;
				credito.total = valor;
				tmp = 0;
				if(intereses >= valor) {
					tmp = valor;
					valor = 0;
				}
				else {
					tmp = intereses;
					valor -= intereses;
				}
				credito.intereses = tmp;
				tmp = 0;
				if(seguro >= valor) {
					tmp = valor;
					valor = 0;
				}
				else {
					tmp = seguro;
					valor -= seguro;
				}
				credito.seguro = tmp;
				credito.capital = valor;
			}
			$("#mCreditos").modal("hide");
			actualizarCreditos();
		});
	});
</script>
@endpush
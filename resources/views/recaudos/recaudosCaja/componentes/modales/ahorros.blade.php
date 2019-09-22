<div class="modal fade" id="mAhorros" tabindex="-1" role="dialog" aria-labelledby="mAhorrosLabel">
	<div class="modal-dialog modal-lg" role="document">
		<form id="mfAhorros"><input type="hidden" name="modalidadAhorroId" value="">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="mAhorrosLabel">Recaudo</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hiden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label">Valor abono</label>
							<div class="input-group">
								<div class="input-group-prepend">
									<span class="input-group-text">$</span>
								</div>
								{!! Form::text('valorAbonoAhorro', null, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor abono', 'data-maskMoney']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<a href="#" class="btn btn-outline-secondary aSugerirCuota">Sugerir cuota: $0</a>
					</div>
				</div>
			</div>

			<div class="modal-footer">
        		<input type="submit" class="btn btn-outline-primary" value="Guardar">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		</form>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#mAhorros").on("show.bs.modal", function(event){
			var info = $(event.relatedTarget).parent().parent();
			var modal = $(this);
			var id = info.data("modalidad-id");
			var modalidad = info.data("modalidad-nombre");
			var cuota = parseInt(info.data("cuota"));
			var valor = 0;
			modal.find("#mAhorrosLabel").text(modalidad);
			modal.find(".aSugerirCuota").data("cuota", cuota).text("Sugerir cuota: $" + $().formatoMoneda(cuota));
			modal.find(".aSugerirCuota").show();
			data.ahorros.forEach(function(ahorro){
				if(ahorro.modalidad == id) valor = ahorro.valor;
			});
			if(cuota == 0) modal.find(".aSugerirCuota").hide();
			$("input[name='modalidadAhorroId']").val(id);
			$("input[name='valorAbonoAhorro']").val(valor).maskMoney('mask');
		});
		$("#mAhorros").on('shown.bs.modal', function () {
			$("input[name='valorAbonoAhorro']").focus();
		})
		$(".aSugerirCuota").click(function(event){
			event.preventDefault();
			var boton = $(this);
			$("input[name='valorAbonoAhorro']").val(boton.data("cuota")).maskMoney('mask');
		});
		$("#mfAhorros").submit(function(event){
			event.preventDefault();
			var valor = parseInt($("input[name='valorAbonoAhorro']").maskMoney("cleanvalue"));
			var modalidadAhorroId = $("input[name='modalidadAhorroId']").val();
			if(isNaN(valor)) valor = 0;
			if(valor != 0) {
				var ahorro = null;
				data.ahorros.forEach(function(item){
					if(item.modalidad == modalidadAhorroId) ahorro = item;
				});
				if(ahorro == null) {
					ahorro = new Object();
					ahorro.modalidad = modalidadAhorroId;
					ahorro.visible = false;
					data.ahorros.push(ahorro);
				}
				ahorro.valor = valor;
			}
			$("#mAhorros").modal("hide");
			actualizarAhorros();
		});
	});
</script>
@endpush
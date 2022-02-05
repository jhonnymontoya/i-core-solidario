<div class="modal fade" id="mCuotaNoRembolsable" tabindex="-1" role="dialog" aria-labelledby="mCuotaNoReintegrableLabel">
	<div class="modal-dialog modal-lg" role="document">
		<form id="mfCuotaNoReintegrable"><input type="hidden" name="modalidadCNRId" value="">
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="mCuotaNoReintegrableLabel">Recaudo</h3>
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
								{!! Form::text('valorAbonoCuotaNoReintegrable', null, ['class' => ['form-control'], 'autocomplete' => 'off', 'placeholder' => 'Valor abono', 'data-maskMoney']) !!}
							</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 text-center">
						<a href="#" class="btn btn-outline-secondary cnrSugerirCuota">Sugerir cuota: $0</a>
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
		$("#mCuotaNoRembolsable").on("show.bs.modal", function(event){
			var info = $(event.relatedTarget).parent().parent();
			var modal = $(this);
			var id = info.data("modalidad-id");
			var modalidad = info.data("modalidad-nombre");
			var cuota = parseInt(info.data("cuota"));
			var valor = 0;
			modal.find("#mCuotaNoReintegrableLabel").text(modalidad);
			modal.find(".cnrSugerirCuota").data("cuota", cuota).text("Sugerir cuota: $" + $().formatoMoneda(cuota));
			modal.find(".cnrSugerirCuota").show();
			data.cuotasNoRembolsables.forEach(function(cuotaNoRembolsable){
				if(cuotaNoRembolsable.modalidad == id) valor = cuotaNoRembolsable.valor;
			});
			if(cuota == 0) modal.find(".cnrSugerirCuota").hide();
			$("input[name='modalidadCNRId']").val(id);
			$("input[name='valorAbonoCuotaNoReintegrable']").val(valor).maskMoney('mask');
		});
		$("#mCuotaNoRembolsable").on('shown.bs.modal', function () {
			$("input[name='valorAbonoCuotaNoReintegrable']").focus();
		})
		$(".cnrSugerirCuota").click(function(event){
			event.preventDefault();
			var boton = $(this);
			$("input[name='valorAbonoCuotaNoReintegrable']").val(boton.data("cuota")).maskMoney('mask');
		});
		$("#mfCuotaNoReintegrable").submit(function(event){
			event.preventDefault();
			var valor = parseInt($("input[name='valorAbonoCuotaNoReintegrable']").maskMoney("cleanvalue"));
			var modalidadCNRId = $("input[name='modalidadCNRId']").val();
			if(isNaN(valor)) valor = 0;
			if(valor != 0) {
				var cuotaNoReintegrable = null;
				data.cuotasNoRembolsables.forEach(function(item){
					if(item.modalidad == modalidadCNRId) cuotaNoReintegrable = item;
				});
				if(cuotaNoReintegrable == null) {
					cuotaNoReintegrable = new Object();
					cuotaNoReintegrable.modalidad = modalidadCNRId;
					cuotaNoReintegrable.visible = false;
					data.cuotasNoRembolsables.push(cuotaNoReintegrable);
				}
				cuotaNoReintegrable.valor = valor;
			}
			$("#mCuotaNoRembolsable").modal("hide");
			actualizarCuotasNoReembolsables();
		});
	});
</script>
@endpush

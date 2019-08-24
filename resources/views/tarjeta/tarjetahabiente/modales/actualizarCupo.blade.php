<div class="modal fade" id="mActualizarCupo" tabindex="-1" role="dialog" aria-labelledby="mActualizarCupoLabel">
	<div class="modal-dialog modal-lg" role="document">
		{!! Form::open(['url' => '', 'method' => 'put', 'data-maskMoney-removeMask', 'id' => 'mfActualizarCupo']) !!}
		<input type="hidden" name="tarjetaHabiente" value="">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hiden="true">&times;</span>
				</button>
				<h3 class="modal-title" id="mActualizarCupoLabel">Actualizar cupo</h3>
			</div>

			<div class="modal-body">
				<div class="row form-horizontal">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label col-md-3">Cupo</label>
							<div class="input-group col-md-8">
								<div class="input-group-addon">$</div>
								{!! Form::text('cupo', null, ['class' => 'form-control', 'placeholder' => 'Cupo', 'data-maskMoney', 'data-allowzero' => 'true']) !!}
							</div>
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
        		<input type="submit" class="btn btn-outline-primary" value="Guardar">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
		{!! Form::close() !!}
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#mActualizarCupo").on("show.bs.modal", function(event){
			var info = $(event.relatedTarget);
			var modal = $(this);
			var tarjeta = info.data("tarjeta");
			var cupo = info.data("cupo");
			$("input[name='tarjetaHabiente']").val(tarjeta);
			$("input[name='cupo']").val(cupo).maskMoney('mask');
		});
		$("#mActualizarCupo").on('shown.bs.modal', function () {
			$("input[name='cupo']").focus();
		});
		$("#mfActualizarCupo").submit(function(){
			var url = '{{ route('tarjetaHabiente.update.cupo', ':id') }}';
			url = url.replace(':id', $("input[name='tarjetaHabiente']").val());
			$(this).attr("action", url);
		});
	});
</script>
@endpush
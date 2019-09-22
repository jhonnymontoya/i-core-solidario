<div class="modal fade" id="mActualizar" tabindex="-1" role="dialog" aria-labelledby="mLabel">
	<div class="modal-dialog modal-lg" role="document">
		<form id="mfActualizar">
		<input type="hidden" name="tipoCartera" value="">
		<input type="hidden" name="calificacion" value="">
		{{ csrf_field() }}
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="mLabel">Actualizar</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hiden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<strong>Días de mora</strong>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Desde</label>
							{!! Form::number('desde', null, ['class' => 'form-control', 'placeholder' => 'Desde', 'min' => 0]) !!}
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label">Hasta</label>
							{!! Form::number('hasta', null, ['class' => 'form-control', 'placeholder' => 'Hasta', 'min' => 0]) !!}
						</div>
					</div>
				</div>
				<br>
				<div class="row">
					<div class="col-md-12">
						<p class="text-danger"></p>
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
		$("#mActualizar").on("show.bs.modal", function(event){
			var info = $(event.relatedTarget).parent().parent();
			var modal = $(this);
			var calificacion = info.data("calificacion");
			$("input[name='calificacion']").val(calificacion);
			$("input[name='tipoCartera']").val(info.data("tipocartera"));
			$("input[name='desde']").val(info.data("desde"));
			$("input[name='hasta']").val(info.data("hasta"));
			modal.find("#mLabel").text("Actualizar calificación: " + calificacion);
		});
		$("#mActualizar").on('shown.bs.modal', function () {
			$("input[name='desde']").focus();
		})
		$("#mfActualizar").submit(function(event){
			event.preventDefault();
			var $data = $(this).serialize();
			$.post({
				url: "{{ url('parametrosCalificacionCartera') }}",
				dataType: 'json',
				data: $data
			}).done(function(data){
				console.log(data);
				$calificacion = $(".actualizar[data-tipocartera='" + data.tipo_cartera + "'][data-calificacion='" + data.calificacion + "']");
				$($calificacion.find("td")[0]).text(data.dias_desde);
				$($calificacion.find("td")[1]).text(data.dias_hasta);
				$calificacion.data("desde", data.dias_desde);
				$calificacion.data("hasta", data.dias_hasta);
				$("#mActualizar").modal("hide");
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
		$(".text-danger").fadeOut(10000);
	}
</script>
@endpush
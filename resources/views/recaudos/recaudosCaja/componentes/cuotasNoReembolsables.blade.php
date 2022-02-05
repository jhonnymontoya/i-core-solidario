<hr>
@if (!is_null($cuotasNoReembolsables))
	<div class="row">
		<div class="col-md-12 table-responsive">
			<table class="table table-striped table-hover">
				<thead>
					<tr>
						<th>Modalidad</th>
						<th class="text-center">Cuota</th>
						<th>Periodicidad</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($cuotasNoReembolsables as $cuotaNoReembolsable)
						<tr data-modalidad-id="{{ $cuotaNoReembolsable->modalidad_ahorro_id }}" data-modalidad-nombre="{{ $cuotaNoReembolsable->codigo }} - {{ $cuotaNoReembolsable->nombre }}" data-cuota="{{ round($cuotaNoReembolsable->cuota, 0) }}">
							<td>
								<a data-toggle="modal" data-target="#mCuotaNoRembolsable" style="cursor: pointer">{{ $cuotaNoReembolsable->codigo }} - {{ $cuotaNoReembolsable->nombre }}</a>
							</td>
							<td class="text-right">${{ number_format($cuotaNoReembolsable->cuota) }}</td>
							<td>{{ $cuotaNoReembolsable->periodicidad }}</td>
							<td class="text-right">
								<a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#mCuotaNoRembolsable"><i class="fa fa-plus"></i></a>
							</td>
						</tr>
					@endforeach
				</tbody>
			</table>
		</div>
	</div>
@else
	<h5>No hay información a mostrar</h5>
	<br>
@endif
@push('scripts')
<script type="text/javascript">
	function actualizarCuotasNoReembolsables() {
		actualizar();
		data.cuotasNoRembolsables.forEach(function(cuotaNoReembolsable){
			var modalidad = $("tr[data-modalidad-id='" + cuotaNoReembolsable.modalidad + "']");
			var nuevoSaldo = parseInt(modalidad.data("saldo")) + cuotaNoReembolsable.valor;
			if(!cuotaNoReembolsable.visible) {
				var detalle = $("<tr data-abono-cuotanoreintegrable-id='" + cuotaNoReembolsable.modalidad + "'>").addClass("text-danger");
				detalle.append($("<td>").html("<li><em>Abono: $" + $().formatoMoneda(cuotaNoReembolsable.valor) + "</em></li>"));
				detalle.append($("<td colspan='2'>"));
				detalle.append($("<td>").html("<a href='#' class='btn btn-outline-danger btn-sm cnrLimpiar'><i class='far fa-trash-alt'></i></a>"));
				cuotaNoReembolsable.visible = true;
				modalidad.after(detalle);
			}
			else {
				var detalle = $("tr[data-abono-cuotaNoReintegrable-id='" + cuotaNoReembolsable.modalidad + "']");
				var abono = $($(detalle.find("td")[0]).find("em")[0]);
				var total = $($(detalle.find("td")[1]).find("em")[0]);
				abono.text("Abono: $" + $().formatoMoneda(cuotaNoReembolsable.valor));
				total.text("$" + $().formatoMoneda(nuevoSaldo));
			}
		});
		$(".cnrLimpiar").click(function(event){
			event.preventDefault();
			var id = $(this).parent().parent().data("abono-cuotanoreintegrable-id");
			removerAbonoCuotaNoReembolsable(id);
		});
	}
	function removerAbonoCuotaNoReembolsable(id) {
		var abono = $("tr[data-abono-cuotanoreintegrable-id='" + id + "']");
		arr = data.cuotasNoRembolsables;
		data.cuotasNoRembolsables = arr.filter(function(item){
            return item.modalidad != id;
        });
		abono.remove();
		actualizar();
	}
</script>
@endpush

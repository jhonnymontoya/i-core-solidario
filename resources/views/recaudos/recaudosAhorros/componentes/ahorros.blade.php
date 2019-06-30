<hr>
@if (!is_null($ahorros))
	<div class="row">
		<div class="col-md-12 table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Modalidad</th>
						<th class="text-center">Saldo</th>
						<th class="text-center">Cuota</th>
						<th>Periodicidad</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($ahorros as $ahorro)
						<tr data-modalidad-id="{{ $ahorro->modalidad_ahorro_id }}" data-modalidad-nombre="{{ $ahorro->codigo }} - {{ $ahorro->nombre }}" data-cuota="{{ round($ahorro->cuota, 0) }}" data-saldo="{{ round($ahorro->saldo, 0) }}">
							<td>
								<a data-toggle="modal" data-target="#mAhorros" style="cursor: pointer">{{ $ahorro->codigo }} - {{ $ahorro->nombre }}</a>
							</td>
							<td class="text-right">${{ number_format($ahorro->saldo) }}</td>
							<td class="text-right">${{ number_format($ahorro->cuota) }}</td>
							<td>{{ $ahorro->periodicidad }}</td>
							<td>
								<a class="btn btn-info btn-xs" data-toggle="modal" data-target="#mAhorros"><i class="fa fa-plus"></i></a>
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
	function actualizarAhorros() {
		actualizar();
		data.ahorros.forEach(function(ahorro){
			var modalidad = $("tr[data-modalidad-id='" + ahorro.modalidad + "']");
			var nuevoSaldo = parseInt(modalidad.data("saldo")) + ahorro.valor;
			if(!ahorro.visible) {
				var detalle = $("<tr data-abono-ahorro-id='" + ahorro.modalidad + "'>").addClass("text-danger");
				detalle.append($("<td>").html("<li><em>Abono: $" + $().formatoMoneda(ahorro.valor) + "</em></li>"));
				detalle.append($("<td>").html("<em>$" + $().formatoMoneda(nuevoSaldo) + "</em>").addClass("text-right"));
				detalle.append($("<td colspan='2'>"));
				detalle.append($("<td>").html("<a class='btn btn-danger btn-xs aLimpiar'><i class='fa fa-trash'></i></a>"));
				ahorro.visible = true;
				modalidad.after(detalle);
			}
			else {
				var detalle = $("tr[data-abono-ahorro-id='" + ahorro.modalidad + "']");
				var abono = $($(detalle.find("td")[0]).find("em")[0]);
				var total = $($(detalle.find("td")[1]).find("em")[0]);
				abono.text("Abono: $" + $().formatoMoneda(ahorro.valor));
				total.text("$" + $().formatoMoneda(nuevoSaldo));
			}
		});
		$(".aLimpiar").click(function(event){
			var id = $(this).parent().parent().data("abono-ahorro-id");
			removerAbonoAhorro(id);
		});
	}
	function removerAbonoAhorro(id) {
		var abono = $("tr[data-abono-ahorro-id='" + id + "']");
		data.ahorros.forEach(function(ahorro, key){
			if(ahorro.modalidad == id) {
				delete data.ahorros[key];
			}
		});
		abono.remove();
		actualizar();
	}
</script>
@endpush
<hr>
@if (!is_null($creditos) && $creditos->count())
	<div class="row">
		<div class="col-md-12 table-responsive">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Obligación</th>
						<th class="text-center">Capital</th>
						<th class="text-center">Intereses</th>
						<th class="text-center">Seguros</th>
						<th class="text-center">Días vencidos</th>
						<th class="text-center">Capital vencido</th>
						<th class="text-center">Cuota</th>
						<th class="text-center"></th>
					</tr>
				</thead>
				<tbody>
					@foreach ($creditos as $credito)
						@php
							$capital = $credito->saldoObligacion($fecha);
							$intereses = $credito->saldoInteresObligacion($fecha);
							$seguro = $credito->saldoSeguroObligacion($fecha);
						@endphp
						<tr data-credito-id="{{ $credito->id }}" data-credito="{{ $credito->numero_obligacion }} - {{ $credito->modalidadCredito->nombre }}" data-cuota="{{ round($credito->valor_cuota, 0) }}" data-capital="{{ round($capital, 0) }}" data-intereses="{{ round($intereses, 0) }}" data-seguro="{{ round($seguro, 0) }}" data-recaudo="{{ round($credito->valorIncluidoRecaudo, 0) }}">
							<td>
								<a data-toggle="modal" data-target="#mCreditos" style="cursor: pointer">{{ $credito->numero_obligacion }} - {{ $credito->modalidadCredito->nombre }}</a>
							</td>
							<td class="text-right">${{ number_format($capital) }}</td>
							<td class="text-right">${{ number_format($intereses) }}</td>
							<td class="text-right">${{ number_format($seguro) }}</td>
							<td class="text-right">0</td>
							<td class="text-right">$0</td>
							<td class="text-right">${{ number_format($credito->valor_cuota) }}</td>
							<td class="text-right">
								<a href="#" class="btn btn-outline-info btn-sm" data-toggle="modal" data-target="#mCreditos"><i class="fa fa-plus"></i></a>
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
	function actualizarCreditos() {
		actualizar();
		data.creditos.forEach(function(credito){
			var filaCredito = $("tr[data-credito-id='" + credito.id + "']");
			if(!credito.visible) {
				var detalle = $("<tr data-abono-credito-id='" + credito.id + "'>").addClass("text-danger");
				detalle.append($("<td>").html("<li><em>Abono: $" + $().formatoMoneda(credito.total) + "</em></li>"));
				detalle.append($("<td>").html("<em>$" + $().formatoMoneda(credito.capital) + "</em>").addClass("text-right"));
				detalle.append($("<td>").html("<em>$" + $().formatoMoneda(credito.intereses) + "</em>").addClass("text-right"));
				detalle.append($("<td>").html("<em>$" + $().formatoMoneda(credito.seguro) + "</em>").addClass("text-right"));
				detalle.append($("<td colspan='3'>"));
				detalle.append($("<td>").html("<a href='#' class='btn btn-outline-danger btn-sm cLimpiar'><i class='far fa-trash-alt'></i></a>"));
				credito.visible = true;
				filaCredito.after(detalle);
			}
			else {
				var detalle = $("tr[data-abono-credito-id='" + credito.id + "']");
				var abono = $($(detalle.find("td")[0]).find("em")[0]);
				var capital = $($(detalle.find("td")[1]).find("em")[0]);
				var intereses = $($(detalle.find("td")[2]).find("em")[0]);
				var seguro = $($(detalle.find("td")[3]).find("em")[0]);
				abono.text("Abono: $" + $().formatoMoneda(credito.total));
				capital.text("$" + $().formatoMoneda(credito.capital));
				intereses.text("$" + $().formatoMoneda(credito.intereses));
				seguro.text("$" + $().formatoMoneda(credito.seguro));
			}
		});
		$(".cLimpiar").click(function(event){
			event.preventDefault();
			var id = $(this).parent().parent().data("abono-credito-id");
			removerAbonoCredito(id);
		});
	}
	function removerAbonoCredito(id) {
		var abono = $("tr[data-abono-credito-id='" + id + "']");
		data.creditos.forEach(function(credito, key){
			if(credito.id == id) {
				delete data.creditos[key];
			}
		});
		abono.remove();
		actualizar();
	}
</script>
@endpush
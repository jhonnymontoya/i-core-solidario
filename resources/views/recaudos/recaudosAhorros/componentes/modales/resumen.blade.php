<div class="modal fade" id="mResumen" tabindex="-1" role="dialog" aria-labelledby="mResumenLabel">
	<div class="modal-dialog modal-lg" role="document">
		{!! Form::open(['url' => 'recaudosAhorros/create', 'method' => 'post', 'id' => 'mfRecaudo', 'role' => 'form']) !!}
		{!! Form::hidden('socio', optional($socio)->id) !!}
		{!! Form::hidden('modalidad', optional($modalidad)->id) !!}
		{!! Form::hidden('fecha', $fecha) !!}
		{!! Form::hidden('data', "") !!}
		<div class="modal-content">
			<div class="modal-header">
				<h3 class="modal-title" id="mResumenLabel">Resumen de abono</h3>
				<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
					<span aria-hiden="true">&times;</span>
				</button>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						<div class="alert alert-warning">
							<h4>
								<i class="fa fa-exclamation-triangle"></i>&nbsp;Alerta!
							</h4>
							Confirme los datos antes de procesar el abono
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-8">
						<strong>{{ $tercero->nombre_completo }}</strong>
						@if(!is_null($socio) && $socio->estado != 'ACTIVO')
							<span class="badge badge-pill badge-warning">SOCIO NO ACTIVO</span>
						@endif
						<br>
						Fecha recaudo: {{ $fecha }}
						@if ($modalidad)
							<br><br>
							{{ $modalidad->codigo }} - {{ $modalidad->nombre }}
							<br>
							<strong>Saldo:</strong> ${{ number_format($modalidad->saldo) }}
						@endif
						<br>
						<strong>GMF:</strong> <span class="gmf">$0</span>
					</div>
					<div class="col-md-4 col-sm-12">
						<h3 class="text-primary pull-right mTotal">Total: $0</h3>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<h3>Ahorros</h3>
					</div>
				</div>
				<div class="row">
					<div class="col-md-4">
						<dl>
							<dt>Total abonos</dt>
							<dd id="mTotalAbonoAhorros">$0</dd>
						</dl>
					</div>
					<div class="col-md-4">
						<dl>
							<dt>Nuevo saldo</dt>
							<dd id="mNuevoSaldoAhorros">$0</dd>
						</dl>
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<h3>Créditos</h3>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12 table-responsive">
						<table class="table table-hover table-striped">
							<thead>
								<tr>
									<th class="text-center">Total abono capital</th>
									<th class="text-center">Total abono intereses</th>
									<th class="text-center">Total abono seguro</th>
									<th class="text-center">Total abono</th>
								</tr>
							</thead>
							<tbody>
								<tr>
									<td class="text-right">$0</td>
									<td class="text-right">$0</td>
									<td class="text-right">$0</td>
									<td class="text-right">$0</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>

			<div class="modal-footer">
        		<input type="submit" class="btn btn-outline-success" value="Procesar">
				<button type="button" class="btn btn-outline-secondary" data-dismiss="modal">Cancelar</button>
			</div>
		</div>
		</form>
	</div>
</div>
@push('scripts')
<script type="text/javascript">
	$(function(){
		$("#mResumen").on("show.bs.modal", function(event){
			dd = $(this).find("dd");
			td = $(this).find("td");
			$(".mTotal").text("Total: $" + $().formatoMoneda(data.totalRecaudo));

			//ahorros
			totalAbonoAhorros = 0;
			data.ahorros.forEach(function(ahorro){totalAbonoAhorros += ahorro.valor;});
			$(dd[0]).text("$" + $().formatoMoneda(totalAbonoAhorros));
			$(dd[1]).text("$" + $().formatoMoneda(totalAbonoAhorros + {{ $totalAhorros }}));

			//creditos
			cCapital = cIntereses = cSeguro = ctotal = 0;
			data.creditos.forEach(function(credito){
				cCapital += credito.capital;
				cIntereses += credito.intereses;
				cSeguro += credito.seguro;
			});
			ctotal = cCapital + cIntereses + cSeguro;
			$(td[0]).text("$" + $().formatoMoneda(cCapital));
			$(td[1]).text("$" + $().formatoMoneda(cIntereses));
			$(td[2]).text("$" + $().formatoMoneda(cSeguro));
			$(td[3]).text("$" + $().formatoMoneda(ctotal));
		});
		$("#mfRecaudo").submit(function(event){
			boton = $(this).find("input[type='submit']");
			boton.val("Procesando...");
			$(this).find("input[name='data']").val(JSON.stringify(data));
			if(boton.hasClass("disabled")) {
				console.log("Procesando...");
				event.preventDefault();
			}
			boton.addClass("disabled");
		});
	});
</script>
@endpush
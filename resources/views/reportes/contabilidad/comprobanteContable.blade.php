@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
@endphp
<div class="row">
	<div class="col-xs-2 text-center">
		<img src="{{ asset('storage/entidad/' . $imagen) }}">
	</div>
	<div class="col-xs-5">
		<div style="border:1px solid #400; height: 100px; padding:5px;">
			<strong>
				<label class="text-primary">{{ $tercero->nombre }}</label>
				<br>
				{{ $tercero->tipoIdentificacion->codigo }}: {{ number_format($tercero->numero_identificacion) }}-{{ $tercero->digito_verificacion }} 
			</strong>
			@if (!is_null($causaAnulacion))
				<center><h3 class="text-danger">COMPROBANTE ANULADO</h3></center>
			@endif
		</div>
	</div>
	<div class="col-xs-5">
		<div style="border:1px solid #400; height: 100px; padding:5px;">
			<strong>
				<label class="text-primary">{{ $cabecera->TipoDeComprobante }}</label>
			</strong>
			<br>
			<strong>Número:</strong> {{ $cabecera->CodigoComprobante }} - {{ $cabecera->NumeroComprobante }}
			<br>
			<strong>Fecha:</strong> {{ $cabecera->FechaMovimiento }}
			@if (!is_null($causaAnulacion))
				<br>
				<strong class="text-danger">Causa anulación:</strong> {{ $causaAnulacion->nombre }}
			@endif
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12">
		<div style="border:1px solid #400; height: 100px; padding:5px;">
			<strong>Detalle:</strong> {{ $cabecera->Descripcion }}
		</div>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Cuenta</th>
					<th>Nombre</th>
					<th>Tercero</th>
					<th>Referencia</th>
					<th>Débitos</th>
					<th>Créditos</th>
				</tr>
			</thead>
			<tbody>
				@php
					$totalDebitos = 0;
					$totalCreditos = 0;
				@endphp
				@foreach ($detalles as $detalle)
					@php
						$totalDebitos += $detalle->debitos;
						$totalCreditos += $detalle->creditos;
					@endphp
					<tr>
						<td style="font-size: 12px;">{{ $detalle->cuenta }}</td>
						<td style="font-size: 12px;">{{ $detalle->nombre }}</td>
						<td style="font-size: 12px;">{{ $detalle->nombreTercero }}</td>
						<td style="font-size: 12px;">{{ $detalle->referencia }}</td>
						<td style="font-size: 12px;" class="text-right">${{ number_format($detalle->debitos) }}</td>
						<td style="font-size: 12px;" class="text-right">${{ number_format($detalle->creditos) }}</td>
					</tr>
				@endforeach
				<tr style="background-color: #00ffff">
					<th class="text-right" colspan="4">TOTALES DOCUMENTO:</th>
					<th class="text-right">${{ number_format($totalDebitos) }}</th>
					<th class="text-right">${{ number_format($totalCreditos) }}</th>
				</tr>
			</tbody>
		</table>
	</div>
</div>

<div class="row">
	<div class="col-xs-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Elaborado</strong>
			</div>
			<div style="height: 55px;"></div>
			<div class="text-center" style="border-top:1px solid #000;">
				<strong>Usuario:</strong> I-Core
			</div>
		</div>
	</div>

	<div class="col-xs-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Revisado</strong>
			</div>
			<div></div>
		</div>
	</div>

	<div class="col-xs-4">
		<div style="border:1px solid #000; height: 100px;">
			<div class="text-center" style="border-bottom:1px solid #000;">
				<strong>Aprobado</strong>
			</div>
			<div></div>
		</div>
	</div>
</div>
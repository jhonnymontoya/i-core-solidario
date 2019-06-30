@php
	$imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
	$tercero = $entidad->terceroEntidad;
@endphp
<div class="row">
	<div class="col-xs-2 text-center">
		<img src="{{ asset('storage/entidad/' . $imagen) }}">
	</div>
	<div class="col-xs-10 text-center">
		<br>
		<strong>
			<label class="text-primary">{{ $tercero->nombre }}</label>
			<br>
			{{ $tercero->tipoIdentificacion->codigo }}: {{ number_format($tercero->numero_identificacion) }}-{{ $tercero->digito_verificacion }} 
		</strong>
		<h4>
			Reporte Transunion
		</h4>
		<h5>Fecha de corte {{ $fechaCorte }}</h5>
	</div>
</div>
<br>
<div class="row">
	<div class="col-xs-12 table-responsive">
		<table class="table table-striped">
			<thead>
				<tr>
					<th>Tipo Identificación</th>
					<th>Identificación</th>
					<th>Nombre</th>
					<th>Reservado</th>
					<th>Fecha límite de pago</th>
					<th>Numero obligación</th>
					<th>Código sucursal</th>
					<th>Calidad</th>
					<th>Calificación</th>
					<th>Estado del titular</th>
					<th>Estado de obligación</th>
					<th>Edad de mora</th>
					<th>Años en mora</th>
					<th>Fecha de corte</th>
					<th>Fecha inicio</th>
					<th>Fecha terminación</th>
					<th>Fecha de exigibilidad</th>
					<th>Fecha de prescripción</th>
					<th>Fecha de pago</th>
					<th>Modo extinción</th>
					<th>Tipo de pago</th>
					<th>Periodicidad</th>
					<th>Probabilidad de no pago</th>
					<th>Número de cuotas pagadas</th>
					<th>Número de cuotas pactadas</th>
					<th>Cuotas en mora</th>
					<th>Valor inicial</th>
					<th>Valor de mora</th>
					<th>Valor del saldo</th>
					<th>Valor de la cuota</th>
					<th>Valor De Cargo Fijo</th>
					<th>Línea de crédito</th>
					<th>Cláusula De Permanencia</th>
					<th>Tipo de contrato</th>
					<th>Estado de contrato</th>
					<th>Termino O Vigencia Del Contrato</th>
					<th>Numero De Meses Del Contrato</th>
					<th>Naturaleza jurídica</th>
					<th>Modalidad de crédito</th>
					<th>Tipo de moneda</th>
					<th>Tipo de garantía</th>
					<th>Valor de la garantía</th>
					<th>Obligación reestructurada</th>
					<th>Naturaleza de la reestructuración</th>
					<th>Número de reestructuraciones</th>
					<th>Clase  Tarjeta</th>
					<th>No De Cheques Devueltos</th>
					<th>Categoría Servicios</th>
					<th>Plazo</th>
					<th>Días De Cartera</th>
					<th>Tipo De Cuenta</th>
					<th>Cupo Sobregiro</th>
					<th>Dias Autorizados</th>
					<th>Dirección casa del tercero</th>
					<th>Teléfono casa del tercero</th>
					<th>Código ciudad casa del tercero</th>
					<th>Ciudad casa cel tercero</th>
					<th>Código departamento del tercero</th>
					<th>Departamento casa del tercero</th>
				</tr>
			</thead>
			<tbody>
				@foreach ($transuniones as $transunion)
					<tr>
						<td>{{ $transunion->tipo_identificacion }}</td>
						<td>{{ $transunion->identificacion }}</td>
						<td>{{ $transunion->nombre }}</td>
						<td>{{ $transunion->reservado }}</td>
						<td>{{ $transunion->limite_pago }}</td>
						<td>{{ $transunion->obligacion }}</td>
						<td>{{ $transunion->sucursal }}</td>
						<td>{{ $transunion->calidad }}</td>
						<td>{{ $transunion->calificacion }}</td>
						<td>{{ $transunion->titular }}</td>
						<td>{{ $transunion->estadoobl }}</td>
						<td>{{ $transunion->mora }}</td>
						<td>{{ $transunion->anos_mora }}</td>
						<td>{{ $transunion->corte }}</td>
						<td>{{ $transunion->fecha_inicio }}</td>
						<td>{{ $transunion->fecha_terminacion }}</td>
						<td>{{ $transunion->fecha_exigibilidad }}</td>
						<td>{{ $transunion->prescripcion }}</td>
						<td>{{ $transunion->fecha_pago }}</td>
						<td>{{ $transunion->extincion }}</td>
						<td>{{ $transunion->tipo_pago }}</td>
						<td>{{ $transunion->periodicidad }}</td>
						<td>{{ $transunion->probabilidad }}</td>
						<td>{{ $transunion->numero_cuotas_pagas }}</td>
						<td>{{ $transunion->cuotas_pactadas }}</td>
						<td>{{ $transunion->cuotas_vencidas }}</td>
						<td>{{ $transunion->valor_inicial }}</td>
						<td>{{ $transunion->valor_mora }}</td>
						<td>{{ $transunion->saldo }}</td>
						<td>{{ $transunion->valor_cuota }}</td>
						<td>{{ $transunion->cargo_fijo }}</td>
						<td>{{ $transunion->linea_credito }}</td>
						<td>{{ $transunion->permanencia }}</td>
						<td>{{ $transunion->tipo_contrato }}</td>
						<td>{{ $transunion->estado_contrato }}</td>
						<td>{{ $transunion->vigencia_contrato }}</td>
						<td>{{ $transunion->meses_contrato }}</td>
						<td>{{ $transunion->naturaleza_juridica }}</td>
						<td>{{ $transunion->modalidad_credito }}</td>
						<td>{{ $transunion->tipo_moneda }}</td>
						<td>{{ $transunion->tipo_garantia }}</td>
						<td>{{ $transunion->valor_garantia }}</td>
						<td>{{ $transunion->reestructurada }}</td>
						<td>{{ $transunion->naturaleza_reestructuracion }}</td>
						<td>{{ $transunion->numero_reestructuraciones }}</td>
						<td>{{ $transunion->clase_tarjeta }}</td>
						<td>{{ $transunion->cheques_devueltos }}</td>
						<td>{{ $transunion->categoria_servicios }}</td>
						<td>{{ $transunion->plazo }}</td>
						<td>{{ $transunion->dias_cartera }}</td>
						<td>{{ $transunion->tipo_cuenta }}</td>
						<td>{{ $transunion->cupo_sobregiro }}</td>
						<td>{{ $transunion->dias_autorizados }}</td>
						<td>{{ $transunion->direccion_casa }}</td>
						<td>{{ $transunion->telefono_casa }}</td>
						<td>{{ $transunion->codigo_ciudad_casa }}</td>
						<td>{{ $transunion->ciudad_casa }}</td>
						<td>{{ $transunion->codigo_departamento }}</td>
						<td>{{ $transunion->departamento }}</td>
					</tr>
				@endforeach
			</tbody>
		</table>
	</div>
</div>
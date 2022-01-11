@php
    $imagen = $entidad->categoriaImagenes[0]->pivot->nombre;
    $tercero = $entidad->terceroEntidad;
@endphp
<div class="row">
    <div class="col-2 text-center">
        <img src="{{ asset('storage/entidad/' . $imagen) }}">
    </div>
    <div class="col-10 text-center">
        <br>
        <strong>
            <label class="text-primary">{{ $tercero->nombre }}</label>
            <br>
            {{ $tercero->tipoIdentificacion->codigo }}: {{ number_format($tercero->numero_identificacion) }}-{{ $tercero->digito_verificacion }}
        </strong>
        <h4>
            Amortización de crédito
        </h4>
    </div>
</div>
<div class="container-fluid">
    <div class="card card-default card-outline">
        <div class="card-header with-border"><strong>SOLICITUD</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-3 col-3"><strong>Nombre:</strong></div>
                        <div class="col-md-9 col-9">{{ $solicitud->tercero->tipoIdentificacion->codigo }} {{ $solicitud->tercero->nombre_completo }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-3"><strong>Valor solicitud:</strong></div>
                        <div class="col-md-2 col-2">${{ number_format($solicitud->valor_credito) }}</div>

                        <div class="col-md-3 col-3"><strong>Modalidad:</strong></div>
                        <div class="col-md-4 col-4">{{ $solicitud->modalidadCredito->nombre }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-3"><strong>Fecha solicitud:</strong></div>
                        <div class="col-md-2 col-2">{{ $solicitud->fecha_solicitud }}</div>

                        <div class="col-md-3 col-3"><strong>Tasa M.V.:</strong></div>
                        <div class="col-md-4 col-4">{{ number_format($solicitud->tasa, 2) }}%</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-3"><strong>Radicado:</strong></div>
                        <div class="col-md-2 col-2">{{ $solicitud->id }}</div>

                        <div class="col-md-3 col-3"><strong>Fecha aprobación:</strong></div>
                        <div class="col-md-4 col-4">{{ $solicitud->fecha_aprobacion }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-3 col-3"><strong>Estado:</strong></div>
                        <div class="col-md-2 col-2">{{ $solicitud->estado_solicitud }}</div>

                        <div class="col-md-3 col-3"><strong>Fecha desembolso:</strong></div>
                        <div class="col-md-4 col-4">{{ $solicitud->fecha_desembolso }}</div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <div class="card card-default card-outline">
        <div class="card-header with-border"><strong>Amortización</strong></div>
        <div class="card-body">
            <div class="row" style="margin-left: 30px; margin-right: 30px;">
                <div class="col-md-3">
                    <div class="row">
                        <div class="col-md-8"><label>Tasa seguro cartera</label></div>
                        <div class="col-md-4">{{ empty($solicitud->seguroCartera) ? 0 : number_format($solicitud->seguroCartera->tasa_mes, 4) }}%</div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="row">
                        <div class="col-md-8"><label>Porcentaje capital en extraordinarias</label></div>
                        <div class="col-md-4">{{ number_format($solicitud->porcentajeCapitalEnExtraordinarias(), 2) }}%</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-6"><label>Tasa E.A.</label></div>
                        <div class="col-md-6">
                            <?php
                                $tasaEA = ($solicitud->tasa / 100) + 1;
                                $tasaEA = pow($tasaEA, 12) - 1;
                                $tasaEA = number_format($tasaEA * 100, 2);
                            ?>
                            {{ $tasaEA }}%
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th class="text-center">Cuota</th>
                    <th>Naturaleza cuota</th>
                    <th>Forma pago</th>
                    <th>Fecha pago</th>
                    <th class="text-center">Capital</th>
                    <th class="text-center">Intereses</th>
                    <th class="text-center">Seguro cartera</th>
                    <th class="text-center">Total cuota</th>
                    <th class="text-center">Nuevo saldo</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($solicitud->amortizaciones as $amortizacion)
                    <tr>
                        <td class="text-right">{{ $amortizacion->numero_cuota }}</td>
                        <td>{{ $amortizacion->naturaleza_cuota }}</td>
                        <td>{{ $amortizacion->forma_pago }}</td>
                        <td>{{ $amortizacion->fecha_cuota }}</td>
                        <td class="text-right">${{ number_format($amortizacion->abono_capital, 0) }}</td>
                        <td class="text-right">${{ number_format($amortizacion->abono_intereses, 0) }}</td>
                        <td class="text-right">${{ number_format($amortizacion->abono_seguro_cartera, 0) }}</td>
                        <td class="text-right">${{ number_format($amortizacion->total_cuota, 0) }}</td>
                        <td class="text-right">${{ number_format($amortizacion->nuevo_saldo_capital, 0) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
<br>

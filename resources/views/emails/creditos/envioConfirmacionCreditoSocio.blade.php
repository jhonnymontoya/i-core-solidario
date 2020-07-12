@component('mail::message', ['subcopy' => $subcopy])
{{ $data->fecha }}

# {{ $data->titulo }}

El <strong>{{ $data->entidad }}</strong> le informa que se ha registrado la solicitud de crédito con los siguientes datos:

<strong>Solicitante</strong>: {{ $data->solicitante }}.\
<strong>Número solicitud</strong>: {{ $data->numeroSolicitud }}.\
<strong>Modalidad de crédito</strong>: {{ $data->modalidad }}.\
<strong>Fecha solicitud</strong>: {{ $data->fechaSolicitud }}.\
<strong>Empresa</strong>: {{ $data->empresa }}.\
<strong>Valor</strong>: {{ $data->valor }}.\
<strong>Tasa M.V.</strong>: {{ $data->tasa }}.\
<strong>Plazo (cuotas)</strong>: {{ $data->plazo }}.\
<strong>Periodicidad</strong>: {{ $data->periodicidad }}.

Para mayor información, Contacta a un funcionario de servicio.

Cordialmente,<br>
{{ $data->sigla }}
@endcomponent

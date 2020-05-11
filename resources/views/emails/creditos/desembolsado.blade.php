@component('mail::message', ['subcopy' => $subcopy])
{{ $data->fecha }}

# {{ $data->titulo }}

El <strong>{{ $data->entidad }}</strong> le informa que su solicitud de crédito fue aceptada con el número de obligación <strong>{{ $data->numeroSolicitud }}</strong> e inició la gestión de desembolso.

Modalidad de crédito: {{ $data->modalidad }}.\
Valor: {{ $data->valor }}.\
Plazo: {{ $data->plazo }}.

Para mayor información, Contacta a un funcionario de servicio.

Cordialmente,<br>
{{ $data->sigla }}
@endcomponent

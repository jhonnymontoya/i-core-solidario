@component('mail::message')
{{ $data->fecha }}

# {{ $data->titulo }}

El <strong>{{ $data->entidad }}</strong> le informa que su solicitud de crédito fue <strong>APROBADA</strong> por un valor {{ $data->valor }} y un plazo de {{ $data->plazo }}.

Modalidad de crédito: {{ $data->modalidad }}

Le informaremos cuando su solicitud de crédito sea DESEMBOLSADA.

Para mayor información, Contacta a un funcionario de servicio.

Cordialmente,<br>
{{ $data->sigla }}
@endcomponent

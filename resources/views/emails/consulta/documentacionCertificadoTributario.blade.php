@component('mail::message')
# {{ $titulo }}:

Adjunto a este correo electrónico encontrará su certificado tributario con la información de productos y servicios que tuvo con {{ $sigla }} durante el año {{ $anio }}.

Cordialmente,<br>
{{ $sigla }}
@endcomponent

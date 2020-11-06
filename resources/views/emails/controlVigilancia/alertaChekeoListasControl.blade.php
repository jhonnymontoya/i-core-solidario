@component('mail::message')
# {{ $titulo }}:

Este mensaje ha sido enviado automáticamente como herramienta para desarrollar tu gestión como oficial de cumplimiento en la entidad {{ $sigla }}.

Cordialmente,<br>
{{ $sigla }}
@endcomponent

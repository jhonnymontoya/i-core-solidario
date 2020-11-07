@component('mail::message')
# {{ $titulo }}:

Este mensaje ha sido enviado automáticamente como herramienta para desarrollar tu gestión como oficial de cumplimiento en la entidad {{ $sigla }}.

Adjunto encontrará el reporte {{ $periodicidad }} del chekeo de listas de control.

Cordialmente,<br>
{{ $sigla }}
@endcomponent

@component('mail::message')
# {{ $titulo }}:

Nos complace darle la bienvenida al <strong>{{ $entidad->terceroEntidad->nombre }}</strong>, a partir de este momento puede disfrutar de todos los derechos y beneficios de <strong>{{ $entidad->terceroEntidad->sigla }}</strong> como socio activo.

Entre muchos de los beneficios, se encuentra la posibilidad de acceder a la sucursal web, donde puede interactuar activamente y en tiempo real con los servicios que el fondo de empleados ha dispuesto para usted.

Credenciales de ingreso:

<strong>Identificación:</strong> {{ $tercero->numero_identificacion }}
<br>
<strong>Contraseña:</strong> {{ $password }}

Para ingresar ubique el vínculo de la sucursal web en el siguiente enlace:
@component('mail::button', ['url' => $entidad->pagina_web, 'color' => 'blue'])
{{ $entidad->terceroEntidad->sigla }}
@endcomponent

Cordialmente,<br>
{{ $entidad->terceroEntidad->sigla }}
@endcomponent

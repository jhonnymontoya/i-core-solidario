<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        'App\Events\Event' => [
            'App\Listeners\EventListener',
        ],

        /*EVENTOS GENERALES*/
        'App\Events\General\ProcesoCerrado' => [
            'App\Listeners\General\IniciarNuevoProceso'
        ],

        /*EVENTOS DE SOCIOS*/
        'App\Events\Socios\SocioAfiliado' => [
            'App\Listeners\Socios\EnviarCorreoBienvenida',
            'App\Listeners\Socios\ChequearSocioEnListasControl',
        ],

        /*EVENTOS DE CREDITOS*/
        'App\Events\Creditos\SolicitudCreditoAprobado' => [
            'App\Listeners\Creditos\EnviarCorreoSolicitudCreditoAprobado'
        ],

        'App\Events\Creditos\SolicitudCreditoDesembolsado' => [
            'App\Listeners\Creditos\EnviarCorreoSolicitudCreditoDesembolsada'
        ],

        'App\Events\Creditos\SolicitudCreditoDigitalEnviada' => [
            'App\Listeners\Creditos\EnviaCorreoSolicitudCreditoDigitalEnviada',
            'App\Listeners\Creditos\EnviarCorreoNotificacionFuncionarioSolicitudCreditoDigitalEnviada'
        ],

        /*EVENTOS DE TARJETAS*/
        'App\Events\Tarjeta\TarjetaHabienteCreado' => [
            'App\Listeners\Tarjeta\CrearTarjetaHabienteEnRed'
        ],

        'App\Events\Tarjeta\TarjetaHabienteCupoModificado' => [
            'App\Listeners\Tarjeta\ModificarCuentaCorrienteEnRed'
        ],

        'App\Events\Tarjeta\SolicitudCreditoAjusteCreado' => [
            'App\Listeners\Tarjeta\ActualizarCupoTarjetaEnRed'
        ],

        'App\Events\Tarjeta\RecibirTransacciones' => [
            'App\Listeners\Tarjeta\RecibirTransaccionesRed'
        ],

        'App\Events\Tarjeta\ProcesarTransaccionesProvenientesRed' => [
            'App\Listeners\Tarjeta\ProcesarTransaccionesProvenientesDeRed'
        ],

        'App\Events\Tarjeta\CalcularAjusteAhorrosVista' => [
            'App\Listeners\Tarjeta\CalcularAjusteAhorroVista'
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

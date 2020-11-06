<?php

use Illuminate\Foundation\Inspiring;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('tarjeta:recibirTransacciones', function() {
    event(new \App\Events\Tarjeta\RecibirTransacciones());
})->describe("Recibe transacciones del sincronizador y las almacena");

Artisan::command('cronJob:ejecutar', function() {
    //$this->comment("Recibiendo transacciones...");
    event(new \App\Events\ICoreCronJob());
})->describe("Ejecuta labores de CronJob");

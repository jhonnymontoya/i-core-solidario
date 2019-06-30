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
    //$this->comment("Recibiendo transacciones...");
    //\Log::info("Se llama comando iniciar recibir transacciones en red");
    event(new \App\Events\Tarjeta\RecibirTransacciones());
})->describe("Recibe transacciones del sincronizador y las almacena");

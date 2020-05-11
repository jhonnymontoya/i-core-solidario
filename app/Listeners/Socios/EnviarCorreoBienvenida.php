<?php

namespace App\Listeners\Socios;

use App\Events\Socios\SocioAfiliado;
use App\Mail\Socios\SocioAfiliado as MailSocioAfiliado;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Mail;

class EnviarCorreoBienvenida implements ShouldQueue
{
	use InteractsWithQueue;

	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct() {}

	/**
	 * Handle the event.
	 *
	 * @param  SocioAfiliado  $event
	 * @return void
	 */
	public function handle(SocioAfiliado $event) {
		$socio = $event->socio;
		$password = $event->password;

		$mail = null;
		$contactos = $socio->tercero->contactos;
		if($contactos){
			foreach($contactos as $contacto) {
				if($contacto->es_preferido) {
					$mail = $contacto->email ? $contacto->email : $mail;
					if($mail)break;
				}
				$mail = $contacto->email;
			}
		}

		if($mail) {
			Mail::to($mail)->send(new MailSocioAfiliado($socio, $password));
		}
	}
}

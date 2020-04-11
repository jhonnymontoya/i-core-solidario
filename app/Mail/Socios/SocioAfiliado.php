<?php

namespace App\Mail\Socios;

use App\Models\Socios\Socio;
use App\Traits\FonadminTrait;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SocioAfiliado extends Mailable
{
	use Queueable, SerializesModels, FonadminTrait;

	private $socio;
	private $password;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(Socio $socio, $password) {
		$this->socio = $socio;
		$this->password = $password;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$titulo = "Estimad" . (optional(optional($this->socio->tercero)->sexo)->codigo == 2 ? "a " : "o ") . $this->socio->tercero->nombre;
		$entidad = $this->socio->tercero->entidad;
		$sigla = $entidad->terceroEntidad->sigla;
		$subject = "Bienvenid" . (optional(optional($this->socio->tercero)->sexo)->codigo == 2 ? "a a " : "o a ") . $sigla;

		/*$this->withSwiftMessage(function($message) {
			$message->getHeaders()
				->addTextHeader('X-Mailgun-Tag', 'SocioAfiliado');
		});*/

		return $this->from(env('MAIL_FROM_ADDRESS', 'noresponder@i-core.co'), $sigla)
			->subject($subject)
			->markdown('emails.socios.afiliado')
			->withSocio($this->socio)
			->withTercero($this->socio->tercero)
			->withEntidad($entidad)
			->withPassword($this->password)
			->withTitulo($titulo);
	}
}

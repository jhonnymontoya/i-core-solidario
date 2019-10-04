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
		$subject = "Bienvenid" . (optional(optional($this->socio->tercero)->sexo)->codigo == 2 ? "a a " : "o a ") . $this->getEntidad()->terceroEntidad->sigla;
		return $this->from(env('MAIL_FROM_ADDRESS', 'noresponder@i-core.co'), $this->getEntidad()->terceroEntidad->sigla)
						->subject($subject)
						->markdown('emails.socios.afiliado')
						->withSocio($this->socio)
						->withTercero($this->socio->tercero)
						->withEntidad($this->getEntidad())
						->withPassword($this->password)
						->withTitulo($titulo);
	}
}

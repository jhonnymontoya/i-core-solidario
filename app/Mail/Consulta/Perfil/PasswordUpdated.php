<?php

namespace App\Mail\Consulta\Perfil;

use App\Models\Sistema\UsuarioWeb;
use App\Traits\FonadminTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordUpdated extends Mailable
{
	use Queueable, SerializesModels, FonadminTrait;

	private $usuario;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(UsuarioWeb $usuario) {
		$this->usuario = $usuario;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$socio = $this->usuario->socios[0];
		$tercero = $socio->tercero;

		$titulo = "Estimado " . $tercero->nombre_corto;
		$subject = "Se ha actualizado su información de perfil";

		/*$this->withSwiftMessage(function($message) {
			$message->getHeaders()
				->addTextHeader('X-Mailgun-Tag', 'FuncionarioPassActualizado');
		});*/

		return $this->from(env('MAIL_FROM_ADDRESS', 'noresponder@i-core.co'), $this->getEntidad()->terceroEntidad->sigla)
						->subject($subject)
						->view('emails.consulta.passwordUpdated')
						->withUsuario($this->usuario)
						->withTitulo($titulo);
	}
}

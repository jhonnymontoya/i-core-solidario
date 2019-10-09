<?php

namespace App\Mail\Sistema\Profile;

use App\Models\Sistema\Usuario;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordUpdated extends Mailable
{
	use Queueable, SerializesModels;

	private $usuario;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct(Usuario $usuario) {
		$this->usuario = $usuario;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$titulo = "Estimado " . $this->usuario->nombre_corto;
		$subject = "Se ha actualizado su contraseña";

		$this->withSwiftMessage(function($message) {
			$message->getHeaders()
				->addTextHeader('X-Mailgun-Tag', 'SocioPassActualizado');
		});

		return $this->from(env('MAIL_FROM_ADDRESS', 'noresponder@i-core.co'), env('MAIL_FROM_NAME', 'No responder'))
						->subject($subject)
						->view('emails.sistema.passwordUpdated')
						->withUsuario($this->usuario);
	}
}

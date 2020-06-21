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
				->addTextHeader('X-ICore-Tag', 'FuncionarioPassActualizado');
		});

		return $this->subject($subject)
			->view('emails.sistema.passwordUpdated')
			->withUsuario($this->usuario);
	}
}

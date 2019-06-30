<?php

namespace App\Mail\Sistema;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetLink extends Mailable
{
	use Queueable, SerializesModels;

	private $token;

	/**
	 * Create a new message instance.
	 *
	 * @return void
	 */
	public function __construct($token) {
		$this->token = $token;
	}

	/**
	 * Build the message.
	 *
	 * @return $this
	 */
	public function build() {
		$asunto = "Restaurar contraseña";
		$url = route('password.reset', $this->token);
		return $this->view('emails.sistema.passwordResetLink')->withUrl($url)->subject($asunto);
	}
}

<?php

namespace Modules\CustomerModule\Emails;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class PasswordResetMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token,$username)
    {
        $this->token = $token;
        $this->username = $username;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
//        return $this->view('view.name');
        return $this
            ->subject('Your One-Time Password for Account Verification')
            ->html("<p>Dear {$this->username}</p>
                    <p>Thank you for initiating the password recovery process for your account. Please use the following One-Time Password: <b>{$this->token}</b> to verify your identity and reset your password.</p>");
    }
}

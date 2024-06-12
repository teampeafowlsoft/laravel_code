<?php
//
//namespace Modules\CustomerModule\Emails;
//
//use Illuminate\Bus\Queueable;
//use Illuminate\Mail\Mailable;
//use Illuminate\Queue\SerializesModels;
//use Illuminate\Contracts\Queue\ShouldQueue;
//
//class EmailOtpMail extends Mailable
//{
//    use Queueable, SerializesModels;
//
//    public $otp;
//
//    /**
//     * Create a new message instance.
//     *
//     * @return void
//     */
//    public function __construct($otp)
//    {
//        $this->otp = $otp;
//    }
//
//    /**
//     * Build the message.
//     *
//     * @return $this
//     */
//    public function build()
//    {
//        return $this->subject('Forgot Password OTP')
//            ->view('customermodule::admin.emails')
//            ->with([
//                'otp' => $this->otp,
//            ]);
//    }
//}

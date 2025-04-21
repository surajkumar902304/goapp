<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailChangedNotificationMail extends Mailable
{
    use Queueable, SerializesModels;
    public $name;
    public $oldEmail;
    public $newEmail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($oldEmail, $newEmail, $name)
    {
        $this->name = $name;
        $this->oldEmail = $oldEmail;
        $this->newEmail = $newEmail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Email Has Been Updated')
            ->markdown('emails.email-changed-notification')
            ->with([
                'oldEmail' => $this->oldEmail,
                'newEmail' => $this->newEmail,
                'name' => $this->name,
            ]);
    }
}

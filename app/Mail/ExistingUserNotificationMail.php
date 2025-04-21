<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExistingUserNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $shopName;
    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $shopName
     */
    public function __construct($name, $shopName)
    {
        $this->name = $name;
        $this->shopName = $shopName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Shop Access Notification')
                    ->markdown('emails.existing-user-notification');
    }
}

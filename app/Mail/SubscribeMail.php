<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SubscribeMail extends Mailable
{
    use Queueable, SerializesModels;

    private static $data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        self::$data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $viewName = self::$data['fromHost'] ? 'emails.hostMail' : 'emails.attendeeMail';
        return $this->from('calendly@demo.com', 'Calendly Demo')->view($viewName)
            ->with('data', self::$data);
    }
}

<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class RemainderMail extends Mailable
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
        $title = self::$data['host']['name'] . '&' . self::$data['attendee']['name'];
        return $this->from('calendly@demo.com', $title)->view('emails.remainderMail')
            ->with('data', self::$data);
    }
}

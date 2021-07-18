<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\File;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailMarketing extends Mailable
{
    use Queueable, SerializesModels;

    protected $templateMail;
    protected $titleMail;

    /**
     * Create a new message instance.
     *
     * @param $templateMail
     * @param $titleMail
     */
    public function __construct($templateMail, $titleMail)
    {
        $this->templateMail = $templateMail;
        $this->titleMail = $titleMail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('maileclipse::templates.' . $this->templateMail)
            ->subject($this->titleMail);
    }

}

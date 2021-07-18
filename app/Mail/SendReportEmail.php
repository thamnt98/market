<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReportEmail extends Mailable
{
    use Queueable, SerializesModels;

    public  $orders;
    public  $logins;
    public  $name;

    /**
     * Create a new message instance.
     * @param $orders
     */
    public function __construct($orders, $logins, $name)
    {
        $this->orders = $orders;
        $this->logins = $logins;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('admin.mail.report')
            ->subject('Weekly Confirmation')
            ->with([
                'orders' => $this->orders,
                'logins' => $this->logins,
                'name' => $this->name
            ]);
    }
}

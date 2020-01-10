<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CheckoutNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $data;

    /**
     * @var Files
     */
    protected $files;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data, $files)
    {
        $this->data = $data;
        $this->files = $files;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $data = $this->data;
        $files = $this->files;
        $message = $this->from(env('MAIL_FROM_ADDRESS'), 'Checkout eBook(s)')
            ->subject('Successfully Buy eBook(s)')
            ->markdown('mails.checkout')
            ->with([
                'name' => $data['name'],
                'amount' => $data['amount'],
            ]);

        // Attached multiple files
        foreach ($files as $file) { 
            $message->attach($file); // attach each file
        }

        return $message;
    }
}

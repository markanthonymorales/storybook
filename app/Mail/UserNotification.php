<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $data;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $name = $this->data->name;
        $shared_to = $this->data->shared_to;
        return $this->from(env('MAIL_FROM_ADDRESS'), 'Story Information')
            ->subject('You Shared A Story')
            ->markdown('mails.share_to')
            ->with([
                'name' => $name,
                'shared_to' => implode(', ', $shared_to)
            ]);
    }
}

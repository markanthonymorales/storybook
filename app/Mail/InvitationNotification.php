<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvitationNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var User
     */
    protected $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $user = $this->user;
        return $this->from(env('MAIL_FROM_ADDRESS'), 'Story Information')
            ->subject('Shared Story')
            ->markdown('mails.invite')
            ->with([
                'name' => $user->firstname.' '.$user->lastname,
                'link' => route('register')
            ]);
    }
}

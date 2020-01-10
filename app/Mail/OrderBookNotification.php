<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OrderBookNotification extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var data
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
        $message = $this->from(env('MAIL_FROM_ADDRESS'), 'BoD')
            ->subject('Purchase Receipt')
            ->markdown('mails.order')
            ->with([
                'title' => $data['title'],
                'author' => $data['author'],

                'with' => $data['with'],
                'height' => $data['height'],
                'total_page' => $data['total_page'],
                'total_colored_page' => $data['total_colored_page'],
                'colored_index' => $data['colored_index'],
                'paper' => $data['paper'],
                'binding' => $data['binding'],
                'cover' => $data['cover'],
                'lamination' => $data['lamination'],

                'address' => $data['address'],
                'street' => $data['street'],
                'city' => $data['city'],
                'zipcode' => $data['zipcode'],
                'country' => $data['country'],

                'shipping_option' => $data['shipping_option'],
                'shipping_price' => $data['shipping_price'],

                'name' => $data['name'],
                'amount' => $data['amount'],
            ]);

        // Attached multiple files
        foreach ($files as $file) { 
            // having problem on timeout execution
            $message->attach($file); // attach each file
        }

        return $message;
    }
}

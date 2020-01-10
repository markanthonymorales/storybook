<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class StoryEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;
    public $oldEmail = [];
    public $action = 'stories';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $oldEmail = [])
    {
        ini_set('max_execution_time', 300); //300 seconds = 5
        $this->data = $data;
        $this->oldEmail = $oldEmail;
    }
}

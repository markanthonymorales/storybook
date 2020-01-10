<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Mail\InvitationNotification;

use Mail;
use Auth;

class InvitationNotificationListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        ini_set('max_execution_time', 300);
        
        // send email to added users
        $diffEmail = explode(',', $event->data->shared_to);
        if($event->oldEmail != ''){
            $diffEmail = array_diff(explode(',', $event->data->shared_to), explode(',', $event->oldEmail));
        }

        foreach ($diffEmail as $key => $shared) {
            if(!$shared)
                continue;
            
            Mail::to($shared)->send(new InvitationNotification(Auth::user()));
            sleep(3);
        }
    }
}

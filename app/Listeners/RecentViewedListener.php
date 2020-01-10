<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Repository\RecentViewedRepository;
use Auth;

class RecentViewedListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $user = Auth::user();
        // Push story ID to session
        RecentViewedRepository::create($user->id, $event->data->id, $event->action);
    }
}

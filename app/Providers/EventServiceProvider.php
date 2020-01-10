<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;

use App\Events\StoryEvent;
use App\Events\BookEvent;
use App\Events\PrintEvent;

use App\Listeners\InvitationNotificationListener;
use App\Listeners\RecentViewedListener;
use App\Listeners\GeneratePrintFileListener;
use App\Listeners\OrderBookNotificationListener;

use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        StoryEvent::class => [
            RecentViewedListener::class,
            InvitationNotificationListener::class,
        ],
        BookEvent::class => [
            RecentViewedListener::class,
        ],
        PrintEvent::class => [
            // GeneratePrintFileListener::class,
            OrderBookNotificationListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}

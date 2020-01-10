<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Repository\BookRepository;
use App\Repository\PrintRepository;
use App\Repository\OrderRepository;

use Auth;
use Storage;

class GeneratePrintFileListener
{
    /**
     * Handle the event.
     *
     * @param  PrintEvent  $event
     * @return void
     */
    public function handle($event)
    {
        // $authId = Auth::user()->id;
        
        // $xmlPath = 'public/generatedXML/'.$authId.'/'.$event->request->title.'.xml';

        // Storage::put(
        //     '/'.$xmlPath, 
        //     PrintRepository::getXML($event->request)
        // );

        // // save data order physical book
        // OrderRepository::setData($event->request);
    }
}

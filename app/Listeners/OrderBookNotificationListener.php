<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

use App\Repository\PrintRepository;
use App\Repository\BookRepository;
use App\Repository\OrderRepository;

use App\Mail\OrderBookNotification;

use Storage;
use Auth;
use Mail;

class OrderBookNotificationListener
{
    /**
     * Handle the event.
     *
     * @param  PrintEvent  $event
     * @return void
     */
    public function handle($event)
    {
        $user = Auth::user();

        // generate files (XML and EPUB)
        $book = BookRepository::getBookById($user->id, $event->request->id);
        // $files[] = BookRepository::generateEPub($book['id'], $book); //remove for the meantime
        $files[] = BookRepository::generatePDF($book['id'], $book);

        $xmlPath = 'public/generatedXML/'.$user->id.'/'.$event->request->title.'.xml';
        
        Storage::put('/'.$xmlPath, PrintRepository::getXML($event->request));
        $getXMLUrl = Storage::url($xmlPath);
        
        $files[] = public_path($getXMLUrl);

        $data = PrintRepository::handleData($event->request);

        // save data order physical book
        OrderRepository::setData($event->request);

        // send mail to printing company
        Mail::to($data['to'])->send(new OrderBookNotification($data, $files));
        sleep(3);
    }
}

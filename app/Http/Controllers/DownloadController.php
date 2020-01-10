<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repository\BookRepository;
use Auth;

class DownloadController extends Controller
{
    public function getPDF($id)
    {
        $user = Auth::user();

        $book = BookRepository::getBookById($user?$user->id:null, $id);

        if(!$book)
            return redirect()->back()->with('download_message', 'Not allowed to download book id #'.$id);

        $file = BookRepository::generatePDF($id, $book);

        return response()->file($file);
    }

    public function getEpub($id)
    {
        $user = Auth::user();

        $book = BookRepository::getBookById($user?$user->id:null, $id);

        if(!$book)
            return redirect()->back()->with('download_message', 'Not allowed to download book id #'.$id);

        $file = BookRepository::generateEPub($id, $book);

        return response()->download($file);
    }
}

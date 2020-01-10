<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Repository\BookRepository;
use App\Configuration;

class IndexController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

	/**
     * Show the application index.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if (\Auth::user()) {
            return redirect('/mystorybook');
        }
        return view('index');
    }

    public function terms()
    {
        return view('terms-policies', [
            'data' => Configuration::getValue('terms_policies')
        ]);
    }
}

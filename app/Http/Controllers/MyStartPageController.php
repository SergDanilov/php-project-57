<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyStartPageController extends Controller
{
    public function show()
    {
        return view('my-start-page');
    }
}

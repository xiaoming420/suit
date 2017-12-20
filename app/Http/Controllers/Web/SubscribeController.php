<?php

namespace App\Http\Controllers\Web;

use App\Models\city;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SubscribeController extends Controller
{
    public function infopage(Request $request)
    {
        //$provines = city::getprovines();
        return view('web.reserve');
    }
}

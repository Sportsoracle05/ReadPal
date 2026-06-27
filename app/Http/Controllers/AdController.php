<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdController extends Controller
{
    public function show(Request $request)
    {
        return view('ads.vast', [
            'redirect' => $request->get('redirect', url('/'))
        ]);
    }
}
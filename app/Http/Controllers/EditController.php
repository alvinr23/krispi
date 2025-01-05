<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;

class HapusController extends Controller
{
    public function edit()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('Users')->find();
        return view('user1', compact('data'));
    }
}

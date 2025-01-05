<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HapusController extends Controller
{
    public function destroy()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('users')->delete();
        return view('user1', compact('data'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use DB;
use Auth;

class UsersController extends Controller
{
    public function index()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('users')->get();
        return view('user.index', compact('data'));
    }

    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        return view('user.create');
    }

    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
            'level' => 'required'
        ]);

        $data['password'] = bcrypt($request->password);
        User::create($data);

        return redirect()->route('user.index')
            ->with('success', 'Post created successfully.');
    }


    public function show(User $user)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        return view('user.show', compact('user'));
    }

    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $user = DB::table('users')->where('id', $id)->first();
        return view('user.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $user = DB::table('users')->where('id', $id)->first();
        $pass = $user->password;
        $data = [
            'name' => $request->name,
            'username' => $request->username,
            'password' => $pass,
            'level' => $request->level
        ];

        if ($request->password != null) {
            $data['password'] = bcrypt($request->password);
        }

        DB::table('users')->where('id', $id)->update($data);
        return redirect()->route('user.index')->with('success', 'Post updated successfully');
    }

    public function destroy(User $user)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $user->delete();

        return redirect()->route('user.index')
            ->with('success', 'Post deleted successfully');
    }
    
}

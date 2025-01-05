<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PasswordController extends Controller
{
   
    

    
    public function edit(Admin $admin)
    {
        
        return view('admin.password.edit', compact('admin'));
    }

    
    public function update(Request $request, Admin $admin)
    {
        $admin = Admin::where('id',$admin->id)->first();
        
        if($request->password != Hash::check($request->password, $admin->password)){
            return back()->withErrors([

                'old_password' => 'Password Anda Tidak sesuai'
            ]);
        }

        Admin::where('id',$admin->id)->update([
            'password' => bcrypt($request->new_password)

        ]);

        return redirect()->intended('dashboard');
    
                        

    }

   
}

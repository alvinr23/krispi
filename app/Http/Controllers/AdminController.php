<?php

namespace App\Http\Controllers;

use App\Models\admin;
use Illuminate\Http\Request;
use DB;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = DB::table('admin')->get();
        return view('admin1', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => 'required',
            'name' => 'required',
            'email' => 'required',
            'password' => 'required',
        ]);


        $data['password'] = bcrypt($request->password);

        /// insert setiap request dari form ke dalam database via model
        /// jika menggunakan metode ini, maka nama field dan nama form harus sama
        Admin::create($data);

        /// redirect jika sukses menyimpan data
        return redirect()->route('admin.index')
            ->with('success', 'Post created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function show(Admin $admin)
    {

        return view('admin.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function edit(Admin $admin)
    {
        return view('admin.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Admin $admin)
    {


        $data = $request->validate([
            'name' => 'required',
            'username' => 'required',
            'email' => 'required',
            // 'password' => 'required',
        ]);

        /// mengubah data berdasarkan request dan parameter yang dikirimkan
        // $admin->update($request->all());
        if ($request->password == null || $request->password == '') {
            $payload = Admin::where('id', $admin->id)->first();
            $admin = Admin::where('id', $admin->id)->update([
                $data,
                $data['password'] => $payload->password
            ]);
        }


        /// setelah berhasil mengubah data
        if ($request->password != null) {
            $admin = Admin::where('id', $admin->id)->update([
                $data,
                $data['password'] => bcrypt($request->password),
            ]);
        }
        return redirect()->route('admins.index')->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Admin  $admin
     * @return \Illuminate\Http\Response
     */
    public function destroy(Admin $admin)
    {
        $admin->delete();

        return redirect()->route('admins.index')
            ->with('success', 'Post deleted successfully');
    }
}

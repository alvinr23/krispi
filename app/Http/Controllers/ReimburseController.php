<?php

namespace App\Http\Controllers;

use App\Models\Reimburse;
use Illuminate\Http\Request;
use DB;
use Auth;

class ReimburseController extends Controller
{
    public function index()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('reimburse as r')
        ->leftjoin('pegawai as p', 'r.id_pegawai', 'p.id')
        ->select("r.*","p.nama as nama")
        ->get();
        

        return view('reimburse.index', compact('data'));
    }

    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        
        $reimburse = [
            'nama_pegawai' => $request->nama_pegawai,
            'jenis' => $request->jenis,
            'tanggal' => $request->tanggal,
        ];
        DB::table('reimburse')->insert($reimburse);
        return redirect()->route('reimburse.index')->with('success', 'Post updated successfully');
    }

    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table('reimburse')->get();
        return view('reimburse.create', compact('reimburse'));
    }

    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $reimburse = DB::table("reimburse")->where('id',$id)->first();
        return view('reimburse.edit', compact('reimburse'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */

     public function update(Request $request, $id)
     {
         if (Auth::user() == '') {
             return view('auth.login');
             exit();
         }
         $reimburse = DB::table('reimburse')->where('id', $id)->first();
         $reimburse = [
             'nama_pegawai' => $request->nama_pegawai,
             'jenis' => $request->jenis,
             'tanggal' => $request->tanggal,
         ];
         DB::table('reimburse')->where('id', $id)->update($reimburse);
         return redirect()->route('reimburse.index')->with('success', 'Post updated successfully');
     }

    public function destroy($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $reimburse = DB::table('reimburse')->where('id', $id);
        $reimburse->delete();

        return redirect()->route('reimburse.index')
            ->with('success','Post deleted successfully');
    }


    public function lihat($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $foto = DB::table('reimburse_foto')->where('id_reimburse_asset', $id)->get();
        return view('reimburse.foto', compact('id','foto'));
    }

    public function fotouploadreimburse(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $file = $request->file('foto');
        $tujuan_upload = 'files/reimburse/';

        $nama = $file->getClientOriginalName();
        $file->move($tujuan_upload, $nama);

        DB::table('reimburse_foto')->insert(['file' => $nama, 'id_reimburse' => $id, 'deskripsi' => $request->deskripsi]);
        return back();
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use DB;
use Auth;

class BarangController extends Controller
{
    public function Auth(){
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
    }
    public function index()
    {
        Auth();
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('barang as a')
            ->leftjoin('barang_kategori as b', 'a.id_kategori', 'b.id')
            ->select("a.*", "b.nama as kategori")
            ->get();
        //$data = Kendaraan::with('user')->get();

        return view('barang.index', compact('data'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $kategori = DB::table('barang_kategori')->get();
        return view('barang.create', compact('kategori'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = $request->validate([
            'kode' => 'required',
            'nama' => 'required',
            'deskripsi' => 'required',
            'id_kategori' => 'required',
        ]);
        $barang = [
            'kode' => $request->kode,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'id_kategori' => $request->id_kategori,
            'created_id' => auth()->user()->id,
            'updated_id' => auth()->user()->id,
        ];
        DB::table('barang')->insert($barang);
        return redirect()->route('barang.index')->with('success', 'Post updated successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table("barang")->where('id', $id)->first();
        $kategori = DB::table("barang_kategori")->get();
        return view('barang.edit', compact('barang', 'kategori'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = [
            'kode' => $request->kode,
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'id_kategori' => $request->id_kategori,
            'updated_id' => auth()->user()->id,
        ];

        DB::table('barang')->where('id', $id)->update($barang);

        return redirect()->route('barang.index')->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table("barang")->where('id', $id)->delete();

        return redirect()->route('barang.index')
            ->with('success', 'Post deleted successfully');
    }

}

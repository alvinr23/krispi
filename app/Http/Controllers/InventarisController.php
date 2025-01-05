<?php

namespace App\Http\Controllers;

use App\Models\Inventaris;
use Illuminate\Http\Request;
use DB;
use Auth;

class InventarisController extends Controller
{
    public function index()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('inventaris as a')
            ->leftjoin('pegawai as b', 'b.id', 'a.id_pegawai')
            ->leftjoin('barang_kategori as c', 'c.id', 'a.id_barang')
            ->leftjoin('verifikasi_barang as d', 'd.id', 'a.verifikasi')
            ->select('a.*', 'b.nama as nama', 'b.nip as nip', 'c.nama as barang', 'd.nama as verifikasi')
            ->get();
        return view('inventaris.index', compact('data'));
    }

    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $pegawai = DB::table('pegawai')->get();
        $barang = DB::table('barang')->get();
        return view('inventaris.create', compact('pegawai', 'barang'));
    }

    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $status = DB::table('barang')->where('id', $request->id_barang)->first();
        if($status->id_kategori == $request->id_jenis){
            $inventaris = [
                'id_pegawai' => $request->id_pegawai,
                'id_barang' => $request->id_barang,
                'keterangan' => $request->keterangan,
                'verifikasi' => 1,
            ];
            DB::table('inventaris')->insert($inventaris);
            return redirect()->back()->with('success', 'Post updated successfully');
        }else{
            return back()->withErrors(['error' => ['Barang tidak bisa digunakan di kendaraan ini']]);
        }

        
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $inventaris = DB::table("inventaris")->where('id', $id)->first();
        $jenis = DB::table("barang_kategori")->get();
        $verifikasi = DB::table('verifikasi_barang')->get();
        return view('inventaris.edit', compact('inventaris', 'jenis', 'verifikasi'));
    }

    public function update(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $inventaris = DB::table('inventaris')->where('id', $id)->first();
        $data = [
            'id_pegawai' => $request->id_pegawai,
            'id_barang' => $request->id_barang,
            'keterangan' => $request->keterangan,
            'verifikasi' => $request->verifikasi,
        ];
        if (Auth::user() === 'admin') {
            $data['verifikasi'] = $request->verifikasi;
        }
        DB::table('inventaris')->where('id', $id)->update($data);
        return redirect()->route('inventaris.index')->with('success', 'Post updated successfully');
    }

    public function destroy($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $del = DB::table('inventaris')->where('id', $id);
        $del->delete();

        return redirect()->route('inventaris.index')
            ->with('success', 'Post deleted successfully');
    }

    public function lihat($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $foto = DB::table('inventaris_foto')->where('id_inventaris', $id)->get();
        return view('inventaris.foto', compact('id','foto'));
    }

    public function fotoupload(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $file = $request->file('foto');
        $tujuan_upload = 'files/inventaris/';

        $nama = $file->getClientOriginalName();
        $file->move($tujuan_upload, $nama);

        DB::table('inventaris_foto')->insert(['file' => $nama, 'id_inventaris' => $id, 'deskripsi' => $request->deskripsi]);
        return back();
    }
}

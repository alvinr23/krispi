<?php

namespace App\Http\Controllers;

use App\Models\PerbaikanAsset;
use Illuminate\Http\Request;
use DB;
use Auth;

class PerbaikanAssetController extends Controller
{
    public function index()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('perbaikan_asset as s')
        ->leftjoin('pegawai as p', 's.id_pegawai', 'p.id')
        ->leftjoin('kendaraan as k', 's.id_kendaraan', 'k.id')
        ->select("s.*","k.nama as kendaraan","p.nama as pemilik")
        ->get();
        

        return view('perbaikan-asset.index', compact('data'));
    }

    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $nama = [
            'nama'=> $request->nama
        ];
        $pegawai = DB::table('kendaraan_pegawai')->where('id_kendaraan', $request->id_kendaraan)->first();
        $perbaikan_asset = [
            'id_kendaraan' => $request->id_kendaraan,
            'id_pegawai' => $pegawai->id_pegawai,
            'kebutuhan_sekarang' => $request->kebutuhan_sekarang,
            'kebutuhan_selanjutnya' => $request->kebutuhan_selanjutnya,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan,
        ];
        DB::table('perbaikan_asset')->insert($perbaikan_asset);
        $id_now = DB::table('perbaikan_asset')->latest('id')->first();
        $now = [
            'id_barang' => $request->kebutuhan_sekarang,
            'id_perbaikan_asset' => $id_now->id
        ];
        return redirect()->back()->with('success', 'Post updated successfully');
    }

    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table('barang')->get();
        return view('perbaikan-asset.create', compact('barang'));
    }

    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table('barang')->get();
        $perbaikan_asset = DB::table("perbaikan_asset")->where('id',$id)->first();
        // $now = DB::table('kebutuhan_sekarang')->first();
        // $next = DB::table('kebutuhan_berikutnya')->get();
        $nama = DB::table("users")->get();
        return view('perbaikan-asset.edit', compact('perbaikan_asset','nama','barang', /** 'now', 'next' */));
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
        // $sekarang = DB::table('barang_kategori')->where('id', $request->kebutuhan_sekarang)->first();
        // $selanjutnya = DB::table('barang_kategori')->where('id', $request->kebutuhan_selanjutnya)->first();
        $nama = [
            'nama'=> $request->nama
        ];
        $pegawai = DB::table('kendaraan_pegawai')->where('id_kendaraan', $request->id_kendaraan)->first();
        $perbaikan_asset = [
            'id_kendaraan' => $request->id_kendaraan,
            'id_pegawai' => $pegawai->id_pegawai,
            'kebutuhan_sekarang' => $request->kebutuhan_sekarang,
            'kebutuhan_selanjutnya' => $request->kebutuhan_selanjutnya,
            'tanggal' => $request->tanggal,
            'keterangan' => $request->keterangan
        ];
        DB::table('perbaikan_asset')->where('id', $id)->update($perbaikan_asset);
        return redirect()->back()->with('success', 'Post updated successfully');
    }

    public function destroy($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $perbaikan_asset = DB::table('perbaikan_asset')->where('id', $id);
        $perbaikan_asset->delete();

        return redirect()->route('perbaikan-asset.index')
            ->with('success','Post deleted successfully');
    }


    public function lihat($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $foto = DB::table('perbaikan_asset_foto')->where('id_perbaikan_asset', $id)->get();
        return view('perbaikan-asset.foto', compact('id','foto'));
    }

    public function fotouploadkendaraan(Request $request, $id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $file = $request->file('foto');
        $tujuan_upload = 'files/perbaikan-asset/';

        $nama = $file->getClientOriginalName();
        $file->move($tujuan_upload, $nama);

        DB::table('perbaikan_asset_foto')->insert(['file' => $nama, 'id_perbaikan_asset' => $id, 'deskripsi' => $request->deskripsi]);
        return back();
    }
}
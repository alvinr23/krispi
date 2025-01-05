<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use Illuminate\Http\Request;
use DB;
use Auth;

class GudangController extends Controller
{

    public function stok()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('barang as a')
                ->leftjoin('barang_kategori as b','a.id_kategori','b.id')
                ->select('a.*','b.nama as kategori',
                    DB::RAW(" ( (select sum(jumlah) from transaksi_barang_masuk_detail as z where z.id_barang = a.id)-
                        case when (select sum(jumlah) from transaksi_barang_keluar_detail as z where z.id_barang = a.id) !=0 then (select sum(jumlah) from transaksi_barang_keluar_detail as z where z.id_barang = a.id) else
                        0 end  ) as stock ")
            )
                ->get();

        return view('gudang.stock', compact('data'));
    }

    public function barangMasukData(){
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table('barang')->get();
        $data = DB::table('transaksi_barang_masuk as a') 
                ->select('a.*',DB::RAW('(select count(*) from transaksi_barang_masuk_detail as z where z.id_transaksi=a.id) as jumlah'))
                ->get();
        return view('gudang.masuk-data',compact('data','barang'));
    }
    public function barangMasukSimpan(Request $request){
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = $request->barang;
        if (!empty($barang)) {
            $count = DB::table('transaksi_barang_masuk')->where('tanggal',date('Y-m-d'))->count();
            $kode = "BM".date('Ymd').($count+1);
            DB::table('transaksi_barang_masuk')->insert(['no_faktur'=>$request->no_faktur,'tanggal'=>$request->tanggal,'kode'=>$kode]);
            $val = DB::table('transaksi_barang_masuk')->limit(1)->orderby('id','desc')->first();
        }else{
            return back();
        }
        for ($i=0; $i < count($barang); $i++) { 
            DB::table('transaksi_barang_masuk_detail')->insert(['id_barang'=>$barang[$i],'jumlah'=>$request->jumlah[$i],'id_transaksi'=>$val->id]);
        }
        return back();
    }



    public function barangKeluarData(){
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = DB::table('barang')->get();
        $kendaraan = DB::table('kendaraan')->get();
        $data = DB::table('transaksi_barang_keluar as a') 
        ->leftjoin('kendaraan as b','b.id','a.id_kendaraan')
                ->select('a.*',DB::RAW('(select count(*) from transaksi_barang_keluar_detail as z where z.id_transaksi=a.id) as jumlah'),DB::RAW("concat(b.no_plat,' - ','b.nama') as kendaraan"))
                ->get();
        return view('gudang.keluar-data',compact('data','barang','kendaraan'));
    }
    public function barangKeluarSimpan(Request $request){
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $barang = $request->barang;
        if (!empty($barang)) {
            $count = DB::table('transaksi_barang_keluar')->where('tanggal',date('Y-m-d'))->count();
            $kode = "BM".date('Ymd').($count+1);
            DB::table('transaksi_barang_keluar')->insert(['id_kendaraan'=>$request->id_kendaraan,'tanggal'=>$request->tanggal,'kode'=>$kode]);
            $val = DB::table('transaksi_barang_keluar')->limit(1)->orderby('id','desc')->first();
        }else{
            return back();
        }
        for ($i=0; $i < count($barang); $i++) { 
            DB::table('transaksi_barang_keluar_detail')->insert(['id_barang'=>$barang[$i],'jumlah'=>$request->jumlah[$i],'id_transaksi'=>$val->id]);
        }
        return back();
    }
}
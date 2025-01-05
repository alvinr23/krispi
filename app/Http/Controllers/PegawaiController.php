<?php

namespace App\Http\Controllers;

use App\Models\Pegawai;
use Illuminate\Http\Request;
use DB;
use Hash;
use Auth;
use App\Models\User;
use Exception;

class PegawaiController extends Controller
{
    public function index()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('pegawai')->get();
        return view('pegawai.index', compact('data'));
    }

    public function kendaraan($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('kendaraan_pegawai as a')
            ->leftjoin('kendaraan as b', 'b.id', 'a.id_kendaraan')
            ->leftjoin('pegawai as c', 'c.id', 'a.id_pegawai')
            ->leftjoin('kendaraan_jenis as d', 'b.id_jenis', 'd.id')
            ->where('a.id_pegawai', $id)
            ->select('b.*', 'a.id as id_kendaraan_pegawai', 'd.nama as jenis', 'a.id as id_kendaraanPegawai', 'a.status')
            ->get();
        return view('pegawai.kendaraan', compact('data', 'id'));
    }

    public function kendaraanTambah($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $id = $id;
        $kendaraan = DB::table('kendaraan')->get();
        return view('pegawai.kendaraan-tambah', compact('id', 'kendaraan'));
    }

    public function kendaraanSimpan(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        DB::table('kendaraan_pegawai')->insert([
            'id_pegawai' => $request->id_pegawai,
            'id_kendaraan' => $request->id_kendaraan,
        ]);
        return back();
    }

    public function kendaraanHapus($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        DB::table('kendaraan_pegawai')->where('id', $id)->delete();
        return back();
    }

    public function kendaraanStatus($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $check = DB::table('kendaraan_pegawai')->where('id', $id)->first();
        $st = $check->status == 1 ? 0 : 1;
        DB::table('kendaraan_pegawai')->where('id', $id)->update(['status' => $st]);
    }

    public function kendaraanServis($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $data = DB::table('servis as a')
            ->leftjoin('kendaraans as b', 'b.id', 'a.id_kendaraan')
            ->leftjoin('users as c', 'c.id', 'a.id_pegawai')
            ->where('a.id_kendaraan', $id)
            ->get();
        return view('pegawai.kendaraan-servis', compact('data', 'id'));
    }

    public function exportWord($id)
    {
        $nama = DB::table('pegawai as a')
            ->leftjoin('kendaraan_pegawai as b', 'b.id_pegawai', '=', 'a.id')
            ->leftjoin('jabatan as c', 'c.id', '=', 'a.id_jabatan')
            ->where('b.id', $id)
            ->select('a.*', 'c.nama as jabatan')
            ->get();

        $kendaraan = DB::table('kendaraan_pegawai as a')
            ->leftjoin('kendaraan as b', 'b.id', '=', 'a.id_kendaraan')
            ->leftjoin('kendaraan_jenis as c', 'c.id', '=', 'b.id_jenis')
            ->where('a.id', $id)
            ->select('b.nama as nama', 'c.id as id_jenis', 'b.no_plat as plat', 'b.no_mesin as mesin', 'b.no_rangka as rangka', 'b.nama as kendaraan')
            ->get();

        
        
        $file = public_path('SuratPernyataan.docx');
        $word = new \PhpOffice\PhpWord\TemplateProcessor($file);


        foreach ($nama as $n) {
        $word->setValue('nama', $n->nama);
        $word->setValue('nip', $n->nip);
        $word->setValue('jabatan', $n->jabatan);}
        foreach ($kendaraan as $k) {
         if($k->id_jenis == 1 )
        $word->setValue('kendaraan', 'Motor');
        else if ($k->id_jenis == 3)
        $word->setValue('kendaraan', 'Mobil');
        $word->setValue('jenis_kendaraan', $k->nama);
        $word->setValue('plat_nomor', $k->plat); }

        

        $word->saveAs('hasilPersyaratan.docx');
       

        $download = public_path('hasilPersyaratan.docx');
        return response()->download($download);
      

    }

    public function exportWord2($id)
    {
        $nama = DB::table('pegawai as a')
            ->leftjoin('kendaraan_pegawai as b', 'b.id_pegawai', '=', 'a.id')
            ->leftjoin('jabatan as c', 'c.id', '=', 'a.id_jabatan')
            ->where('b.id', $id)
            ->select('a.*', 'c.nama as jabatan')
            ->get();

        $kendaraan = DB::table('kendaraan_pegawai as a')
            ->leftjoin('kendaraan as b', 'b.id', '=', 'a.id_kendaraan')
            ->leftjoin('kendaraan_jenis as c', 'c.id', '=', 'b.id_jenis')
            ->where('a.id', $id)
            ->select('b.nama as nama', 'c.id as id_jenis', 'b.no_plat as plat', 'b.no_mesin as mesin', 'b.no_rangka as rangka', 'b.nama as kendaraan')
            ->get();

        
        
      

        $file2 = public_path('BeritaAcara.docx');
        $word2 = new \PhpOffice\PhpWord\TemplateProcessor($file2);

     
        foreach ($nama as $n) {
            $word2->setValue('nama', $n->nama);
            $word2->setValue('nip', $n->nip);
            $word2->setValue('jabatan', $n->jabatan);}
            foreach ($kendaraan as $k) {
            $word2->setValue('kendaraan', $k->kendaraan);
            $word2->setValue('mesin', $k->mesin);
            $word2->setValue('rangka', $k->rangka);
            $word2->setValue('plat', $k->plat); }

     
        $word2->saveAs('hasilBeritaAcara.docx');

     
        $download2 = public_path('hasilBeritaAcara.docx');
        return response()->download($download2);

    }


    // public function servisTambah($id)
    // {

    //     $val = DB::table('kendaraan_pegawai as a')
    //         ->leftjoin('kendaraans as b', 'b.id', 'a.id_kendaraan')
    //         ->where('a.id', $id)
    //         ->first();
    //     return view('pegawai.servis-tambah', compact('id', 'val'));
    // }

    // public function servisSimpan(Request $request, $id)
    // {
    //     DB::table('servis')->insert([
    //         'id_kendaraan' => $id,
    //         'id_pegawai' => $request->id_pegawai,
    //         'kebutuhan_sekarang' => $request->kebutuhan_sekarang,
    //         'kebutuhan_selanjutnya' => $request->kebutuhan_selanjutnya,
    //         'tanggal' => $request->tanggal,
    //         'keterangan' => $request->keterangan
    //     ]);

    //     return back();
    // }


    public function create()
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $jabatan = DB::table('jabatan')->get();
        return view('pegawai.create', compact('jabatan'));
    }


    public function edit($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $pegawai = DB::table("pegawai")->where('id', $id)->first();
        $jabatan = DB::table('jabatan')->get();
        return view('pegawai.edit', compact('pegawai', 'jabatan'));
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
        $pegawai = DB::table('pegawai')->where('id', $id)->first();
        $pass = $pegawai->password;
        $data = [
            'username' => $request->username,
            'password' => $pass,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'id_jabatan' => $request->id_jabatan,
            'email' => $request->email,
        ];
        if ($request->password != null) {
            $data['password'] = bcrypt($request->password);
        }

        DB::table('pegawai')->where('id', $id)->update($data);
        return redirect()->route('pegawai.index')->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $pegawai = DB::table("pegawai")->where('id', $id)->delete();

        return redirect()->route('pegawai.index')
            ->with('success', 'Post deleted successfully');
    }

    public function store(Request $request)
    {
        if (Auth::user() == '') {
            return view('auth.login');
            exit();
        }
        $file = $request->file('foto');
        $tujuan_upload = 'files/pegawai/';
        $nama = $file->getClientOriginalName();
        $file->move($tujuan_upload, $nama);

        $pegawai = [
            'username' => $request->username,
            'password' => $request->password,
            'nip' => $request->nip,
            'nama' => $request->nama,
            'alamat' => $request->alamat,
            'no_hp' => $request->no_hp,
            'id_jabatan' => $request->id_jabatan,
            'email' => $request->email,
            'foto' => $nama,

        ];
        $pegawai['password'] = bcrypt($request->password);
        DB::table('pegawai')->insert($pegawai);
        $user = DB::table('pegawai')->where('nip', $request->nip)->first();

        $data = [
            'username' => $request->username,
            'password' => bcrypt($request->password),
            'name' => $request->nama,
            'email' => $request->email,
            'id_pegawai' => $user->id

        ];

        User::create($data);
        return redirect()->back()->with('success', 'Post updated successfully');
    }
}

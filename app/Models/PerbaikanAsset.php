<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerbaikanAsset extends Model
{
    use HasFactory;
    protected $table = 'perbaikan_asset';
    protected $fillable = [
        'id_kendaraan','id_pegawai','kebutuhan_sekarang','kebutuhan_selanjutnya','tanggal','keterangan'
    ];
    public function perbaikanAsset(){
        return $this->belongsTo(User::class);
    }
}

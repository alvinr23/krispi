<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pegawai extends Model
{
    use HasFactory;
    protected $fillable = [
        'username',
        'password',
        'nip',
        'nama',
        'alamat',
        'no_hp',
        'id_jabatan',
        'email',
        'foto'
    ];
    public function pegawai()
    {
        return $this->belongsTo(User::class);
    }
}

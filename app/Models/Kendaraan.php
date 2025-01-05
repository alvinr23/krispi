<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kendaraan extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_jenis', 'no_rangka', 'no_plat', 'no_mesin', 'warna'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

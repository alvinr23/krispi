<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;
    protected $fillable = [
        'kode','nama','deskripsi'
    ];
    public function barang(){
        return $this->belongsTo(User::class);
    }
    public function kategori(){
        return $this->hasMany(Kategori::class);
    }
}

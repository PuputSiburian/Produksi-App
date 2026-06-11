<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class produksi extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'kode_produk',
        'nama_produk',
        'part_number',
        'stasiun',
        'target_standar',
        'deskripsi',
        'status'
    ];
}
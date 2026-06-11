<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiLine extends Model
{
    use HasFactory, Auditable;

    protected $table = 'produksi_line';
    
    protected $fillable = [
        'tanggal',
        'proses',
        'nama_line',
        'nama_operator',
        'produk',
        'part_number',
        'lot_produk',
        'target',
        'qty',
        'reject',
        'downtime',
        'keterangan',
        'user_id'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
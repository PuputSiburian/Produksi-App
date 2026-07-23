<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiCrimping extends Model
{
    use HasFactory, Auditable;

    protected $table = 'produksi_crimpings';
    
    protected $fillable = [
        'tanggal',
        'line_crimping',
        'nama_operator',
        'produk',
        'part_number',
        'lot_produk',
        'warna',
        'target',
        'qty',
        'reject',
        'keterangan',
        'user_id',
        'leader_name'   // 🔥 HANYA leader_name, SHIFT DIHAPUS
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
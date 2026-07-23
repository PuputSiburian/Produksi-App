<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProduksiCutting extends Model
{
    use HasFactory, Auditable;

    protected $table = 'produksi_cuttings';
    
    protected $fillable = [
        'tanggal',
        'line_cutting',
        'nama_operator',
        'proses',
        'produk',
        'lot_produk',
        'part_number',
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
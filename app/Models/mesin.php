<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mesin extends Model
{
    use HasFactory;

    protected $table = 'mesins';
    
    protected $fillable = [
        'kode_mesin',
        'nama_mesin',
        'jenis_mesin',
        'lokasi',
        'status',
        'gangguan',
        'tanggal_gangguan',
        'teknisi',
        'durasi_gangguan',
        'prioritas',
        'keterangan',
        'user_id'
    ];

    protected $casts = [
        'tanggal_gangguan' => 'date',
        'durasi_gangguan' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scope untuk status
    public function scopeBeroperasi($query)
    {
        return $query->where('status', 'Beroperasi');
    }

    public function scopeBermasalah($query)
    {
        return $query->whereIn('status', ['Perbaikan', 'Rusak']);
    }

    // Accessor untuk badge status
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'Beroperasi' => '<span class="badge bg-success">✓ Beroperasi</span>',
            'Perbaikan' => '<span class="badge bg-warning text-dark">🔧 Perbaikan</span>',
            'Rusak' => '<span class="badge bg-danger">❌ Rusak</span>',
            'Maintenance' => '<span class="badge bg-info">⚙ Maintenance</span>',
            'Idle' => '<span class="badge bg-secondary">⏸ Idle</span>',
            default => '<span class="badge bg-secondary">' . $this->status . '</span>',
        };
    }

    // Accessor untuk badge prioritas
    public function getPrioritasBadgeAttribute()
    {
        return match($this->prioritas) {
            'Rendah' => '<span class="badge bg-secondary">🟢 Rendah</span>',
            'Sedang' => '<span class="badge bg-primary">🔵 Sedang</span>',
            'Tinggi' => '<span class="badge bg-warning text-dark">🟡 Tinggi</span>',
            'Darurat' => '<span class="badge bg-danger">🔴 Darurat</span>',
            default => '<span class="badge bg-secondary">' . $this->prioritas . '</span>',
        };
    }
}
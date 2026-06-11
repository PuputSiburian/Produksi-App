<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $table = 'activity_logs';
    protected $primaryKey = 'id';
    
    protected $fillable = [
        'table_name',
        'record_id',
        'user_id',
        'user_name',
        'action',
        'old_data',
        'new_data',
        'ip_address'
    ];
    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
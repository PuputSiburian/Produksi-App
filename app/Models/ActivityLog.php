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

    /**
     * Get formatted old data
     */
    public function getOldDataFormattedAttribute()
    {
        if ($this->old_data) {
            return json_decode($this->old_data, true);
        }
        return null;
    }

    /**
     * Get formatted new data
     */
    public function getNewDataFormattedAttribute()
    {
        if ($this->new_data) {
            return json_decode($this->new_data, true);
        }
        return null;
    }

    /**
     * Get action badge color
     */
    public function getActionBadgeAttribute()
    {
        return match($this->action) {
            'CREATE' => 'success',
            'UPDATE' => 'warning',
            'DELETE' => 'danger',
            default => 'secondary'
        };
    }

    /**
     * Get action icon
     */
    public function getActionIconAttribute()
    {
        return match($this->action) {
            'CREATE' => 'fa-plus-circle',
            'UPDATE' => 'fa-edit',
            'DELETE' => 'fa-trash-alt',
            default => 'fa-info-circle'
        };
    }
}
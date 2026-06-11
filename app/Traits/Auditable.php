<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait Auditable
{
    /**
     * Mencatat aktivitas ke dalam log
     */
    public function logActivity($action, $oldData = null, $newData = null)
    {
        ActivityLog::create([
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name ?? 'Unknown',
            'action' => $action,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip_address' => request()->ip(),
        ]);
    }
    
    /**
     * Relasi ke tabel activity_logs
     */
    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'record_id', 'id')
                    ->where('table_name', $this->getTable());
    }
}
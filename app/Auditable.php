<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    public function logActivity($action, $oldData = null, $newData = null)
    {
        ActivityLog::create([
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'action' => $action,
            'old_data' => $oldData,
            'new_data' => $newData,
            'user_id' => auth()->id(),
            'user_name' => auth()->user()->name,
            'ip_address' => Request::ip(),
        ]);
    }
    
    public function activities()
    {
        return $this->hasMany(ActivityLog::class, 'record_id')
            ->where('table_name', $this->getTable())
            ->orderBy('created_at', 'desc');
    }
}
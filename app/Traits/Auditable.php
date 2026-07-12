<?php

namespace App\Traits;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Boot the trait - otomatis mencatat saat create, update, delete
     */
    protected static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logActivity('CREATE', null, $model->toArray());
        });

        static::updated(function ($model) {
            $oldData = $model->getOriginal();
            $newData = $model->toArray();
            $model->logActivity('UPDATE', $oldData, $newData);
        });

        static::deleted(function ($model) {
            $model->logActivity('DELETE', $model->toArray(), null);
        });
    }

    /**
     * Mencatat aktivitas ke dalam log
     */
    public function logActivity($action, $oldData = null, $newData = null)
    {
        // 🔥 CEK USER
        $userId = auth()->id();
        $userName = auth()->user() ? auth()->user()->name : 'System';

        // 🔥 JIKA TIDAK LOGIN, GUNAKAN USER_ID = 1 (ADMIN)
        if (!$userId) {
            $userId = 1;
            $userName = 'System';
        }

        ActivityLog::create([
            'user_id' => $userId,
            'table_name' => $this->getTable(),
            'record_id' => $this->id,
            'action' => $action,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'user_name' => $userName,
            'ip_address' => Request::ip(),
        ]);
    }

    /**
     * Get all activities for this model
     */
    public function activities()
    {
        return ActivityLog::where('table_name', $this->getTable())
                          ->where('record_id', $this->id)
                          ->orderBy('created_at', 'desc');
    }
}
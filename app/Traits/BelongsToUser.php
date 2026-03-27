<?php

namespace App\Traits;

use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivityNotificationMail;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;



trait BelongsToUser
{
    protected static function bootBelongsToUser()
    {
        static::creating(function ($model) {
            if (auth()->check()) {
                $user = auth()->user();
                if (!$model->user_id) {
                    $model->user_id = $user->id;
                }
                if ($user->firm_id && !$model->firm_id) {
                    $model->firm_id = $user->firm_id;
                }
            }
        });

        // ✅ Add safety check for global scope
        static::addGlobalScope('firm', function ($builder) {
            if (auth()->check()) {
                $user = auth()->user();

                // Super Admin sees all data
                if ($user->isSuperAdmin()) {
                    return;
                }

                // ✅ Check if firm_id exists before applying scope
                if ($user->firm_id && in_array($user->role, ['firm_admin', 'employee'])) {
                    $table = $builder->getModel()->getTable();
                    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'firm_id')) {
                        $builder->where("{$table}.firm_id", $user->firm_id);
                    }
                }
                // ✅ Fallback to user_id
                elseif (auth()->id()) {
                    $table = $builder->getModel()->getTable();
                    if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'user_id')) {
                        $builder->where("{$table}.user_id", auth()->id());
                    }
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    // ✅ NEW: Firm relationship for models with firm_id
    public function firm()
    {
        return $this->belongsTo(\App\Models\Firm::class);
    }
}

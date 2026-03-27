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
        // Auto-assign user_id et firm_id à la création
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

        // ✅ Global Scope OPTIMISÉ (sans Schema::hasColumn à chaque requête)
        static::addGlobalScope('firm', function ($builder) {
            if (!auth()->check()) {
                return;
            }

            $user = auth()->user();

            // Super Admin voit tout
            if ($user->isSuperAdmin()) {
                return;
            }

            // Employer/Firm Admin : scope par firm_id
            if ($user->firm_id && in_array($user->role, ['firm_admin', 'employee'])) {
                $builder->where('firm_id', $user->firm_id);
            }
            // Fallback : scope par user_id
            elseif (auth()->id()) {
                $builder->where('user_id', auth()->id());
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

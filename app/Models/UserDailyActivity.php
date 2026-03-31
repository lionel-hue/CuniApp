<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDailyActivity extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'hits',
    ];

    protected $casts = [
        'date' => 'date',
        'hits' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $fillable=[
        'user_id',
        'message'
    ];
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

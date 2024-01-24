<?php

namespace App\Models\English;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingRequest extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getResponseAttribute()
    {
        return json_decode($this->attributes['response'], true);
    }
}

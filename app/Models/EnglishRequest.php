<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int    $id
 * @property string $type
 * @property string $skill
 * @property int    $user_id
 * @property string $hash
 * @property string $response
 * @property string $pdf_path
 * @property Carbon $completed_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
class EnglishRequest extends Model
{
    protected $table = 'english_requests';

    protected $fillable = [
        'type',
        'skill',
        'user_id',
        'hash',
        'response',
        'extra_data',
        'pdf_path',
        'completed_at',
    ];

    protected $casts = [
        'response' => 'array',
        'extra_data' => 'array',
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

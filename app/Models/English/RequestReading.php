<?php

namespace App\Models\English;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RequestReading extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reading_requests';
    protected $fillable = [
        'id',
        'user_id',
        'youtube_url',
        'hash',
        'response',
        'export_pdf',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    const DISPLAY_NAME = 'Listening';

}

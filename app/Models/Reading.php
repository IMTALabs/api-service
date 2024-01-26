<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reading extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'reading_requests';
    protected $fillable = ['id', 'user_id', 'topic', 'is_topic', 'hash', 'response', 'paragraph', 'created_at', 'updated_at', 'deleted_at'];
    CONST DISPLAY_NAME='Reading';
}

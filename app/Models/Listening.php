<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Listening extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'listening_request';
    protected $fillable = ['id', 'user_id', 'youtube_url',
    'hash', 'response', 'created_at', 'updated_at', 'deleted_at'];

    CONST DISPLAY_NAME='Listening';
}

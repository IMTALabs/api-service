<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Writing extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'writing_request';
    protected $fillable = ['id', 'user_id', 'topic', 'hash', 'response', 'created_at', 'updated_at', 'deleted_at'];
    CONST DISPLAY_NAME='Writing';
}

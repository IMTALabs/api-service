<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListenMark extends Model
{
    use HasFactory,SoftDeletes;
    protected $table='english_submittions';
    protected $fillable=['id','user_id','skill','request_id','mark','score','created_at','updated_at','deleted_at'];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class InstagramPost extends Model
{
    protected $fillable = ['post_url', 'thumbnail_image', 'caption', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
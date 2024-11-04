<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeComment extends Model
{
    protected $fillable = [
        'youtube_video_id',
        'video_id',
        'comment_id',
        'title',
        'like_count',
        'dislike_count',
        'create_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

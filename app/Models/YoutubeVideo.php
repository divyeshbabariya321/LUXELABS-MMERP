<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class YoutubeVideo extends Model
{
    protected $fillable = [
        'youtube_channel_id',
        'channel_id',
        'link',
        'media_id',
        'title',
        'description',
        'view_count',
        'like_count',
        'dislike_count',
        'comment_count',
        'create_time',
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}

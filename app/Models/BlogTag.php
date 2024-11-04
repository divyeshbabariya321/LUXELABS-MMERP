<?php

namespace App\Models;
use App\Tag;
use App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class BlogTag extends Model
{
    public $fillable = [
        'blog_id',
        'type',
        'tag_id',
        'created_at',
        'updated_at',
    ];

    public function tag(): BelongsTo
    {
        return $this->belongsTo(Tag::class);
    }
}

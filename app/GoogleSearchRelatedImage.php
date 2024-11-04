<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class GoogleSearchRelatedImage extends Model
{
    protected $fillable = [
        'google_search_image_id', 'google_image', 'image_url',
    ];

    public function googleSearchImage(): BelongsTo
    {
        return $this->belongsTo(GoogleSearchImage::class);
    }
}

<?php

namespace App;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class GoogleSearchImage extends Model
{
    protected $fillable = [
        'user_id', 'product_id', 'crop_image',
    ];

    public function googleSearchRelatedImages(): HasMany
    {
        return $this->hasMany(GoogleSearchRelatedImage::class);
    }
}

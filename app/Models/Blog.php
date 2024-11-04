<?php

namespace App\Models;
use App\User;
use App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    public $fillable = [
        'blog_id',
        'user_id',
        'idea',
        'keyword',
        'content',
        'plaglarism',
        'internal_link',
        'header_tag',
        'title_tag',
        'store_website_id',
        'strong_tag',
        'italic_tag',
        'external_link',
        'meta_desc',
        'url_structure',
        'url_xml',
        'publish_blog_date',
        'no_index',
        'no_follow',
        'canonical_url',
        'checkmobile_friendliness',
        'date',
        'facebook',
        'facebook_date',
        'instagram',
        'instagram_date',
        'twitter',
        'twitter_date',
        'google',
        'google_date',
        'bing',
        'bing_date',
        'created_at',
        'updated_at',
    ];

    public function blogsTag(): HasMany
    {
        return $this->hasMany(BlogTag::class);
    }

    public function user(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

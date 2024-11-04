<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\StoreWebsite;

class StoreWebsiteBuilderApiKeyHistory extends Model
{
    use HasFactory;

    protected $fillable = ['store_website_id', 'old', 'new', 'updated_by'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function storeWebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class);
    }
}

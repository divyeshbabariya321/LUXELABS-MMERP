<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteStatusHistory extends Model
{
    use HasFactory;

    protected $table = 'store_website_page_status_histories';

    public function newstatus(): BelongsTo
    {
        return $this->belongsTo(StoreWebsiteStatus::class, 'new_status_id');
    }

    public function oldstatus(): BelongsTo
    {
        return $this->belongsTo(StoreWebsiteStatus::class, 'old_status_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

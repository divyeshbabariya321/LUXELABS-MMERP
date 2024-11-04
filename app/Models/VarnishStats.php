<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VarnishStats extends Model
{
    use HasFactory;

    protected $fillable = ['timestamp', 'server_name', 'server_ip', 'website_name', 'cache_name', 'cache_hit', 'cache_miss', 'cache_hitpass', 'cache_hitrate', 'cache_missrate'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }

    public function storewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }
}

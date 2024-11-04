<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\User;
use App\StoreWebsite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreWebsiteAdminUrl extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'store_website_id',
        'website_url',
        'admin_url',
        'store_dir',
        'server_ip_address',
        'request_data',
        'response_data',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->select('name', 'id');
    }

    public function storewebsite(): BelongsTo
    {
        return $this->belongsTo(StoreWebsite::class, 'store_website_id')->select('title', 'id');
    }
}
